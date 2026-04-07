<?php
include "connexion.php";
require "auth_check.php";  

$q = trim($_GET['q'] ?? '');
$params = [];
$where = '';
if ($q !== '') {
    $where = " WHERE c.nom LIKE :q OR c.prenoms LIKE :q OR c.zone LIKE :q OR c.statut LIKE :q OR c.numero_gilet LIKE :q";
    $params = [':q' => "%$q%"];
}

$sql = "SELECT c.*,
    (SELECT m.immatriculation FROM motos m JOIN affectations a ON a.moto_id = m.moto_id WHERE a.conducteur_id = c.conducteur_id ORDER BY a.affectation_id DESC LIMIT 1) AS moto_immatriculation,
    (SELECT CONCAT(t.depart, ' → ', t.arrivee) FROM trajets t JOIN affectations a ON a.trajet_id = t.trajet_id WHERE a.conducteur_id = c.conducteur_id ORDER BY a.affectation_id DESC LIMIT 1) AS trajet_label
    FROM conducteurs c" . $where;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des conducteurs</title>
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
        <span class="navbar-brand"> Gestion des conducteurs</span>
        <a href="index_conducteur.php" class="btn btn-light btn-sm fw-bold">
                Gestion des conducteurs
        </a>
        <a href="index_moto.php" class="btn btn-light btn-sm fw-bold">
                Gestion des motos
        </a>
        <a href="index_trajet.php" class="btn btn-light btn-sm fw-bold">
                Gestion des trajets
        </a>
        <form class="d-flex ms-3" method="GET" action="index_conducteur.php">
            <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Rechercher un conducteur" value="<?= htmlspecialchars($q) ?>">
            <button class="btn btn-outline-light btn-sm" type="submit">Recherche</button>
        </form>
        <div class="d-flex gap-2 align-items-center">
            <a href="nouveau_conducteur.php" class="btn btn-light btn-sm fw-bold">
                + Nouveau conducteur
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
        Résultats pour « <?= htmlspecialchars($q) ?> » — <?= count($rows) ?> conducteur(s).
    </div>
    <?php endif; ?>

    <?php
    // Compteurs par statut
    $cAttente = $cPrep = $cServie = 0;
    foreach ($rows as $r) {
        if ($r['statut'] === 'indisponible')              $cAttente++;
        elseif ($r['statut'] === 'en course') $cPrep++;
        else                                             $cServie++;
    }
    ?>

    <!-- Cartes statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat p-3">
                <div class="text-muted mb-1" style="font-size:11px;">TOTAL CONDUCTEURS</div>
                <div class="fw-bold fs-4"><?= count($rows) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3" style="border-left:4px solid #f59e0b!important;">
                <div class="text-muted mb-1" style="font-size:11px;">INDISPONIBLE</div>
                <div class="fw-bold fs-4 text-warning"><?= $cAttente ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3" style="border-left:4px solid #3b82f6!important;">
                <div class="text-muted mb-1" style="font-size:11px;">EN COURSE</div>
                <div class="fw-bold fs-4 text-primary"><?= $cPrep ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3" style="border-left:4px solid #22c55e!important;">
                <div class="text-muted mb-1" style="font-size:11px;">DISPONIBLE</div>
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
                    <th>ID</th>
            <th>Nom du conducteur</th>
            <th>Prénoms du conducteur</th>
            <th>Zone d'activité</th>
            <th>Numéro de gilet</th>
            <th>Statut</th>
            <th>Moto</th>
            <th>Trajet</th>
            <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">Aucun conducteurs.</td></tr>
            <?php else: foreach ($rows as $row):
                $badgeClass = match($row['statut']) {
                    'indisponible'              => 'badge-attente',
                    'en course' => 'badge-preparation',
                    default                   => 'badge-servie'
                };
            ?>
            <tr>
                <td class="text-muted">#<?= $row['conducteur_id'] ?></td>
                
                <td><span class="badge bg-dark"> <?= $row['nom'] ?></span></td>
                <td><span class="badge bg-dark"> <?= $row['prenoms'] ?></span></td>
                <td><span class="badge bg-dark"> <?= $row['zone'] ?></span></td>
                <td><span class="badge bg-dark"> <?= $row['numero_gilet'] ?></span></td>
                <!-- <td><?= date('d/m/Y H:i', strtotime($row['date_heure'])) ?></td> -->
                <td>
                    <span class="badge-statut <?= $badgeClass ?>">
                        <?= ucfirst($row['statut']) ?>
                    </span>
                </td>
                <td><span class="badge bg-dark"><?= $row['moto_immatriculation'] ?: '—' ?></span></td>
                <td><span class="badge bg-dark"><?= $row['trajet_label'] ?: '—' ?></span></td>
                <td>
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="affecter_moto.php?id=<?= $row['conducteur_id'] ?>" class="btn btn-info btn-sm">🏍️ Moto</a>
                        <a href="affecter_trajet.php?id=<?= $row['conducteur_id'] ?>" class="btn btn-secondary btn-sm">🗺️ Trajet</a>
                        <?php if ($row['statut'] !== 'Disponible'): ?>
                        <a href="modifier_statut.php?id=<?= $row['conducteur_id'] ?>" class="btn btn-outline-primary btn-sm">▶ Statut</a>
                        <?php endif; ?>
                        <a href="modifier_conducteur.php?id=<?= $row['conducteur_id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                        <a href="supprimer_conducteur.php?id=<?= $row['conducteur_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce conducteur ?')">🗑️</a>
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