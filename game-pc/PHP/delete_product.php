<?php
session_start();
include '../../private_gamepc/connection.php';

$components_id = $_GET["id"];

$stmt = $pdo->prepare("DELETE FROM components WHERE components_id = :components_id");
$stmt->bindParam(':components_id', $components_id, PDO::PARAM_INT);
$stmt->execute();

$_SESSION['success'] = "Product successfully deleted.";
header('Location: ../index.php?page=product_overview');
exit;
?>