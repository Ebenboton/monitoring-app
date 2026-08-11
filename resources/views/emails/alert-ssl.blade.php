<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte SSL</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; }
        .header { background: {{ $daysRemaining < 7 ? '#dc2626' : '#d97706' }}; padding: 28px 32px; border-radius: 12px 12px 0 0; }
        .header-icon { font-size: 32px; margin-bottom: 8px; }
        .header h1 { color: #fff; font-size: 20px; font-weight: 700; }
        .header p { color: {{ $daysRemaining < 7 ? '#fecaca' : '#fde68a' }}; font-size: 13px; margin-top: 4px; }
        .body { background: #fff; padding: 32px; }
        .alert-badge { display: inline-block; background: {{ $daysRemaining < 7 ? '#fee2e2' : '#fef3c7' }}; color: {{ $daysRemaining < 7 ? '#dc2626' : '#d97706' }}; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 20px; }
        .app-name { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .app-url { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .ssl-countdown { text-align: center; background: {{ $daysRemaining < 7 ? '#fef2f2' : '#fffbeb' }}; border: 2px solid {{ $daysRemaining < 7 ? '#fecaca' : '#fde68a' }}; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .ssl-days { font-size: 48px; font-weight: 800; color: {{ $daysRemaining < 7 ? '#dc2626' : '#d97706' }}; line-height: 1; }
        .ssl-label { font-size: 14px; color: #64748b; margin-top: 4px; }
        .metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .metric { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; }
        .metric-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .metric-value { font-size: 15px; font-weight: 600; color: #0f172a; }
        .btn { display: inline-block; background: {{ $daysRemaining < 7 ? '#dc2626' : '#d97706' }}; color: #fff; font-size: 14px; font-weight: 600; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-bottom: 24px; }
        .footer { background: #f8fafc; padding: 20px 32px; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; }
        .footer strong { color: #64748b; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-icon">🔐</div>
        <h1>{{ $daysRemaining < 7 ? 'Certificat SSL critique' : 'Certificat SSL à renouveler' }}</h1>
        <p>Vérifié le {{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>
    <div class="body">
        <span class="alert-badge">
            {{ $daysRemaining < 7 ? '🔴 Critique' : '🟡 Attention' }}
        </span>
        <div class="app-name">{{ $application->name }}</div>
        <div class="app-url">{{ $application->url }}</div>

        <div class="ssl-countdown">
            <div class="ssl-days">{{ $daysRemaining }}</div>
            <div class="ssl-label">jours avant expiration du certificat</div>
        </div>

        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Application</div>
                <div class="metric-value">{{ $application->name }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Groupe</div>
                <div class="metric-value">{{ $application->group_name ?? '—' }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Date d'expiration</div>
                <div class="metric-value">{{ now()->addDays($daysRemaining)->format('d/m/Y') }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Niveau d'alerte</div>
                <div class="metric-value">{{ $daysRemaining < 7 ? '🔴 CRITIQUE' : '🟡 WARN' }}</div>
            </div>
        </div>

        <a href="{{ url('/applications/'.$application->id) }}" class="btn">
            Voir l'application →
        </a>

        <p style="font-size:13px;color:#64748b;">
            @if($daysRemaining < 7)
                Le certificat SSL expire dans moins de 7 jours. Un renouvellement immédiat est nécessaire
                pour éviter que les utilisateurs ne puissent plus accéder à l'application.
            @else
                Planifiez le renouvellement de ce certificat SSL avant le {{ now()->addDays($daysRemaining)->format('d/m/Y') }}.
                Un nouveau rappel sera envoyé si le certificat n'est pas renouvelé.
            @endif
        </p>
    </div>
    <div class="footer">
        <p><strong>M-Monitoring</strong> — Plateforme de surveillance applicative</p>
        <p style="margin-top:4px;">Cet email a été envoyé automatiquement. Ne pas répondre.</p>
    </div>
</div>
</body>
</html>