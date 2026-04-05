<?php
include "connexion.php";
require "auth_check.php";  
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM consultations WHERE consultation_id = ?");
$stmt->execute([$id]);
header("Location: index.php?msg=Commande+supprimée.");
exit;
?>