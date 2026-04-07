<?php
include "connexion.php";
require "auth_check.php";  

$q = trim($_GET['q'] ?? '');
$params = [];
$where = '';
if ($q !== '') {
    $where = " WHERE depart LIKE :q OR arrivee LIKE :q OR tarif LIKE :q";
    $params = [':q' => "%$q%"]; 
}

$sql = "SELECT * FROM trajets" . $where . " ORDER BY trajet_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des trajets</title>
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
        <span class="navbar-brand"> Gestion des trajets</span>
        <a href="index_conducteur.php" class="btn btn-light btn-sm fw-bold">
                Gestion des conducteurs
        </a>
        <a href="index_moto.php" class="btn btn-light btn-sm fw-bold">
                Gestion des motos
        </a>
        <a href="index_trajet.php" class="btn btn-light btn-sm fw-bold">
                Gestion des trajets
        </a>
        <form class="d-flex ms-3" method="GET" action="index_trajet.php">
            <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Rechercher un trajet" value="<?= htmlspecialchars($q) ?>">
            <button class="btn btn-outline-light btn-sm" type="submit">Recherche</button>
        </form>
        <div class="d-flex gap-2 align-items-center">
            <a href="nouveau_trajet.php" class="btn btn-light btn-sm fw-bold">
                + Nouveau trajet
            </a>
            <!-- <a href="serveurs.php" class="btn btn-outline-light btn-sm">
                👨‍🍳 Medecin
            </a> -->
            <span class="text-light me-3">
                Bienvenue, <?= htmlspecialchars($_SESSION["user_nom"] ?? 'Invité') ?> (<?= htmlspecialchars($_SESSION["user_role"] ?? 'invité') ?>)
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

    <?php if ($q !== ''): ?>
    <div class="mb-4 text-muted">
        Résultats pour « <?= htmlspecialchars($q) ?> » — <?= count($rows) ?> trajet(s).
    </div>
    <?php endif; ?>


    <!-- Tableau -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th><th>Point de départ</th>
                    <th>Point d'arrivée</th><th>Tarif</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">Aucun trajet.</td></tr>
            <?php else: foreach ($rows as $row):
                // $badgeClass = match($row['statut']) {
                //     'indisponible'              => 'badge-attente',
                //     'en course' => 'badge-preparation',
                //     default                   => 'badge-servie'
                // };
            ?>
            <tr>
                <td class="text-muted">#<?= $row['trajet_id'] ?></td>
                
                <td><span class="badge bg-dark"> <?= $row['depart'] ?></span></td>
                <td><span class="badge bg-dark"> <?= $row['arrivee'] ?></span></td>
                <td><span class="badge bg-dark"> <?= $row['tarif'] ?></span></td>

                
                
                <td>
                    <div class="d-flex gap-1 flex-wrap">
                        
                        <a href="modifier_trajet.php?id=<?= $row['trajet_id'] ?>"
                           class="btn btn-warning btn-sm">✏️</a>
                        <a href="supprimer_trajet.php?id=<?= $row['trajet_id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer ce trajet ?')">🗑️</a>
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