<?php
include "connexion.php";
require "auth_check.php";  
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM trajets WHERE trajet_id = ?");
$stmt->execute([$id]);
header("Location: index_trajet.php?msg=Trajet+supprimé+avec+succès.");
exit;
?>