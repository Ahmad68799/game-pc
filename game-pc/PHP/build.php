<?php
include '../../private_gamepc/connection.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: build.inc.php');
    exit;
}

$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$build_name = filter_input(INPUT_POST, 'build_name', FILTER_SANITIZE_STRING);
$component_ids = isset($_POST['components']) ? $_POST['components'] : [];
if (!$user_id || !$build_name || empty($component_ids)) {
    die("ERROR");
}

$total_price = 0;
$placeholders = rtrim(str_repeat('?,', count($component_ids)), ',');
try {
    $sql = "SELECT SUM(price) as total FROM components WHERE components_id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($component_ids));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['total'])) {
        $total_price = $result['total'];
    } else {
        die("EROR: CALCULADIDNT.");
    }
} catch(PDOException $e) {
    die("CALCULADIDNT: " . $e->getMessage());
}
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare("INSERT INTO builds (user_id, name, total_price) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $build_name, $total_price]);
    $build_id = $pdo->lastInsertId();

    $stmt_components = $pdo->prepare("INSERT INTO build_components (build_id, component_id) VALUES (?, ?)");
    foreach ($component_ids as $component_id) {
        if (!empty($component_id)) {
            $stmt_components->execute([$build_id, $component_id]);
        }
    }
    $pdo->commit();
    header("Location: ../index.php?page=profile");
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die("OUW: " . $e->getMessage());
}

?>
