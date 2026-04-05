<?php
require "connexion.php";

 // ← Ces 2 lignes EN PREMIER, avant tout HTML

$erreur  = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom      = trim($_POST["nom"]      ?? "");
    $prenom   = trim($_POST["prenom"]   ?? "");
    $email    = trim($_POST["email"]    ?? "");
    $mdp      = trim($_POST["mdp"]      ?? "");
    $mdp_conf = trim($_POST["mdp_conf"] ?? "");

    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } elseif (strlen($mdp) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($mdp !== $mdp_conf) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si email déjà utilisé
        $stmt = $pdo->prepare("SELECT utilisateur_id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            // Hacher le mot de passe (sécurité)
            $hash = password_hash($mdp, PASSWORD_DEFAULT);

            // Insérer en base
            $stmt = $pdo->prepare(
                "INSERT INTO utilisateurs (nom, prenoms, email, mot_de_passe)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$nom, $prenom, $email, $hash]);

            $message = "Compte créé avec succès ! Vous pouvez vous connecter.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { background: #f5f6fa; }
        .auth-card {
            max-width: 480px;
            margin: 60px auto;
            border-top: 3px solid #3b5bdb;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm auth-card">
        <div class="card-body p-4">

            <h4 class="fw-bold mb-1">Créer un compte</h4>
            <p class="text-muted small mb-4">
                Déjà inscrit ?
                <a href="login.php">Se connecter</a>
            </p>

            <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Nom *</label>
                        <input type="text" name="nom" class="form-control"
                               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                               placeholder="DUPONT" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Prénom *</label>
                        <input type="text" name="prenom" class="form-control"
                               value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
                               placeholder="Jean" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="exemple@email.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mot de passe *</label>
                    <input type="password" name="mdp" class="form-control"
                           placeholder="Minimum 6 caractères" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirmer le mot de passe *</label>
                    <input type="password" name="mdp_conf" class="form-control"
                           placeholder="Répéter le mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Créer mon compte
                </button>

            </form>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
