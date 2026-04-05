<?php
// modifier_statut.php — Faire avancer le statut d'une commande
include "connexion.php";
require "auth_check.php";  // ← Ces 2 lignes EN PREMIER, avant tout HTML
$id  = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT statut FROM consultations WHERE consultation_id = ?");
$stmt->execute([$id]);
$cmd = $stmt->fetch();

if (!$cmd) { header("Location: index.php"); exit; }

// Progression : en attente → en cours → servie
$prochain = match($cmd['statut']) {
    'En attente'              => 'En cours',
    'En cours' => 'Terminée',
    default                   => null
};

if ($prochain) {
    $stmt = $pdo->prepare("UPDATE consultations SET statut=? WHERE consultation_id=?");
    $stmt->execute([$prochain, $id]);
    header("Location: index.php?msg=Statut+mis+à+jour+: ".urlencode($prochain));
} else {
    header("Location: index.php");
}
exit;
?>