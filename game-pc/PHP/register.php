<?php
session_start();

include '../../private_gamepc/connection.php';

$username = $_POST["username"];
$prefix = $_POST["prefix"];
$lastname = $_POST["lastname"];
$place = $_POST["place"];
$street = $_POST["street"];
$housenumber = $_POST["housenumber"];
$zipcode = $_POST["zipcode"];
$email = $_POST["email"];
$confirm_password = $_POST["confirm_password"];
$password = $_POST["password"];
$role = 1; //

if ($housenumber < 0 || $zipcode < 0) {
    $_SESSION['alert'] = 'Housnumber and Postal code most not be a negative number!';
    header('Location: ../index.php?page=register');
    exit;
}

$sql = "SELECT COUNT(*) FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $email]);
if ($stmt->fetchColumn() > 0) {
    $_SESSION['alert'] = 'E-mail already in use!';
    header('location: ../index.php?page=register');
    exit;
}


if ($password === $confirm_password) {
    // Hash het wachtwoord
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$sql = "
    INSERT INTO users (username, prefix, lastname, place, street, house_number, zip_code, email, password, role_id)
    VALUES (:username, :prefix, :lastname, :place, :street, :house_number, :zip_code, :email, :password, :role)
";

$stmt = $pdo->prepare($sql);

$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->bindParam(':prefix', $prefix, PDO::PARAM_STR);
$stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
$stmt->bindParam(':place', $place, PDO::PARAM_STR);
$stmt->bindParam(':street', $street, PDO::PARAM_STR);
$stmt->bindParam(':house_number', $housenumber, PDO::PARAM_STR);
$stmt->bindParam(':zip_code', $zipcode, PDO::PARAM_STR);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
$stmt->bindParam(':role', $role, PDO::PARAM_STR);

$stmt->execute();
header('Location: ../index.php?page=login');
} else {
    $_SESSION['alert'] = 'password does not match!';
    header('location: ../index.php?page=register');
    exit;
}