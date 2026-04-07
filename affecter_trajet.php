<?php
include "connexion.php";
require "auth_check.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conducteur_id = intval($_POST['conducteur_id'] ?? 0);
    $trajet_id = intval($_POST['trajet_id'] ?? 0);
    $affectation_id = intval($_POST['affectation_id'] ?? 0);

    if ($conducteur_id && $trajet_id) {
        if ($affectation_id) {
            $stmt = $pdo->prepare("UPDATE affectations SET trajet_id = ? WHERE affectation_id = ?");
            $stmt->execute([$trajet_id, $affectation_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO affectations (conducteur_id, trajet_id) VALUES (?, ?)");
            $stmt->execute([$conducteur_id, $trajet_id]);
        }
        header("Location: index_conducteur.php?msg=Trajet+affecté+avec+succès.");
        exit;
    }
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM conducteurs WHERE conducteur_id = ?");
$stmt->execute([$id]);
$conducteur = $stmt->fetch();
if (!$conducteur) { header("Location: index_conducteur.php"); exit; }

$current = $pdo->prepare("SELECT a.*, m.immatriculation, m.marque FROM affectations a LEFT JOIN motos m ON m.moto_id = a.moto_id WHERE a.conducteur_id = ? ORDER BY a.affectation_id DESC LIMIT 1");
$current->execute([$id]);
$currentAffectation = $current->fetch();

$availableTrajets = $pdo->query("SELECT * FROM trajets WHERE trajet_id NOT IN (SELECT trajet_id FROM affectations WHERE trajet_id IS NOT NULL) ORDER BY depart ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Affecter un trajet</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f8fafc;font-size:13px;}
    .card{border:none;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.09);}
    .card-header{background:linear-gradient(135deg,#1a3a6e,#2563ab);color:#fff;border-radius:12px 12px 0 0!important;}
    .form-label{font-weight:600;font-size:12px;}
    </style>
</head>
<body>
<div class="container py-4">
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card">
    <div class="card-header py-3"><h5 class="mb-0">🗺️ Affecter un trajet — Conducteur #<?= $conducteur['conducteur_id'] ?></h5></div>
    <div class="card-body p-4">
        <p class="mb-3">
            <strong>Nom :</strong> <?= htmlspecialchars($conducteur['nom']) ?>
            <strong>Prénom :</strong> <?= htmlspecialchars($conducteur['prenoms']) ?>
        </p>

        <?php if (empty($currentAffectation) || empty($currentAffectation['moto_id'])): ?>
        <div class="alert alert-warning py-2">
            Avant d'affecter un trajet, il faut d'abord affecter une moto à ce conducteur.
        </div>
        <?php else: ?>
        <div class="alert alert-info py-2">
            Moto actuelle : <strong><?= htmlspecialchars($currentAffectation['immatriculation']) ?></strong> (<?= htmlspecialchars($currentAffectation['marque']) ?>)
        </div>
        <?php endif; ?>

        <?php if (empty($availableTrajets)): ?>
            <div class="alert alert-warning py-2">Aucun trajet disponible pour l'affectation.</div>
        <?php elseif (empty($currentAffectation) || empty($currentAffectation['moto_id'])): ?>
            <div class="alert alert-secondary py-2">Sélectionnez d'abord une moto avant de choisir un trajet.</div>
        <?php else: ?>
        <form action="affecter_trajet.php" method="POST">
            <input type="hidden" name="conducteur_id" value="<?= $conducteur['conducteur_id'] ?>">
            <input type="hidden" name="affectation_id" value="<?= intval($currentAffectation['affectation_id']) ?>">
            <div class="mb-3">
                <label class="form-label">Choisir un trajet <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm" name="trajet_id" required>
                    <option value="">— Sélectionnez un trajet —</option>
                    <?php foreach ($availableTrajets as $trajet): ?>
                    <option value="<?= $trajet['trajet_id'] ?>">
                        <?= htmlspecialchars($trajet['depart'] . ' → ' . $trajet['arrivee'] . ' (' . $trajet['tarif'] . ')') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-4">✔ Affecter</button>
                <a href="index_conducteur.php" class="btn btn-secondary btn-sm">Annuler</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
</div></div></div>
<script src="js/bootstrap.bundle.min.js"></script>
</body></html>
