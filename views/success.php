<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../account/signin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Paiement réussi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 text-center">
  <h1 class="text-success">✅ Paiement effectué avec succès !</h1>
  <p class="lead">Merci pour votre souscription. Votre abonnement est maintenant actif.</p>
  <a href="../index.php" class="btn btn-primary mt-4">Retour à l'accueil</a>
</div>
</body>
</html>