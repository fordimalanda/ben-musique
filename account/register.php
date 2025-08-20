<?php
$errors = [];
$success = false;


require_once '../config/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Récupération des données
  $prenom = trim($_POST['prenom'] ?? '');
  $nom = trim($_POST['nom'] ?? '');
  $tel = trim($_POST['tel'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $sexe = $_POST['sexe'] ?? null;
  $birth_day = $_POST['birth_day'] ?? '';
  $bio = trim($_POST['bio'] ?? '');
  $adresse = trim($_POST['adresse'] ?? '');
  $pays = trim($_POST['pays'] ?? '');
  $ville = trim($_POST['ville'] ?? '');
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';

  // Validation
  if (!$prenom || strlen($prenom) > 25) $errors[] = "Prénom invalide";
  if (!$nom || strlen($nom) > 25) $errors[] = "Nom invalide";
  if ($tel && strlen($tel) > 25) $errors[] = "Téléphone invalide";
  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
  if ($sexe && !in_array($sexe, ['M', 'F', 'Other'])) $errors[] = "Sexe invalide";
  if (!$birth_day) $errors[] = "Date de naissance obligatoire";
  if (!$pays) $errors[] = "Pays obligatoire";
  if (!$ville) $errors[] = "Ville obligatoire";
  if (!$password || strlen($password) < 6) $errors[] = "Mot de passe trop court";
  if ($password !== $password_confirm) $errors[] = "Les mots de passe ne correspondent pas";

  // Upload de la photo
  $profil_picture_path = null;
  if (!empty($_FILES['profil_picture']['name'])) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($finfo, $_FILES['profil_picture']['tmp_name']);
    finfo_close($finfo);

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file_type, $allowed_types)) {
      $errors[] = "Format de photo non valide";
    } else {
      $upload_dir = __DIR__ . '/uploads/';
      if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
      $extension = pathinfo($_FILES['profil_picture']['name'], PATHINFO_EXTENSION);
      $filename = uniqid('profile_', true) . '.' . $extension;
      $path = $upload_dir . $filename;
      if (move_uploaded_file($_FILES['profil_picture']['tmp_name'], $path)) {
        $profil_picture_path = 'uploads/' . $filename;
      } else {
        $errors[] = "Erreur lors de l'envoi de la photo";
      }
    }
  }

  // Vérifie si l'email est déjà utilisé
  if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
      $errors[] = "Cet e-mail est déjà utilisé.";
    }
  }

  // Si tout est bon, insertion
  if (empty($errors)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, tel, email, sexe, birth_day, profil_picture, bio, adresse, pays, ville, password)
      VALUES (:prenom, :nom, :tel, :email, :sexe, :birth_day, :profil_picture, :bio, :adresse, :pays, :ville, :password)");

    $stmt->execute([
      'prenom' => $prenom,
      'nom' => $nom,
      'tel' => $tel ?: null,
      'email' => $email,
      'sexe' => $sexe ?: null,
      'birth_day' => $birth_day,
      'profil_picture' => $profil_picture_path,
      'bio' => $bio ?: null,
      'adresse' => $adresse ?: null,
      'pays' => $pays,
      'ville' => $ville,
      'password' => $password_hash,
    ]);

    $success = true;
  }
}
?>

<!-- HTML / Formulaire Bootstrap -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .form-register {
      width: 100%;
      max-width: 600px;
      padding: 2rem;
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <main class="form-register">
    <h1 class="h3 mb-4 fw-normal text-center">Créer un compte</h1>

    <?php if ($success): ?>
      <div class="alert alert-success">Inscription réussie ! <a href="signin.php">Connectez-vous ici</a>.</div>
    <?php elseif (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Prénom *</label>
          <input type="text" class="form-control" name="prenom" required maxlength="25" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Nom *</label>
          <input type="text" class="form-control" name="nom" required maxlength="25" />
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="tel" class="form-control" name="tel" maxlength="25" />
      </div>

      <div class="mb-3">
        <label class="form-label">Sexe</label>
        <select class="form-select" name="sexe">
          <option value="">Choisir...</option>
          <option value="M">Masculin</option>
          <option value="F">Féminin</option>
          <option value="Other">Autre</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Date de naissance *</label>
        <input type="date" class="form-control" name="birth_day" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Photo de profil</label>
        <input type="file" class="form-control" name="profil_picture" accept="image/*" />
      </div>

      <div class="mb-3">
        <label class="form-label">Bio</label>
        <textarea class="form-control" name="bio" rows="3" maxlength="255"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Adresse</label>
        <input type="text" class="form-control" name="adresse" maxlength="255" />
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Pays *</label>
          <input type="text" class="form-control" name="pays" required maxlength="100" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Ville *</label>
          <input type="text" class="form-control" name="ville" required maxlength="100" />
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Adresse e-mail *</label>
        <input type="email" class="form-control" name="email" required maxlength="255" />
      </div>

      <div class="mb-3">
        <label class="form-label">Mot de passe *</label>
        <input type="password" class="form-control" name="password" required minlength="6" />
      </div>

      <div class="mb-4">
        <label class="form-label">Confirmer le mot de passe *</label>
        <input type="password" class="form-control" name="password_confirm" required minlength="6" />
      </div>

      <button type="submit" class="btn btn-success w-100 btn-lg">S'inscrire</button>
    </form>

    <p class="mt-3 text-center">
      Vous avez déjà un compte ? 
      <a href="signin.php">Connectez-vous ici.</a>
    </p>

    <p class="mt-3 mb-0 text-muted text-center">&copy; 2025</p>
  </main>
</body>
</html>