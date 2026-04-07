<?php
include "connexion.php";
require "auth_check.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conducteur_id = intval($_POST['conducteur_id'] ?? 0);
    $moto_id = intval($_POST['moto_id'] ?? 0);

    if ($conducteur_id && $moto_id) {
        $stmt = $pdo->prepare("INSERT INTO affectations (conducteur_id, moto_id) VALUES (?, ?)");
        $stmt->execute([$conducteur_id, $moto_id]);
        header("Location: index_conducteur.php?msg=Moto+affectée+avec+succès.");
        exit;
    }
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM conducteurs WHERE conducteur_id = ?");
$stmt->execute([$id]);
$conducteur = $stmt->fetch();
if (!$conducteur) { header("Location: index_conducteur.php"); exit; }

$availableMotos = $pdo->query("SELECT * FROM motos WHERE moto_id NOT IN (SELECT moto_id FROM affectations WHERE moto_id IS NOT NULL) ORDER BY immatriculation ASC")->fetchAll();
$current = $pdo->prepare("SELECT a.*, m.immatriculation, m.marque FROM affectations a JOIN motos m ON m.moto_id = a.moto_id WHERE a.conducteur_id = ? ORDER BY a.affectation_id DESC LIMIT 1");
$current->execute([$id]);
$currentMoto = $current->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Affecter une moto</title>
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
    <div class="card-header py-3"><h5 class="mb-0">🏍️ Affecter une moto — Conducteur #<?= $conducteur['conducteur_id'] ?></h5></div>
    <div class="card-body p-4">
        <p class="mb-3">
            <strong>Nom :</strong> <?= htmlspecialchars($conducteur['nom']) ?>
            <strong>Prénom :</strong> <?= htmlspecialchars($conducteur['prenoms']) ?>
        </p>
        <?php if ($currentMoto): ?>
        <div class="alert alert-info py-2">
            Moto déjà affectée : <strong><?= htmlspecialchars($currentMoto['immatriculation']) ?></strong> (<?= htmlspecialchars($currentMoto['marque']) ?>)
        </div>
        <?php endif; ?>

        <?php if (empty($availableMotos)): ?>
            <div class="alert alert-warning py-2">Aucune moto disponible pour l'affectation.</div>
        <?php else: ?>
        <form action="affecter_moto.php" method="POST">
            <input type="hidden" name="conducteur_id" value="<?= $conducteur['conducteur_id'] ?>">
            <div class="mb-3">
                <label class="form-label">Choisir une moto <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm" name="moto_id" required>
                    <option value="">— Sélectionnez une moto —</option>
                    <?php foreach ($availableMotos as $moto): ?>
                    <option value="<?= $moto['moto_id'] ?>">
                        <?= htmlspecialchars($moto['immatriculation'] . ' — ' . $moto['marque']) ?>
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
