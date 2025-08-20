<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: account/signin.php');
    exit();
}
require_once '../config/db.php';

$stm = $pdo->query("SELECT * FROM abonnements ORDER BY id_abonnement");
$abonnements = $stm->fetchAll();

// Définir les avantages par type ou nom
$features = [
    'Starter' => [
        'Accès à la bibliothèque de base',
        'Écoute avec publicité',
        'Qualité audio standard'
    ],
    'Business' => [
        'Accès illimité à tous les titres',
        'Zéro publicité',
        'Qualité audio HD',
        'Téléchargement possible'
    ],
    'Premium' => [
        'Toutes les fonctionnalités Business',
        'Audio Hi-Fi & lossless',
        'Support prioritaire',
        'Accès aux nouveautés exclusives'
    ]
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nos Abonnements</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-abonnement {
      border-radius: 12px;
      transition: transform .2s, box-shadow .2s;
      height: 100%;
    }
    .card-abonnement:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .btn-acheter {
      width: 100%;
    }
    .feature-list {
      margin-top: 1rem;
    }
    .feature-list li {
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <h1 class="mb-4 text-center">Choisis ton abonnement</h1>
  <div class="row g-4">
    <?php foreach ($abonnements as $abo): ?>
    <div class="col-md-4">
      <div class="card card-abonnement h-100">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= htmlspecialchars($abo['name']) ?> (<?= htmlspecialchars($abo['type_abonnement']) ?>)</h5>
          <p class="card-text mb-1"><?= htmlspecialchars($abo['description']) ?></p>
          <p class="h4 text-primary mb-1"><?= number_format($abo['prix'], 2, ',', ' ') ?> USD</p>
          <p class="text-muted small mb-3"><?= htmlspecialchars($abo['duree']) ?> jours</p>
          
          <?php
          $name = $abo['name'];
          if (isset($features[$name])) {
              echo '<ul class="feature-list">';
              foreach ($features[$name] as $f) {
                  echo '<li>✅ ' . htmlspecialchars($f) . '</li>';
              }
              echo '</ul>';
          }
          ?>

          <div class="mt-auto">
            <form action="purchase.php" method="POST">
              <input type="hidden" name="id_abonnement" value="<?= $abo['id_abonnement'] ?>">
              <button type="submit" class="btn btn-success btn-acheter mt-3">Acheter</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>