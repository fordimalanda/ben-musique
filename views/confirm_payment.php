<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: account/signin.php');
    exit();
}

require_once '../config/db.php';

if (
    !isset($_POST['id_abonnement'], $_POST['moyen_paiement'], $_POST['nom_client'], $_POST['email_client'])
) {
    echo "Données manquantes.";
    exit();
}

$id_user = $_SESSION['user_id'];
$id_abonnement = intval($_POST['id_abonnement']);
$moyen = trim($_POST['moyen_paiement']);
$nom_client = trim($_POST['nom_client']);
$email = trim($_POST['email_client']);

// Récupérer les infos de l'abonnement (prix et durée)
$stmt = $pdo->prepare("SELECT prix, duree FROM abonnements WHERE id_abonnement = ?");
$stmt->execute([$id_abonnement]);
$abo = $stmt->fetch();

if (!$abo) {
    echo "Abonnement non trouvé.";
    exit();
}

$prix = $abo['prix'];
$duree = $abo['duree']; // en jours

$date_paiement = date('Y-m-d H:i:s');
$date_expiration = date('Y-m-d H:i:s', strtotime("+$duree days"));

// Insertion dans la table payements
$stmt = $pdo->prepare("
    INSERT INTO payements (id_user, id_abonnement, montant, devise, status, method, date_payement, date_expiration)
    VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)
");

$status = 'réussi';
$devise = 'USD';

$stmt->execute([
    $id_user,
    $id_abonnement,
    $prix,          
    $devise,        
    $status,        
    $moyen,         
    $date_expiration
]);


// ✅ Redirection ou message de succès
header("Location: success.php?abo=$id_abonnement");
exit();