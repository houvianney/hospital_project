<?php
include "connexion.php";
require "auth_check.php";  
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM motos WHERE moto_id = ?");
$stmt->execute([$id]);
header("Location: index_moto.php?msg=Moto+supprimée+avec+succès.");
exit;
?>