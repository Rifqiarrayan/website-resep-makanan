<?php
include 'db.php';
session_start();

// Periksa apakah ada ID yang dikirimkan
if (!isset($_GET['id'])) {
    echo "Recipe not found.";
    exit();
}

// Ambil ID dari URL
$id = (int)$_GET['id'];

// Resep default
$defaultRecipes = [
    1 => ["title" => "Nasi Goreng Sederhana", "image" => "nasi goreng.jpg", "steps" => "Panaskan minyak, tambahkan bawang putih, tambahkan nasi, bumbui, dan goreng hingga matang."],
    2 => ["title" => "Mie Goreng Pedas", "image" => "mie goreng.jpg", "steps" => "Masak mie, tumis bawang, tambahkan cabai, campurkan mie, dan goreng hingga rata."],
    3 => ["title" => "Sate Ayam Bumbu Kacang", "image" => "sate ayam.jpg", "steps" => "Tusuk ayam, bakar hingga matang, sajikan dengan bumbu kacang."],
    4 => ["title" => "Ayam Goreng Kremes", "image" => "ayam goreng.jpg", "steps" => "Goreng ayam hingga renyah, tambahkan kremesan, dan sajikan."],
    5 => ["title" => "Sop Buntut", "image" => "sop buntut.jpg", "steps" => "Rebus buntut sapi, tambahkan bumbu dan sayur, masak hingga empuk."],
    6 => ["title" => "Nasi Uduk", "image" => "nasi uduk.jpg", "steps" => "Masak nasi dengan santan dan bumbu, sajikan dengan lauk."],
    7 => ["title" => "Soto Ayam", "image" => "soto ayam.jpg", "steps" => "Rebus ayam, masak kuah soto, tambahkan bahan pelengkap."],
    8 => ["title" => "Gado-Gado", "image" => "gado gado.jpg", "steps" => "Siapkan sayur dan bumbu kacang, sajikan bersama lontong."],
    9 => ["title" => "Bakso Sapi", "image" => "bakso sapi.jpg", "steps" => "Campur daging dan bumbu, bentuk bola, masak hingga matang."]
];

// Periksa apakah ID sesuai dengan resep default
if (array_key_exists($id, $defaultRecipes)) {
    $recipe = $defaultRecipes[$id];
    $title = $recipe['title'];
    $image = $recipe['image'];
    $steps = $recipe['steps'];
} else {
    // Cek apakah ID ada dalam database
    $stmt = $conn->prepare("SELECT title, image, steps FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($title, $image, $steps);
    $stmt->fetch();
    $stmt->close();

    // Jika data tidak ditemukan
    if (!$title) {
        echo "Recipe not found.";
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <script src="favorites.js"></script>
    <title><?php echo htmlspecialchars($title); ?> - Recipe Hub</title>
</head>
<body>
<div class="recipe-detail-container">
    <div class="recipe-detail-card">
        <h1 class="recipe-title"><?php echo htmlspecialchars($title); ?></h1>
        <img src="uploads/<?php echo htmlspecialchars($image); ?>" class="recipe-detail-image" alt="<?php echo htmlspecialchars($title); ?>">
        <div class="recipe-steps">
            <h2>Langkah-langkah</h2>
            <p><?php echo nl2br(htmlspecialchars($steps)); ?></p>
        </div>
        <button class="favorite-btn" data-recipe-id="<?php echo $id; ?>" onclick="toggleFavorite(<?php echo $id; ?>)">
            <span class="icon">❤️</span> Favorite
        </button>
        <a href="index.php" class="back-button">Back to Home</a>
    </div>
</div>
</body>
</html>