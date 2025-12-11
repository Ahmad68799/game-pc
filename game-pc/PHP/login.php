<?php
session_start();
include '../../private_gamepc/connection.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';


if (empty($email) || empty($password)) {
    $_SESSION['alert'] = 'Enter both email address and password.';
    header('Location: ../index.php?page=login');
    exit();
}


$sql = 'SELECT user_id, role_id, username, password FROM users WHERE email = :email';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['role_id'] = $user['role_id'];
    $_SESSION['userid'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['notification_type'] = 'success';

    $roleNames = [
        1 => 'customer',
        2 => 'admin',
        3 => 'worker'
    ];
    $roleName = $roleNames[$user['role_id']] ?? 'unkown';

    $_SESSION['notification'] = 'Successfully logged in as ' . $roleName . '.';

    switch ($user['role_id']) {
        case 1:
            header('Location: ../index.php?page=home');
            break;
        case 2:
            header('Location: ../index.php?page=users_overview');
            break;
        case 3:
            header('Location: ../index.php?page=orders');
            break;
        default:
            header('Location: ../index.php?page=login');
    }
    exit();
}

// Verkeerd wachtwoord of email
$_SESSION['alert'] = 'email or password is incorrect please try again.';
header('Location: ../index.php?page=login');
exit();
?>
