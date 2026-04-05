<?php
// modifier_commande.php
include "connexion.php";
require "auth_check.php";  

// POST : traitement
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id            = intval($_POST['consultation_id']);
    $nom           = $_POST['nom'];
    $motif         = $_POST['motif'];
    $date_heure    = $_POST['date_heure'];
    $statut        = $_POST['statut'];
    $medecin_id    = isset($_POST['medecin_id']) && $_POST['medecin_id'] !== '' ? intval($_POST['medecin_id']) : null;

    $stmt = $pdo->prepare("UPDATE consultations SET nom_patient=?, motif=?, date_heure=?, statut=?, medecin_id=? WHERE consultation_id=?");
    $stmt->execute([$nom, $motif, $date_heure, $statut, $medecin_id, $id]);
    header("Location: index.php?msg=Commande+modifiée+avec+succès.");
    exit;
}

// GET : formulaire pré-rempli
$id  = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM consultations WHERE consultation_id = ?");
$stmt->execute([$id]);
$c   = $stmt->fetch();
if (!$c) { header("Location: index.php"); exit; }

$serveurs_res = $pdo->query("SELECT * FROM medecins WHERE disponible = 'Oui' ORDER BY nom ASC")->fetchAll();
$dt = new DateTime($c['date_heure']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Modifier consultation</title>
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
    <div class="card-header py-3"><h5 class="mb-0">✏️ Modifier consultation #<?= $id ?></h5></div>
    <div class="card-body p-4">
        <form action="modifier_consultation.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="consultation_id"   value="<?= $c['consultation_id'] ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:12px;">nom du patient</label>
                    <input type="text" class="form-control form-control-sm"
                           name="nom" value="<?= $c['nom_patient'] ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold" style="font-size:12px;">Motif</label>
                    <input type="text" class="form-control form-control-sm"
                           name="motif" value="<?= htmlspecialchars($c['motif']) ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold" style="font-size:12px;">Date et heure</label>
                    <input type="datetime-local" class="form-control form-control-sm"
                           name="date_heure" value="<?= htmlspecialchars($c['date_heure']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size:12px;">Statut</label>
                    <select class="form-select form-select-sm" name="statut">
                        <?php foreach(['En attente','En cours','Terminée'] as $s): ?>
                        <option value="<?= $s ?>" <?= $c['statut']===$s?'selected':'' ?>>
                            <?= ucfirst($s) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" style="font-size:12px;">Medecin</label>
                    <select class="form-select form-select-sm" name="medecin_id">
                        <option value="" <?= is_null($c['medecin_id']) ? 'selected' : '' ?>>Aucun</option>
                        <?php foreach ($serveurs_res as $s): ?>
                        <option value="<?= $s['medecin_id'] ?>" <?= intval($c['medecin_id']) === intval($s['medecin_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nom'].' '.$s['prenoms'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            

            

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm px-4">💾 Enregistrer</button>
                <a href="index.php" class="btn btn-secondary btn-sm">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div></div></div>
<script src="js/bootstrap.bundle.min.js"></script>
</body></html>

