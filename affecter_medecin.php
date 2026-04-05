<?php
// affecter_serveur.php — Affecter un serveur disponible à une commande en attente
include "connexion.php";
require "auth_check.php";  // ← Ces 2 lignes EN PREMIER, avant tout HTML

// POST : traitement affectation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_cmd  = intval($_POST['consultation_id']);
    $id_serv = intval($_POST['medecin_id']);
    $stmt = $pdo->prepare("UPDATE consultations SET medecin_id = ?, statut = 'En cours' WHERE consultation_id = ?");
    $stmt->execute([$id_serv, $id_cmd]);
    header("Location: index.php?msg=Serveur+affecté+avec+succès.");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM consultations WHERE consultation_id = ?");
$stmt->execute([$id]);
$cmd = $stmt->fetch();
if (!$cmd) { header("Location: index.php"); exit; }

$serveurs = $pdo->query("SELECT * FROM medecins WHERE disponible = 'Oui' ORDER BY nom ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Affecter un medecin</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f8fafc;font-size:13px;}
    .card{border:none;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.09);}
    .card-header{background:linear-gradient(135deg,#1a3a6e,#2563ab);color:#fff;border-radius:12px 12px 0 0!important;}
    </style>
</head>
<body>
<div class="container py-4">
<div class="row justify-content-center"><div class="col-lg-6">
<div class="card">
    <div class="card-header py-3">
        <h5 class="mb-0">👨‍🍳 Affecter un medecin — Consultation #<?= $id ?></h5>
    </div>
    <div class="card-body p-4">
        <p class="mb-3">
            <strong>nom du patient :</strong> <?= $cmd['nom_patient'] ?> &nbsp;|&nbsp;
            <strong>Motif :</strong> <?= htmlspecialchars($cmd['motif']) ?>
        </p>
        <form action="affecter_medecin.php" method="POST">
            <input type="hidden" name="consultation_id" value="<?= $id ?>">
            <div class="mb-3">
                <label class="form-label fw-bold" style="font-size:12px;">
                    Medecins disponible <span class="text-danger">*</span>
                </label>
                <select class="form-select form-select-sm" name="medecin_id" required>
                    <option value="">— Choisir un medecin —</option>
                    <?php foreach ($serveurs as $s): ?>
                    <option value="<?= $s['medecin_id'] ?>">
                        <?= htmlspecialchars($s['nom'].' '.$s['prenoms']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-4">✔ Affecter</button>
                <a href="index.php" class="btn btn-secondary btn-sm">Annuler</a>
            </div>
        </form>
    </div>
</div>
</div></div></div>
<script src="js/bootstrap.bundle.min.js"></script>
</body></html>