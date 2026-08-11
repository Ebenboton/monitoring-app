<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation mot de passe</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: #1e293b; }
        .wrapper { max-width: 600px; margin: 32px auto; }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); padding: 32px; border-radius: 12px 12px 0 0; text-align: center; }
        .header-icon { font-size: 40px; margin-bottom: 12px; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 700; }
        .header p { color: #e9d5ff; font-size: 13px; margin-top: 6px; }
        .body { background: #fff; padding: 32px; }
        .greeting { font-size: 16px; font-weight: 600; color: #0f172a; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 24px; }
        .credentials { background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 24px; }
        .credentials-title { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 14px; }
        .credential-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .credential-row:last-child { border-bottom: none; }
        .credential-label { font-size: 13px; color: #64748b; }
        .credential-value { font-size: 14px; font-weight: 600; color: #0f172a; font-family: monospace; background: #fff; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; }
        .warning { background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444; border-radius: 8px; padding: 14px 16px; margin-bottom: 24px; }
        .warning p { font-size: 13px; color: #991b1b; }
        .btn { display: block; background: #7c3aed; color: #fff; font-size: 15px; font-weight: 600; padding: 14px 24px; border-radius: 8px; text-decoration: none; text-align: center; margin-bottom: 24px; }
        .note { font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .footer { background: #f8fafc; padding: 20px 32px; border-radius: 0 0 12px 12px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="header-icon">🔐</div>
        <h1>Réinitialisation de mot de passe</h1>
        <p>Votre mot de passe a été réinitialisé par un administrateur</p>
    </div>
    <div class="body">
        <p class="greeting">Bonjour {{ $user->name }},</p>
        <p class="intro">
            Un administrateur a réinitialisé votre mot de passe sur <strong>M-Monitoring</strong>.
            Utilisez les identifiants ci-dessous pour vous reconnecter.
        </p>

        <div class="credentials">
            <div class="credentials-title">Vos nouveaux identifiants</div>
            <div class="credential-row">
                <span class="credential-label">Adresse email</span>
                <span class="credential-value">{{ $user->email }}</span>
            </div>
            <div class="credential-row">
                <span class="credential-label">Mot de passe temporaire</span>
                <span class="credential-value">{{ $plainPassword }}</span>
            </div>
        </div>

        <div class="warning">
            <p>🔒 <strong>Sécurité :</strong> Vous serez obligé de définir un nouveau mot de passe dès votre prochaine connexion.</p>
        </div>

        <a href="{{ url('/') }}" class="btn">
            Se connecter à M-Monitoring →
        </a>

        <p class="note">
            Si vous n'avez pas demandé cette réinitialisation, contactez immédiatement votre administrateur système.
            Ne partagez jamais vos identifiants avec quiconque.
        </p>
    </div>
    <div class="footer">
        <p><strong>M-Monitoring</strong> — Plateforme de surveillance applicative</p>
        <p style="margin-top:4px;">Cet email a été envoyé automatiquement. Ne pas répondre.</p>
    </div>
</div>
</body>
</html>