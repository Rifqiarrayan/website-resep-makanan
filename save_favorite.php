<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$favorites = $data['favorites'];

// Hapus favorit lama
$stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Tambahkan favorit baru
$stmt = $conn->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
foreach ($favorites as $recipe_id) {
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
}
$stmt->close();

echo json_encode(['success' => true]);
?>