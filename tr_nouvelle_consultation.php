<?php
include "connexion.php";
require "auth_check.php";  
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: nouvelle_consultation.php"); exit; }

$nom        = trim($_POST['nom']);
$motif      = trim($_POST['motif']);
$date_heure = $_POST['date_heure'];

try {
    $stmt = $pdo->prepare("INSERT INTO consultations (nom_patient,date_heure,motif) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $date_heure, $motif]);
    header("Location: index.php?msg=Commande+enregistrée+avec+succès.");
} catch (PDOException $e) {
    header("Location: nouvelle_consultation.php?erreur=" . urlencode($e->getMessage()));
}
exit;
?>