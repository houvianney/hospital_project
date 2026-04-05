<?php
require "connexion.php";

// Si déjà connecté, rediriger
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $mdp   = trim($_POST["mdp"]   ?? "");

    if (empty($email) || empty($mdp)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        // Chercher l'utilisateur par email
        $stmt = $pdo->prepare(
            "SELECT * FROM utilisateurs WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user["mot_de_passe"])) {
            // Connexion réussie — créer la session
            $_SESSION["user_id"]  = $user["utilisateur_id"];
            $_SESSION["user_nom"] = $user["nom"] . " " . $user["prenom"];
            $_SESSION["user_role"] = $user["role"];

            header("Location: index.php");
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { background: #f5f6fa; }
        .auth-card {
            max-width: 420px;
            margin: 80px auto;
            border-top: 3px solid #3b5bdb;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm auth-card">
        <div class="card-body p-4">

            <h4 class="fw-bold mb-1">Connexion</h4>
            <p class="text-muted small mb-4">
                Pas encore de compte ?
                <a href="register.php">S'inscrire</a>
            </p>

            <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET["success"])): ?>
            <div class="alert alert-success">
                Compte créé avec succès. Connectez-vous.
            </div>
            <?php endif; ?>

            <form method="POST" novalidate>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="exemple@email.com" required autofocus>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Mot de passe *</label>
                    <input type="password" name="mdp" class="form-control"
                           placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Se connecter
                </button>

            </form>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
