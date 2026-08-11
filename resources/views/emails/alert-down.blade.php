<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Panne</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: #dc2626; padding: 32px; text-align: center; }
        .header-icon { font-size: 48px; margin-bottom: 12px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .header p { color: #fecaca; font-size: 14px; }
        .body { padding: 32px; }
        .alert-badge { display: inline-block; background: #fee2e2; color: #dc2626; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; }
        .app-name { font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
        .app-url { font-size: 13px; color: #64748b; margin-bottom: 24px; word-break: break-all; }
        .metrics { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px; }
        .metrics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .metric-item { }
        .metric-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .metric-value { font-size: 16px; font-weight: 600; color: #1e293b; }
        .metric-value.red { color: #dc2626; }
        .metric-value.orange { color: #ea580c; }
        .error-box { background: #fff1f2; border: 1px solid #fecaca; border-left: 4px solid #dc2626; border-radius: 6px; padding: 14px 16px; margin-bottom: 24px; }
        .error-box p { font-size: 13px; color: #9f1239; line-height: 1.6; }
        .btn { display: inline-block; background: #dc2626; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64748b; }
        .info-value { color: #1e293b; font-weight: 500; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 32px; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .footer strong { color: #475569; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Header rouge --}}
    <div class="header">
        <div class="header-icon">🔴</div>
        <h1>Application en panne</h1>
        <p>Détectée le {{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>

    <div class="body">
        <span class="alert-badge">⚠ Panne critique</span>

        <div class="app-name">{{ $application->name }}</div>
        <div class="app-url">{{ $application->url }}</div>

        {{-- Métriques --}}
        <div class="metrics">
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-label">Code HTTP</div>
                    <div class="metric-value red">{{ $httpCode ?? 'Timeout' }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Temps de réponse</div>
                    <div class="metric-value red">{{ $responseTime ? $responseTime.'ms' : '—' }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Début de l'incident</div>
                    <div class="metric-value">{{ $incident->started_at->format('H:i:s') }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-label">Cause détectée</div>
                    <div class="metric-value orange">{{ ucfirst(str_replace('_', ' ', $incident->root_cause ?? 'Inconnue')) }}</div>
                </div>
            </div>
        </div>

        {{-- Message d'erreur --}}
        @if($errorMessage)
        <div class="error-box">
            <p><strong>Détail de l'erreur :</strong><br>{{ $errorMessage }}</p>
        </div>
        @endif

        {{-- Infos application --}}
        <div class="info-row">
            <span class="info-label">Groupe</span>
            <span class="info-value">{{ $application->group_name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Méthode</span>
            <span class="info-value">{{ $application->method }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Seuil DOWN configuré</span>
            <span class="info-value">{{ $application->latency_down_ms }}ms</span>
        </div>
        <div class="info-row">
            <span class="info-label">Retries effectués</span>
            <span class="info-value">{{ $application->retry_count }} tentatives</span>
        </div>
    </div>

    <div class="footer">
        <p><strong>M-Monitoring</strong> — Plateforme de surveillance applicative<br>
        Cet email a été envoyé automatiquement. Ne pas répondre.</p>
    </div>
</div>
</body>
</html>