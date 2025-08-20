<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: account/signin.php');
    exit();
}

require_once '../config/db.php';

if (!isset($_POST['id_abonnement'])) {
    echo "Aucun abonnement sélectionné.";
    exit();
}

$id_abo = intval($_POST['id_abonnement']);

$stmt = $pdo->prepare("SELECT * FROM abonnements WHERE id_abonnement = ?");
$stmt->execute([$id_abo]);
$abo = $stmt->fetch();

if (!$abo) {
    echo "Abonnement introuvable.";
    exit();
}

// Récupérer les infos de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT prenom, nom, email FROM users WHERE id_user = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

$nomComplet = $user['prenom'] . ' ' . $user['nom'];
$email = $user['email'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paiement - <?= htmlspecialchars($abo['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4 text-center">Paiement de l'abonnement</h2>

  <div class="card mb-4">
    <div class="card-body">
      <h4 class="card-title"><?= htmlspecialchars($abo['name']) ?> (<?= htmlspecialchars($abo['type_abonnement']) ?>)</h4>
      <p><strong>Description :</strong> <?= htmlspecialchars($abo['description']) ?></p>
      <p><strong>Prix :</strong> <?= number_format($abo['prix'], 2, ',', ' ') ?> USD</p>
      <p><strong>Durée :</strong> <?= $abo['duree'] ?> jours</p>
    </div>
  </div>

  <form method="post" action="confirm_payment.php">
    <input type="hidden" name="id_abonnement" value="<?= $abo['id_abonnement'] ?>">
    <input type="hidden" name="nom_client" value="<?= htmlspecialchars($nomComplet) ?>">
    <input type="hidden" name="email_client" value="<?= htmlspecialchars($email) ?>">

    <div class="mb-3">
      <label class="form-label">Moyen de paiement :</label>
      <select class="form-select" name="moyen_paiement" required>
        <option value="">-- Sélectionnez un moyen --</option>
        <option value="Orange Money">Orange Money</option>
        <option value="M-Pesa">M-Pesa</option>
        <option value="Airtel Money">Airtel Money</option>
        <option value="AfriMoney">AfriMoney</option>
        <option value="VISA">VISA</option>
        <option value="MasterCard">MasterCard</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Nom du titulaire :</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($nomComplet) ?>" disabled>
    </div>

    <div class="mb-3">
      <label class="form-label">Email de confirmation :</label>
      <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" disabled>
    </div>

    <button type="submit" class="btn btn-primary">Payer maintenant</button>
  </form>
</div>
</body>
</html>