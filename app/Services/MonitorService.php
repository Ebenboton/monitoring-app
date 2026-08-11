<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Check;
use App\Models\Incident;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class MonitorService
{
    public function checkApplication(Application $app): Check
    {
        if ($app->isUnderMaintenance()) {
            $app->update(['current_status' => 'MAINTENANCE']);
            return $this->saveCheck($app, 'UP', null, null, null, null, null, null, 'Maintenance active');
        }

        $result = $this->performCheck($app);
        $this->updateApplicationStatus($app, $result);
        $incident = $this->handleIncident($app, $result);

        // Envoie les alertes
        $alertService = new AlertService();

        if (in_array($result->status, ['DOWN', 'ERROR']) && $incident) {
            $alertService->handleAlert($app, $result, $incident);
        } elseif ($result->status === 'SLOW') {
            $alertService->handleAlert($app, $result, null);
        }

        return $result;
    }


    private function performCheck(Application $app): Check
    {
        $startTime = microtime(true);

        try {
            // Niveau 1 — TCP (vérifié implicitement par HTTP)
            // Niveau 2 & 3 — HTTP + latence
            $response = Http::timeout($app->timeout_ms / 1000)
                ->withHeaders($app->headers ?? [])
                ->send($app->method, $app->url);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            $httpCode     = $response->status();
            $body         = $response->body();

            // Vérifie les codes HTTP acceptés
            $acceptedCodes = array_map('trim', explode(',', $app->accepted_codes));
            if (!in_array((string) $httpCode, $acceptedCodes)) {
                return $this->saveCheck(
                    $app,
                    'DOWN',
                    $httpCode,
                    $responseTime,
                    false,
                    null,
                    null,
                    null,
                    "Code HTTP {$httpCode} non accepté"
                );
            }

            // Niveau 3 — Latence
            $status = 'UP';
            if ($responseTime >= $app->latency_down_ms) {
                $status = 'DOWN';
            } elseif ($responseTime >= $app->latency_warn_ms) {
                $status = 'SLOW';
            }

            // Niveau 4 — Keyword check
            $keywordOk = true;
            $keywordError = null;

            if ($app->keyword_expected) {
                // Découpe par virgule et vérifie chaque mot-clé indépendamment
                $expectedKeywords = array_map('trim', explode(',', $app->keyword_expected));
                foreach ($expectedKeywords as $keyword) {
                    if (!empty($keyword) && !str_contains($body, $keyword)) {
                        $keywordOk = false;
                        $status = 'DOWN';
                        $keywordError = "Mot-clé attendu absent : {$keyword}";
                        break;
                    }
                }
            }

            if ($app->keyword_forbidden) {
                // Découpe par virgule et vérifie chaque mot-clé interdit indépendamment
                $forbiddenKeywords = array_map('trim', explode(',', $app->keyword_forbidden));
                foreach ($forbiddenKeywords as $keyword) {
                    if (!empty($keyword) && str_contains($body, $keyword)) {
                        $keywordOk = false;
                        $status = 'DOWN';
                        $keywordError = "Mot-clé interdit détecté : {$keyword}";
                        break;
                    }
                }
            }


            // Niveau 5 — SSL
            $sslDaysRemaining = null;
            $sslValid         = null;

            if ($app->ssl_check && str_starts_with($app->url, 'https://')) {
                [$sslValid, $sslDaysRemaining] = $this->checkSsl($app->url);
                if (!$sslValid || $sslDaysRemaining < 7) {
                    $status = 'DOWN';
                } elseif ($sslDaysRemaining < $app->ssl_alert_days) {
                    $status = $status === 'UP' ? 'SLOW' : $status;
                }
            }

            // Niveau 6 — Auth check
            $authOk = null;
            if ($app->auth_enabled) {
                $authOk = $this->checkAuth($app);
                if (!$authOk) {
                    $status = 'DOWN';
                }
            }

            $errorMessage = $keywordError;
            if ($authOk === false) {
                $errorMessage = ($errorMessage ? $errorMessage . ' | ' : '') . 'Échec authentification';
            }

            return $this->saveCheck(
                $app,
                $status,
                $httpCode,
                $responseTime,
                $keywordOk,
                $authOk,
                $sslDaysRemaining,
                $sslValid,
                $errorMessage
            );
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            return $this->saveCheck(
                $app,
                'DOWN',
                null,
                $responseTime,
                null,
                null,
                null,
                null,
                'Connexion impossible : ' . $e->getMessage()
            );
        } catch (\Exception $e) {
            return $this->saveCheck(
                $app,
                'ERROR',
                null,
                null,
                null,
                null,
                null,
                null,
                'Erreur inattendue : ' . $e->getMessage()
            );
        }
    }

    private function checkSsl(string $url): array
    {
        try {
            $host = parse_url($url, PHP_URL_HOST);
            $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
            $socket  = @stream_socket_client(
                "ssl://{$host}:443",
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) return [false, 0];

            $params = stream_context_get_params($socket);
            $cert   = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            fclose($socket);

            $expiry      = $cert['validTo_time_t'];
            $daysLeft    = (int) ceil(($expiry - time()) / 86400);

            return [$daysLeft > 0, $daysLeft];
        } catch (\Exception $e) {
            return [false, 0];
        }
    }

    private function checkAuth(Application $app): bool
    {
        try {
            $credential = $app->auth_credential ? Crypt::decryptString($app->auth_credential) : null;
            $password   = $app->auth_password   ? Crypt::decryptString($app->auth_password)   : null;

            switch ($app->auth_type) {
                case 'basic':
                    $response = Http::timeout(10)
                        ->withBasicAuth($credential, $password)
                        ->get($app->auth_url ?? $app->url);
                    return $response->successful();

                case 'bearer':
                    $response = Http::timeout(10)
                        ->withToken($credential)
                        ->get($app->auth_url ?? $app->url);
                    return $response->successful();

                case 'form_post':
                    $response = Http::timeout(10)
                        ->asForm()
                        ->post($app->auth_url ?? $app->url, [
                            'email'    => $credential,
                            'password' => $password,
                        ]);
                    $success = $response->successful() || $response->redirect();
                    if ($success && $app->auth_success_keyword) {
                        $success = str_contains($response->body(), $app->auth_success_keyword);
                    }
                    return $success;

                case 'cookie':
                    $response = Http::timeout(10)
                        ->withHeaders(['Cookie' => $credential])
                        ->get($app->auth_url ?? $app->url);
                    return $response->successful();
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    private function saveCheck(
        Application $app,
        string $status,
        ?int $httpCode,
        ?int $responseTime,
        ?bool $keywordOk,
        ?bool $authOk,
        ?int $sslDaysRemaining,
        ?bool $sslValid,
        ?string $errorMessage
    ): Check {
        return Check::create([
            'application_id'   => $app->id,
            'status'           => $status,
            'http_code'        => $httpCode,
            'response_time_ms' => $responseTime,
            'keyword_ok'       => $keywordOk,
            'auth_ok'          => $authOk,
            'ssl_days_remaining' => $sslDaysRemaining,
            'ssl_valid'        => $sslValid,
            'error_message'    => $errorMessage,
            'checked_at'       => now(),
        ]);
    }

    private function updateApplicationStatus(Application $app, Check $check): void
    {
        $app->update([
            'current_status'  => $check->status,
            'last_checked_at' => now(),
        ]);
    }

    private function handleIncident(Application $app, Check $check): ?Incident
    {
        $openIncident = $app->incidents()
            ->where('is_resolved', false)
            ->latest('started_at')
            ->first();

        if ($check->status === 'DOWN' || $check->status === 'ERROR') {
            if (!$openIncident) {
                $openIncident = Incident::create([
                    'application_id' => $app->id,
                    'started_at'     => now(),
                    'root_cause'     => $this->detectRootCause($check),
                    'is_resolved'    => false,
                ]);
            }
            return $openIncident;
        } else {
            if ($openIncident) {
                $openIncident->resolve();

                // Envoie l'email de rétablissement
                $alertService = new AlertService();
                $alertService->sendRecoveryAlert($app, $openIncident->fresh());
            }
            return null;
        }
    }
    
    private function detectRootCause(Check $check): string
    {
        if ($check->auth_ok === false)        return 'auth';
        if ($check->ssl_valid === false)       return 'ssl';
        if ($check->keyword_ok === false)      return 'keyword';
        if ($check->http_code === null)        return 'timeout';
        return 'http_error';
    }
}
