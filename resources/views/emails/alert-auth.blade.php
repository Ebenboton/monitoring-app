<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Auth</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; }
        .header { background: #7c3aed; padding: 28px 32px; border-radius: 12px 12px 0 0; }
        .header-icon { font-size: 32px; margin-bottom: 8px; }
        .header h1 { color: #fff; font-size: 20px; font-weight: 700; }
        .header p { color: #ddd6fe; font-size: 13px; margin-top: 4px; }
        .body { background: #fff; padding: 32px; }
        .alert-badge { display: inline-block; background: #ede9fe; color: #7c3aed; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 20px; }
        .app-name { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .app-url { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .warning-box { background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #dc2626; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .warning-box p { font-size: 14px; color: #991b1b; font-weight: 500; }
        .warning-box span { font-size: 13px; color: #b91c1c; display: block; margin-top: 4px; }
        .metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .metric { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; }
        .metric-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .metric-value { font-size: 15px; font-weight: 600; color: #0f172a; }
        .metric-value.red { color: #dc2626; }
        .metric-value.purple { color: #7c3aed; }
        .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
        .info-box p { font-size: 13px; color: #166534; }
        .btn { display: inline-block; background: #7c3aed; color: #fff; font-size: 14px; font-weight: 600; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-bottom: 24px; }
        .footer { background: #f8fafc; padding: 20px 32px; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; }
        .footer strong { color: #64748b; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-icon">🔑</div>
        <h1>Échec d'authentification</h1>
        <p>Détecté le {{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>
    <div class="body">
        <span class="alert-badge">🔒 Auth Failed</span>
        <div class="app-name">{{ $application->name }}</div>
        <div class="app-url">{{ $application->url }}</div>

        <div class="warning-box">
            <p>⚠ L'application répond HTTP 200 mais l'authentification a échoué</p>
            <span>100% des utilisateurs sont potentiellement bloqués sans que le monitoring basique ne le détecte.</span>
        </div>

        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Statut HTTP</div>
                <div class="metric-value">200 OK</div>
            </div>
            <div class="metric">
                <div class="metric-label">Statut Auth</div>
                <div class="metric-value red">✗ ÉCHEC</div>
            </div>
            <div class="metric">
                <div class="metric-label">Type d'auth</div>
                <div class="metric-value purple">{{ strtoupper($application->auth_type ?? '—') }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Incident depuis</div>
                <div class="metric-value">{{ $incident->started_at->format('H:i:s') }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Groupe</div>
                <div class="metric-value">{{ $application->group_name ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">URL de login</div>
                <div class="metric-value" style="font-size:12px;word-break:break-all;">{{ $application->auth_url ?? $application->url }}</div>
            </div>
        </div>

        <div class="info-box">
            <p>💡 Vérifiez que le compte de monitoring est toujours actif et que son mot de passe n'a pas expiré.</p>
        </div>

        <a href="{{ url('/applications/'.$application->id) }}" class="btn">
            Voir les détails →
        </a>

        <p style="font-size:13px;color:#64748b;">
            Cet incident a été ouvert automatiquement. Vérifiez les credentials de monitoring
            dans la configuration de l'application.
        </p>
    </div>
    <div class="footer">
        <p><strong>M-Monitoring</strong> — Plateforme de surveillance applicative</p>
        <p style="margin-top:4px;">Cet email a été envoyé automatiquement. Ne pas répondre.</p>
    </div>
</div>
</body>
</html>