<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rétablissement</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; }
        .header { background: #16a34a; padding: 28px 32px; border-radius: 12px 12px 0 0; }
        .header-icon { font-size: 32px; margin-bottom: 8px; }
        .header h1 { color: #fff; font-size: 20px; font-weight: 700; }
        .header p { color: #bbf7d0; font-size: 13px; margin-top: 4px; }
        .body { background: #fff; padding: 32px; }
        .alert-badge { display: inline-block; background: #dcfce7; color: #16a34a; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 20px; }
        .app-name { font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .app-url { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .recovery-box { text-align: center; background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .recovery-icon { font-size: 40px; margin-bottom: 8px; }
        .recovery-text { font-size: 16px; font-weight: 700; color: #16a34a; }
        .recovery-sub { font-size: 13px; color: #64748b; margin-top: 4px; }
        .metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
        .metric { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; }
        .metric-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .metric-value { font-size: 15px; font-weight: 600; color: #0f172a; }
        .metric-value.green { color: #16a34a; }
        .timeline { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .timeline-item { display: flex; align-items: flex-start; gap: 12px; padding: 6px 0; }
        .timeline-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
        .timeline-dot.red { background: #dc2626; }
        .timeline-dot.green { background: #16a34a; }
        .timeline-text { font-size: 13px; color: #374151; }
        .timeline-time { font-size: 12px; color: #94a3b8; }
        .btn { display: inline-block; background: #16a34a; color: #fff; font-size: 14px; font-weight: 600; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-bottom: 24px; }
        .footer { background: #f8fafc; padding: 20px 32px; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; }
        .footer p { font-size: 12px; color: #94a3b8; }
        .footer strong { color: #64748b; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-icon">✅</div>
        <h1>Application rétablie</h1>
        <p>Résolu le {{ now()->format('d/m/Y à H:i:s') }}</p>
    </div>
    <div class="body">
        <span class="alert-badge">✓ Incident résolu</span>
        <div class="app-name">{{ $application->name }}</div>
        <div class="app-url">{{ $application->url }}</div>

        <div class="recovery-box">
            <div class="recovery-icon">🟢</div>
            <div class="recovery-text">{{ $application->name }} est de nouveau opérationnel</div>
            <div class="recovery-sub">L'incident a été résolu automatiquement</div>
        </div>

        @php
            $duration = $incident->duration_seconds;
            $formatted = $duration
                ? sprintf('%02dh %02dm %02ds', floor($duration/3600), floor(($duration%3600)/60), $duration%60)
                : 'inconnue';
        @endphp

        <div class="metrics">
            <div class="metric">
                <div class="metric-label">Statut actuel</div>
                <div class="metric-value green">✓ UP</div>
            </div>
            <div class="metric">
                <div class="metric-label">Durée de l'incident</div>
                <div class="metric-value">{{ $formatted }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Cause initiale</div>
                <div class="metric-value">{{ ucfirst(str_replace('_', ' ', $incident->root_cause ?? 'inconnue')) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Groupe</div>
                <div class="metric-value">{{ $application->group_name ?? '—' }}</div>
            </div>
        </div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-dot red"></div>
                <div>
                    <div class="timeline-text">Début de l'incident</div>
                    <div class="timeline-time">{{ $incident->started_at->format('d/m/Y à H:i:s') }}</div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot green"></div>
                <div>
                    <div class="timeline-text">Incident résolu</div>
                    <div class="timeline-time">{{ $incident->resolved_at?->format('d/m/Y à H:i:s') ?? now()->format('d/m/Y à H:i:s') }}</div>
                </div>
            </div>
        </div>

        <a href="{{ url('/applications/'.$application->id) }}" class="btn">
            Voir l'historique →
        </a>

        <p style="font-size:13px;color:#64748b;">
            L'application est revenue à la normale. Consultez l'historique des checks
            pour analyser les causes de cet incident.
        </p>
    </div>
    <div class="footer">
        <p><strong>M-Monitoring</strong> — Plateforme de surveillance applicative</p>
        <p style="margin-top:4px;">Cet email a été envoyé automatiquement. Ne pas répondre.</p>
    </div>
</div>
</body>
</html>