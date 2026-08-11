<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation M-Monitoring</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; }
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 32px; border-radius: 12px 12px 0 0; text-align: center; }
        .header-icon { font-size: 40px; margin-bottom: 12px; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 700; }
        .header p { color: #bfdbfe; font-size: 13px; margin-top: 6px; }
        .body { background: #fff; padding: 32px; }
        .greeting { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 24px; }
        .credentials { background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 24px; }
        .credentials-title { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 14px; }
        .credential-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .credential-row:last-child { border-bottom: none; }
        .credential-label { font-size: 13px; color: #64748b; }
        .credential-value { font-size: 14px; font-weight: 600; color: #0f172a; font-family: monospace; background: #fff; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; }
        .warning { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
        .warning p { font-size: 13px; color: #92400e; }
        .requirements { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .requirements-title { font-size: 12px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; }
        .requirements ul { list-style: none; }
        .requirements li { font-size: 13px; color: #166534; padding: 3px 0; }
        .requirements li::before { content: '✓ '; font-weight: 700; }
        .btn { display: block; background: #1e40af; color: #fff; font-size: 15px; font-weight: 600; padding: 14px 24px; border-radius: 8px; text-decoration: none; text-align: center; margin-bottom: 24px; }
        .btn:hover { background: #1d4ed8; }
        .note { font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .footer { background: #f8fafc; padding: 20px 32px; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-icon">🛡️</div>
        <h1>Bienvenue sur M-Monitoring</h1>
        <p>Votre compte a été créé avec succès</p>
    </div>
    <div class="body">
        <p class="greeting">Bonjour {{ $user->name }},</p>
        <p class="intro">
            Un compte a été créé pour vous sur la plateforme de surveillance <strong>M-Monitoring</strong>.
            Vous trouverez ci-dessous vos identifiants de connexion temporaires.
        </p>

        <div class="credentials">
            <div class="credentials-title">Vos identifiants de connexion</div>
            <div class="credential-row">
                <span class="credential-label">Adresse email</span>
                <span class="credential-value">{{ $user->email }}</span>
            </div>
            <div class="credential-row">
                <span class="credential-label">Mot de passe temporaire</span>
                <span class="credential-value">{{ $plainPassword }}</span>
            </div>
            <div class="credential-row">
                <span class="credential-label">Rôle attribué</span>
                <span class="credential-value">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
            </div>
        </div>

        <div class="warning">
            <p>⚠️ <strong>Important :</strong> Ce mot de passe est temporaire. Vous serez invité à le changer dès votre première connexion.</p>
        </div>

        <div class="requirements">
            <div class="requirements-title">Votre nouveau mot de passe devra contenir</div>
            <ul>
                <li>Au moins 8 caractères</li>
                <li>Au moins une lettre majuscule (A-Z)</li>
                <li>Au moins une lettre minuscule (a-z)</li>
                <li>Au moins un chiffre (0-9)</li>
                <li>Au moins un caractère spécial (@, #, $, !, etc.)</li>
            </ul>
        </div>

        <a href="{{ url('/') }}" class="btn">
            Accéder à M-Monitoring →
        </a>

        <p class="note">
            Si vous n'êtes pas à l'origine de cette demande ou si vous pensez avoir reçu cet email par erreur,
            veuillez contacter votre administrateur système immédiatement.
        </p>
    </div>
    <div class="footer">
        <p><strong>M-Monitoring</strong> — Plateforme de surveillance applicative</p>
        <p style="margin-top:4px;">Cet email a été envoyé automatiquement. Ne pas répondre.</p>
    </div>
</div>
</body>
</html>