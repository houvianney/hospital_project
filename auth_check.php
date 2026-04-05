<?php
// auth_check.php
// Inclure ce fichier en HAUT de chaque page protégée
// Usage : require "auth_check.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Optionnel : vérifier le rôle admin
// if ($_SESSION["user_role"] !== "admin") {
//     header("Location: index.php");
//     exit();
// }
?>
