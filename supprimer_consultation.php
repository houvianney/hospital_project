<?php
// supprimer_consultation.php
include "connexion.php";
require "auth_check.php";  // ← Ces 2 lignes EN PREMIER, avant tout HTML
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM consultations WHERE consultation_id = ?");
$stmt->execute([$id]);
header("Location: index.php?msg=Commande+supprimée.");
exit;
?>