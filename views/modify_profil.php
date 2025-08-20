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

$errors = [];
$success = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $sexe = $_POST['sexe'] ?? '';
    $birth_day = $_POST['birth_day'] ?? '';
    $ville = trim($_POST['ville'] ?? '');
    $pays = trim($_POST['pays'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // Validation basique
    if (!$prenom) $errors[] = "Le prénom est obligatoire.";
    if (!$nom) $errors[] = "Le nom est obligatoire.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Un email valide est obligatoire.";
    if ($sexe !== 'M' && $sexe !== 'F') $errors[] = "Le sexe doit être 'M' ou 'F'.";
    if ($birth_day && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth_day)) $errors[] = "La date de naissance doit être au format YYYY-MM-DD.";

    // Gestion upload photo
    $profil_picture = $user['profil_picture']; // garder l’ancienne photo si pas de nouveau upload
    if (isset($_FILES['profil_picture']) && $_FILES['profil_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profil_picture']['tmp_name'];
        $fileName = $_FILES['profil_picture']['name'];
        $fileSize = $_FILES['profil_picture']['size'];
        $fileType = $_FILES['profil_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Générer un nom unique
            $newFileName = 'profile_' . md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profil_picture = 'uploads/' . $newFileName;
            } else {
                $errors[] = "Erreur lors de l'upload de la photo de profil.";
            }
        } else {
            $errors[] = "Format de fichier non supporté pour la photo de profil.";
        }
    }

    if (empty($errors)) {
        // Mettre à jour la base de données
        $stmt = $pdo->prepare("UPDATE users SET prenom = ?, nom = ?, email = ?, tel = ?, sexe = ?, birth_day = ?, ville = ?, pays = ?, adresse = ?, bio = ?, profil_picture = ? WHERE id_user = ?");
        $stmt->execute([
            $prenom, $nom, $email, $tel, $sexe, $birth_day, $ville, $pays, $adresse, $bio, $profil_picture, $_SESSION['user_id']
        ]);

        // Mettre à jour les variables de session si nécessaire (ex: prénom, nom, photo)
        $_SESSION['user_prenom'] = $prenom;
        $_SESSION['user_nom'] = $nom;
        $_SESSION['user_profile'] = $profil_picture;

        $success = true;

        // Recharger les données mises à jour
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier mon profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f0f2f5;
            padding: 2rem 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            border-radius: 12px;
            padding: 2.5rem 3rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .profile-img-preview {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #007bff;
            margin-bottom: 1rem;
        }
        label {
            font-weight: 600;
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
            border-radius: 8px;
        }
        .alert-success {
            max-width: 700px;
            margin: 1rem auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4 text-center">Modifier mon profil</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Votre profil a été mis à jour avec succès !
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="text-center">
            <img src="<?php echo '../' . htmlspecialchars($user['profil_picture'] ?? 'uploads/default-profile.png'); ?>" alt="Photo de profil" class="profile-img-preview" id="profilePreview">
        </div>

        <div class="mb-3">
            <label for="profil_picture">Changer la photo de profil</label>
            <input type="file" class="form-control" name="profil_picture" id="profil_picture" accept=".jpg,.jpeg,.png,.gif">
        </div>

        <div class="mb-3">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" name="prenom" id="prenom" required value="<?= htmlspecialchars($user['prenom']) ?>">
        </div>

        <div class="mb-3">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" name="nom" id="nom" required value="<?= htmlspecialchars($user['nom']) ?>">
        </div>

        <div class="mb-3">
            <label for="email">Adresse e-mail</label>
            <input type="email" class="form-control" name="email" id="email" required value="<?= htmlspecialchars($user['email']) ?>">
        </div>

        <div class="mb-3">
            <label for="tel">Téléphone</label>
            <input type="tel" class="form-control" name="tel" id="tel" value="<?= htmlspecialchars($user['tel'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="sexe">Sexe</label>
            <select class="form-select" name="sexe" id="sexe" required>
                <option value="M" <?= ($user['sexe'] === 'M') ? 'selected' : '' ?>>Masculin</option>
                <option value="F" <?= ($user['sexe'] === 'F') ? 'selected' : '' ?>>Féminin</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="birth_day">Date de naissance</label>
            <input type="date" class="form-control" name="birth_day" id="birth_day" value="<?= htmlspecialchars($user['birth_day'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="ville">Ville</label>
            <input type="text" class="form-control" name="ville" id="ville" value="<?= htmlspecialchars($user['ville'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="pays">Pays</label>
            <input type="text" class="form-control" name="pays" id="pays" value="<?= htmlspecialchars($user['pays'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="adresse">Adresse</label>
            <input type="text" class="form-control" name="adresse" id="adresse" value="<?= htmlspecialchars($user['adresse'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="bio">Bio</label>
            <textarea class="form-control" name="bio" id="bio" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </form>
</div>

<script>
    // Afficher l'aperçu de la nouvelle photo sélectionnée
    document.getElementById('profil_picture').addEventListener('change', function(event) {
        const [file] = this.files;
        if (file) {
            document.getElementById('profilePreview').src = URL.createObjectURL(file);
        }
    });
</script>

</body>
</html>