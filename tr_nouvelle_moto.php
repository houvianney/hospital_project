<?php
include "connexion.php";
require "auth_check.php";  
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: nouvelle_moto.php"); exit; }

$ima    = trim($_POST['ima'] ?? '');
$marque = trim($_POST['marque'] ?? '');
$annee  = trim($_POST['annee'] ?? '');

try {
    $stmt = $pdo->prepare("INSERT INTO motos (immatriculation,marque,annee) VALUES (?, ?, ?)");
    $stmt->execute([$ima, $marque, $annee]);
    header("Location: index_moto.php?msg=Moto+enregistrée+avec+succès.");
} catch (PDOException $e) {
    header("Location: nouvelle_moto.php?erreur=" . urlencode($e->getMessage()));
}
exit;
?>