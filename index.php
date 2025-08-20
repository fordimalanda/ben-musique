<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: account/signin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Accueil Music App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .navbar-brand { font-size: 1.5rem; font-weight: bold; }
    .profile-img { width:40px; height:40px; object-fit:cover; border-radius:50%; cursor:pointer; }
    .subscribe-btn { margin: 2rem 0; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
    <a class="navbar-brand" href="#">Ben Musique</a>
    <div class="ms-auto" style="position: relative; z-index: 1000;">
        <a href="views/profil.php" style="pointer-events:auto;">
            <img src="<?php echo htmlspecialchars($_SESSION['user_profile'] ?? 'uploads/default-profile.png'); ?>"
             alt="Profil"
             class="profile-img">
        </a>
    </div>


  </nav>

  <main class="container mt-4">
    <div id="carouselHome" class="carousel slide carousel-fade mb-5" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselHome" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#carouselHome" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#carouselHome" data-bs-slide-to="2"></button>
      </div>
      <div class="carousel-inner">
  <div class="carousel-item active">
    <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=1500&q=80" class="d-block w-100" alt="Concert">
    <div class="carousel-caption d-none d-md-block">
      <h5>Découvre les hits du moment</h5>
      <p>Écoute en illimité les sons qui cartonnent !</p>
    </div>
  </div>
  <div class="carousel-item">
    <img src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&w=1500&q=80" class="d-block w-100" alt="Musique relax">
    <div class="carousel-caption d-none d-md-block">
      <h5>Crée ton ambiance</h5>
      <p>Des playlists pour chaque humeur</p>
    </div>
  </div>
  <div class="carousel-item">
    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1500&q=80" class="d-block w-100" alt="Écouteurs">
    <div class="carousel-caption d-none d-md-block">
      <h5>Qualité audio premium</h5>
      <p>Ressens chaque beat, chaque basse</p>
    </div>
  </div>
</div>

      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselHome" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Précédent</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselHome" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Suivant</span>
      </button>
    </div>

    <div class="text-center subscribe-btn">
      <h2>Prêt à passer à l'étape supérieure ?</h2>
      <a href="views/price.php" class="btn btn-primary btn-lg">Voir les abonnements</a>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>