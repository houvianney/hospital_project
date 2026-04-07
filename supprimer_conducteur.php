<?php
include "connexion.php";
require "auth_check.php";  
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM conducteurs WHERE conducteur_id = ?");
$stmt->execute([$id]);
header("Location: index_conducteur.php?msg=Conducteur+supprimé+avec+succès.");
exit;
?>