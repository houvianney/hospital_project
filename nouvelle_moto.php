<?php
 include "connexion.php";

// require "auth_check.php"; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Nouvelle moto</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f8fafc; font-size:13px; }
        .card { border:none; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,.09); }
        .card-header { background:linear-gradient(135deg,#1a3a6e,#2563ab); color:#fff;
                        border-radius:12px 12px 0 0 !important; }
        .form-label { font-weight:600; font-size:12px; }
    </style>
</head>
<body>
<div class="container py-4">
<div class="row justify-content-center">
<div class="col-lg-7">

    <?php if (isset($_GET['erreur'])): ?>
    <div class="alert alert-danger py-2">⚠️ <?= htmlspecialchars($_GET['erreur']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header py-3">
            <h5 class="mb-0">➕ Nouvelle moto</h5>
        </div>
        <div class="card-body p-4">
            <form action="tr_nouvelle_moto.php" method="POST" enctype="multipart/form-data">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Immatriculation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm"
                               name="ima"  required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Marque<span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm"
                               name="marque" placeholder="---" required>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Année d'acquisition<span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm"
                               name="annee" required>
                    </div>
                    
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary btn-sm px-4">
                        💾 Enregistrer
                    </button>
                    <a href="index_moto.php" class="btn btn-secondary btn-sm">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div></div></div>
<script src="js/bootstrap.bundle.min.js"></script>
</body></html>