<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../account/signin.php');
    exit();
}

require_once '../config/db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur non trouvé.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f0f2f5;
            padding: 3rem 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-container {
            max-width: 700px;
            margin: auto;
            background: white;
            border-radius: 12px;
            padding: 2.5rem 3rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .profile-img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #007bff;
            margin-bottom: 1.5rem;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .info-value {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #222;
        }
        h1 {
            font-weight: 700;
            margin-bottom: 2rem;
            color: #222;
        }
        .btn-block {
            display: block;
            width: 100%;
            padding: 0.75rem 0;
            font-size: 1.1rem;
            margin-top: 1rem;
            border-radius: 8px;
        }
        .btn-modify {
            background-color: #0d6efd;
            color: white;
            border: none;
        }
        .btn-modify:hover {
            background-color: #084cd6;
            color: white;
        }
        .btn-logout {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        .btn-logout:hover {
            background-color: #b02a37;
            color: white;
        }
    </style>
</head>
<body>

<div class="profile-container text-center">
    <img src="<?php echo '../' . htmlspecialchars($user['profil_picture'] ?? 'uploads/default-profile.png'); ?>" alt="Photo de profil" class="profile-img">

    <h1><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h1>

    <div class="text-start px-4">
        <div>
            <span class="info-label">Adresse e-mail :</span><br>
            <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
        </div>
        <div>
            <span class="info-label">Ville :</span><br>
            <span class="info-value"><?= htmlspecialchars($user['ville'] ?? '-') ?></span>
        </div>
        <div>
            <span class="info-label">Date de naissance :</span><br>
            <span class="info-value"><?= htmlspecialchars($user['birth_day'] ?? '-') ?></span>
        </div>
        <div>
            <span class="info-label">Bio :</span><br>
            <p class="info-value"><?= nl2br(htmlspecialchars($user['bio'] ?? '-')) ?></p>
        </div>
    </div>

    <a href="modify_profil.php" class="btn btn-modify btn-block">Modifier mon profil</a>
    <a href="../account/logout.php" class="btn btn-logout btn-block">Déconnexion</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>