<?php
include "connexion.php";
require "auth_check.php";  
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: nouveau_trajet.php"); exit; }

$depart   = trim($_POST['depart'] ?? '');
$arrivee = trim($_POST['arrivee'] ?? '');
$tarif    = trim($_POST['tarif'] ?? '');

try {
    $stmt = $pdo->prepare("INSERT INTO trajets (depart,arrivee,tarif) VALUES (?, ?, ?)");
    $stmt->execute([$depart, $arrivee, $tarif]);
    header("Location: index_trajet.php?msg=Trajet+enregistré+avec+succès.");
} catch (PDOException $e) {
    header("Location: nouveau_trajet.php?erreur=" . urlencode($e->getMessage()));
}
exit;
?>