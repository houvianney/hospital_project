<?php
// modifier_commande.php
include "connexion.php";
require "auth_check.php";  

// POST : traitement
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['trajet_id'] ?? 0);
    $depart = trim($_POST['depart'] ?? '');
    $arrivee = trim($_POST['arrivee'] ?? '');
    $tarif   = trim($_POST['tarif'] ?? '');
   

    $stmt = $pdo->prepare("UPDATE trajets SET depart=?, arrivee=?, tarif=? WHERE trajet_id=?");
    $stmt->execute([$depart, $arrivee, $tarif, $id]);
    header("Location: index_trajet.php?msg=Trajet+modifié+avec+succès.");
    exit;
}

// GET : formulaire pré-rempli
$id  = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM trajets WHERE trajet_id = ?");
$stmt->execute([$id]);
$c   = $stmt->fetch();
if (!$c) { header("Location: index_trajet.php"); exit; }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Modifier trajets</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f8fafc;font-size:13px;}
    .card{border:none;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.09);}
    .card-header{background:linear-gradient(135deg,#1a3a6e,#2563ab);color:#fff;border-radius:12px 12px 0 0!important;}
    </style>
</head>
<body>
<div class="container py-4">
<div class="row justify-content-center"><div class="col-lg-7">
<div class="card">
    <div class="card-header py-3"><h5 class="mb-0">✏️ Modifier trajet #<?= $id ?></h5></div>
    <div class="card-body p-4">
        <form action="modifier_trajet.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="trajet_id"   value="<?= $id?>">

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:12px;">Point de départ</label>
                    <input type="text" class="form-control form-control-sm"
                           name="depart" value="<?= $c['depart'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:12px;">Point d'arrivée</label>
                    <input type="text" class="form-control form-control-sm"
                           name="arrivee" value="<?= $c['arrivee'] ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold" style="font-size:12px;">Tarif</label>
                    <input type="text" class="form-control form-control-sm"
                           name="tarif" value="<?= htmlspecialchars($c['tarif']) ?>" required>
                </div>
            </div>

            
            

            

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm px-4">💾 Enregistrer</button>
                <a href="index_trajet.php" class="btn btn-secondary btn-sm">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div></div></div>
<script src="js/bootstrap.bundle.min.js"></script>
</body></html>

