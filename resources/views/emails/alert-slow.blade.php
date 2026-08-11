<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Dégradation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; }
        .header { background: #ea580c; padding: 28px 32px; border-radius: 12px 12px 0 0; }
        .header-icon { font-size: 32px; margin-bottom: 8px; }
        .header h1 { color: #fff; font-size: 20px; font-weight: 700; }
        .header p { color: #fed7aa; font-size: 13px; margin-top: 4px; }
        .body { background: #fff; padding: 32px; }
        .alert-badge { display: inline-block; background: #ffedd5; color: #ea580c; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 20px; }
        .app-name { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .app-url { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .metric { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; }
        .metric-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .metric-value { font-size: 16px; font-weight: 600; color: #0f172a; }
        .metric-value.orange { color: #ea580c; }
        .latency-bar { background: #f1f5f9; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .latency-bar-label { font-size: 12px; color: #64748b; margin-bottom: 8px; }
        .bar-track { background: #e2e8f0; border-radius: 4px; height: 8px; }
        .bar-fill { background: #ea580c; border-radius: 4px; height: 8px; }
        .btn { display: inline-block; background: #ea580c; color: #fff; font-size: 14px; font-weight: 600; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-bottom: 24px; }
        .footer { background: #f8fafc; padding: 20px 32px; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; }
        .footer strong { color: #64748b; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-icon">🟡</div>
        <h1>Application dégradée</h1>
        <p>Détecté le {{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>
    <div class="body">
        <span class="alert-badge">⚡ Latence élevée</span>
        <div class="app-name">{{ $application->name }}</div>
        <div class="app-url">{{ $application->url }}</div>

        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Statut</div>
                <div class="metric-value orange">SLOW</div>
            </div>
            <div class="metric">
                <div class="metric-label">Latence mesurée</div>
                <div class="metric-value orange">{{ $responseTime }}ms</div>
            </div>
            <div class="metric">
                <div class="metric-label">Seuil WARN configuré</div>
                <div class="metric-value">{{ $threshold }}ms</div>
            </div>
            <div class="metric">
                <div class="metric-label">Dépassement</div>
                <div class="metric-value orange">+{{ $responseTime - $threshold }}ms</div>
            </div>
            <div class="metric">
                <div class="metric-label">Groupe</div>
                <div class="metric-value">{{ $application->group_name ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Heure de détection</div>
                <div class="metric-value">{{ now()->format('H:i:s') }}</div>
            </div>
        </div>

        @php
            $pct = min(100, round(($responseTime / $application->latency_down_ms) * 100));
        @endphp
        <div class="latency-bar">
            <div class="latency-bar-label">Niveau de latence ({{ $pct }}% du seuil DOWN)</div>
            <div class="bar-track">
                <div class="bar-fill" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        <a href="{{ url('/applications/'.$application->id) }}" class="btn">
            Voir les détails →
        </a>

        <p style="font-size:13px;color:#64748b;">
            L'application répond mais avec une latence dégradant l'expérience utilisateur.
            Si la latence dépasse {{ $application->latency_down_ms }}ms, une alerte PANNE sera envoyée.
        </p>
    </div>
    <div class="footer">
        <p><strong>M-Monitoring</strong> — Plateforme de surveillance applicative</p>
        <p style="margin-top:4px;">Cet email a été envoyé automatiquement. Ne pas répondre.</p>
    </div>
</div>
</body>
</html>