<?php
session_start();

$errors = [];

require_once '../config/db.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors[] = "Veuillez remplir tous les champs.";
    } else {
        // Recherche de l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        

        if ($user && password_verify($password, $user['password'])) {
    // Authentification réussie
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_prenom'] = $user['prenom'];
    $_SESSION['user_profile'] = trim($user['profil_picture']);

    // 🟡 Ajout dans la table `authentifications`
    $id_user = $user['id_user'];
    $id_app = 1; // App-Client
    $auth_method = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'tel';
    $last_login_at = date('Y-m-d H:i:s');
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO authentifications (id_user, id_app, password, auth_method, last_login_at, adress_ip)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $id_user,
        $id_app,
        $user['password'],  // Utilise le hash déjà existant
        $auth_method,
        $last_login_at,
        $ip_address
    ]);

    // ✅ Redirection après enregistrement
    header('Location: ../index.php');
    exit();
} else {
    $errors[] = "Email ou mot de passe incorrect.";
}

    }
}
?>

<!-- HTML / Formulaire de connexion -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .form-signin {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<main class="form-signin">
    <h1 class="h3 mb-4 fw-normal text-center">Connexion</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
            <label for="email">Adresse e-mail</label>
        </div>
        <div class="form-floating mb-4">
            <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
            <label for="password">Mot de passe</label>
        </div>
        <button class="btn btn-primary w-100 btn-lg" type="submit">Se connecter</button>
    </form>

    <p class="mt-3 text-center">
        Vous n'avez pas de compte ? <a href="register.php">Créer un compte</a>
    </p>
    <p class="mt-3 mb-0 text-muted text-center">&copy; 2025</p>
</main>
</body>
</html>