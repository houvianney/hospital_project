<?php
include "connexion.php";
require "auth_check.php";  
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: nouveau_conducteur.php"); exit; }

$nom        = trim($_POST['nom'] ?? '');
$prenom     = trim($_POST['prenom'] ?? '');
$zone       = trim($_POST['zone'] ?? '');
$numero     = trim($_POST['numero'] ?? '');

try {
    $stmt = $pdo->prepare("INSERT INTO conducteurs (nom,prenoms,zone,numero_gilet) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $zone, $numero]);
    header("Location: index_conducteur.php?msg=Conducteur+enregistré+avec+succès.");
} catch (PDOException $e) {
    header("Location: nouveau_conducteur.php?erreur=" . urlencode($e->getMessage()));
}
exit;
?>