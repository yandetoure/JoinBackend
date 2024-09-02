<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <div class="logo">
            <img src="ressources/css/images/logo.png" alt="">
        </div>
        <h1 style="color: #333;">Introduction</h1>
        <p>Bonjour {{ $user->first_name }},</p>
        <p>Votre compte a bien été créé, vous pouvez utiliser votre email et mot de passe pour vous connecter.</p>
        <p>Merci et à bientôt,</p>
        <p>{{ config('app.name') }}</p>
    </div>
</body>
</html>
