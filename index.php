<?php
include "connexion.php";
require "auth_check.php";  // ← Ces 2 lignes EN PREMIER, avant tout HTML

$sql = "SELECT c.*, s.nom AS s_nom, s.prenoms AS s_prenoms
        FROM consultations c
        LEFT JOIN medecins s ON c.medecin_id = s.medecin_id
        ORDER BY c.date_heure DESC";
$result = $pdo->query($sql);
$rows = $result->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des commandes</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f8fafc; font-size:13px; }
        .navbar-brand { font-weight:700; letter-spacing:1px; }
        .badge-attente    { background:#fef3c7; color:#92400e; }
        .badge-preparation{ background:#dbeafe; color:#1e40af; }
        .badge-servie     { background:#dcfce7; color:#15803d; }
        .badge-statut { border-radius:20px; padding:3px 10px;
                         font-size:11px; font-weight:600; }
        .photo-plat { width:55px; height:55px; object-fit:cover;
                       border-radius:8px; }
        .table thead th { background:#1a3a6e; color:#fff; font-size:12px;
                           text-transform:uppercase; letter-spacing:.5px; }
        .btn-sm { font-size:11px; }
        .card-stat { border:none; border-radius:10px;
                     box-shadow:0 2px 10px rgba(0,0,0,.07); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark" style="background:linear-gradient(135deg,#1a3a6e,#2563ab);">
    <div class="container-fluid px-4">
        <span class="navbar-brand"> Gestion des consultations</span>
        <div class="d-flex gap-2 align-items-center">
            <a href="nouvelle_consultation.php" class="btn btn-light btn-sm fw-bold">
                + Nouvelle consultation
            </a>
            <!-- <a href="serveurs.php" class="btn btn-outline-light btn-sm">
                👨‍🍳 Medecin
            </a> -->
            <span class="text-light me-3">
                Bienvenue, <?= htmlspecialchars($_SESSION["user_nom"]) ?> (<?= htmlspecialchars($_SESSION["user_role"]) ?>)
            </span>
             <a href="logout.php" class="btn btn-outline-light btn-sm">
                Déconnexion
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">

    <?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2">
        ✅ <?= htmlspecialchars($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php
    // Compteurs par statut
    $cAttente = $cPrep = $cServie = 0;
    foreach ($rows as $r) {
        if ($r['statut'] === 'En attente')              $cAttente++;
        elseif ($r['statut'] === 'En cours') $cPrep++;
        else                                             $cServie++;
    }
    ?>

    <!-- Cartes statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat p-3">
                <div class="text-muted mb-1" style="font-size:11px;">TOTAL CONSULTATIONS</div>
                <div class="fw-bold fs-4"><?= count($rows) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3" style="border-left:4px solid #f59e0b!important;">
                <div class="text-muted mb-1" style="font-size:11px;">EN ATTENTE</div>
                <div class="fw-bold fs-4 text-warning"><?= $cAttente ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3" style="border-left:4px solid #3b82f6!important;">
                <div class="text-muted mb-1" style="font-size:11px;">EN COURS</div>
                <div class="fw-bold fs-4 text-primary"><?= $cPrep ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3" style="border-left:4px solid #22c55e!important;">
                <div class="text-muted mb-1" style="font-size:11px;">TERMINEE</div>
                <div class="fw-bold fs-4 text-success"><?= $cServie ?></div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th><th>nom du patient</th>
                    <th>Date & Heure</th><th>Motif</th><th>Medecin</th><th>Statut</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Aucune consultation.</td></tr>
            <?php else: foreach ($rows as $row):
                $badgeClass = match($row['statut']) {
                    'En attente'              => 'badge-attente',
                    'En cours' => 'badge-preparation',
                    default                   => 'badge-servie'
                };
            ?>
            <tr>
                <td class="text-muted">#<?= $row['consultation_id'] ?></td>
                
                <td><span class="badge bg-dark">nom <?= $row['nom_patient'] ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($row['date_heure'])) ?></td>

                <td><strong><?= htmlspecialchars($row['motif']) ?></strong></td>
                <td>
                    <?php if ($row['s_nom']): ?>
                        <?= htmlspecialchars($row['s_nom'].' '.$row['s_prenoms']) ?>
                    <?php else: ?>
                        <a href="affecter_medecin.php?id=<?= $row['consultation_id'] ?>"
                           class="badge bg-warning text-dark text-decoration-none">
                            Affecter →
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge-statut <?= $badgeClass ?>">
                        <?= ucfirst($row['statut']) ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1 flex-wrap">
                        <?php if ($row['statut'] !== 'Terminée'): ?>
                        <a href="modifier_statut.php?id=<?= $row['consultation_id'] ?>"
                           class="btn btn-outline-primary btn-sm">▶ Statut</a>
                        <?php endif; ?>
                        <a href="modifier_consultation.php?id=<?= $row['consultation_id'] ?>"
                           class="btn btn-warning btn-sm">✏️</a>
                        <a href="supprimer_consultation.php?id=<?= $row['consultation_id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cette consultation ?')">🗑️</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
        </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>