<?php
session_start();
include 'db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $recipeId = $_GET['id'];
    $userId = $_SESSION['user_id'];

    // Validasi apakah user adalah pemilik resep
    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $recipeId, $userId);

    if ($stmt->execute()) {
        header("Location: index.php?message=Resep berhasil dihapus");
    } else {
        header("Location: index.php?error=Gagal menghapus resep");
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php?error=Invalid request");
}
?>
