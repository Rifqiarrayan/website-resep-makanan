<?php
session_start(); // Mulai sesi
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null; // Set default jika tidak ada sesi
}

// Query untuk resep user
$stmt = $conn->prepare("SELECT * FROM recipes WHERE user_id = ? OR visibility = 'public'");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="favorites.js"></script>
    <title>Miyami Recipe Corner</title>
</head>
<body>

<!-- Navbar -->
<nav class="navbar sticky">
    <div class="containerr">
        <div class="logo-container">
            <img src="logo/logo resep.png" style="width: 100px; height: 100px;" alt="Logo" class="logo-image">
            <h1>Miyami Recipe</h1>
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null): ?>
                <a href="upload_recipe.php" class="nav-link">Tulis Resep</a>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero-section">
    <h2>Selamat Datang di Miyami Recipe</h2>
    <p>Temukan berbagai resep makanan yang lezat dan mudah untuk dibuat. Bergabunglah dengan komunitas kami dan bagikan resep favoritmu!</p>
</div>

<div class="recipes-container">
    <?php
    // Resep default
    $defaultRecipes = [
        ["id" => 1, "title" => "Nasi Goreng Sederhana", "image" => "nasi goreng.jpg", "steps" => "Panaskan minyak, tambahkan bawang putih, tambahkan nasi, bumbui, dan goreng hingga matang."],
        ["id" => 2, "title" => "Mie Goreng Pedas", "image" => "mie goreng.jpg", "steps" => "Masak mie, tumis bawang, tambahkan cabai, campurkan mie, dan goreng hingga rata."],
        ["id" => 3, "title" => "Sate Ayam Bumbu Kacang", "image" => "sate ayam.jpg", "steps" => "Tusuk ayam, bakar hingga matang, sajikan dengan bumbu kacang."],
        ["id" => 4, "title" => "Ayam Goreng Kremes", "image" => "ayam goreng.jpg", "steps" => "Goreng ayam hingga renyah, tambahkan kremesan, dan sajikan."],
        ["id" => 5, "title" => "Sop Buntut", "image" => "sop buntut.jpg", "steps" => "Rebus buntut sapi, tambahkan bumbu dan sayur, masak hingga empuk."],
        ["id" => 6, "title" => "Nasi Uduk", "image" => "nasi uduk.jpg", "steps" => "Masak nasi dengan santan dan bumbu, sajikan dengan lauk."],
        ["id" => 7, "title" => "Soto Ayam", "image" => "soto ayam.jpg", "steps" => "Rebus ayam, masak kuah soto, tambahkan bahan pelengkap."],
        ["id" => 8, "title" => "Gado-Gado", "image" => "gado gado.jpg", "steps" => "Siapkan sayur dan bumbu kacang, sajikan bersama lontong."],
        ["id" => 9, "title" => "Bakso Sapi", "image" => "bakso sapi.jpg", "steps" => "Campur daging dan bumbu, bentuk bola, masak hingga matang."],
        // Resep lainnya...
    ];

    // Tampilkan resep default
    foreach ($defaultRecipes as $recipe): ?>
        <div class="recipe-card">
            <img src="uploads/<?php echo htmlspecialchars($recipe['image']); ?>" class="recipe-image" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
            <div class="card-content">
                <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars(substr($recipe['steps'], 0, 100))) . '...'; ?></p>
                <a href="view_recipe.php?id=<?php echo $recipe['id']; ?>" class="view-button">Lihat Resep</a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null): ?>
                    <button class="favorite-btn" data-recipe-id="<?php echo $recipe['id']; ?>" onclick="toggleFavorite(<?php echo $recipe['id']; ?>)">
                        <span class="icon">❤️</span> Favorite
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Tampilkan resep user -->
    <?php
    while ($row = $result->fetch_assoc()):
        $isOwner = ((int) $_SESSION['user_id'] === (int) $row['user_id']);
    ?>
        <div class="recipe-card" data-id="<?php echo $row['id']; ?>">
            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="recipe-image" alt="<?php echo htmlspecialchars($row['title']); ?>">
            <div class="card-content">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars(substr($row['steps'], 0, 100))) . '...'; ?></p>
                <a href="view_recipe.php?id=<?php echo $row['id']; ?>" class="view-button">Lihat Resep</a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null): ?>
                    <button class="favorite-btn" data-recipe-id="<?php echo $row['id']; ?>" onclick="toggleFavorite(<?php echo $row['id']; ?>)">
                        <span class="icon">❤️</span> Favorite
                    </button>
                <?php endif; ?>

                <!-- Tampilkan tombol Edit/Delete jika user adalah pemilik -->
                <?php if ($isOwner): ?>
                    <div class="button-group">
                        <button class="btn-edit" onclick="window.location.href='edit_recipe.php?id=<?php echo $row['id']; ?>'">Edit</button>
                        <button class="btn-delete" onclick="deleteRecipe(<?php echo $row['id']; ?>)">Delete</button>
        <style>
            /* Tombol Edit */
.btn-edit {
    background-color: #4CAF50; /* Hijau */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Efek hover untuk tombol Edit */
.btn-edit:hover {
    background-color: #45a049;
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

/* Tombol Delete */
.btn-delete {
    background-color: #F44336; /* Merah */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Efek hover untuk tombol Delete */
.btn-delete:hover {
    background-color: #e53935;
    transform: translateY(-2px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

/* Efek saat tombol di klik */
button:active {
    transform: translateY(2px); /* Menurunkan tombol sedikit saat di klik */
}
            </style>
    </div>
<?php endif; ?>
        </div>
    </div>
<?php endwhile; ?>


<!-- Footer Section -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-about">
            <h3>Miyami Recipe</h3>
            <p>Your destination for delicious recipes, culinary inspiration, and cooking tips. Join our community and start sharing your favorite dishes today!</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
        <div class="footer-social">
            <h4>Follow Us</h4>
            <a href="#" target="_blank">
                <img src="logo/logo instagram.png" alt="Instagram" style="width: 30px; height: 30px;"></a>
            <a href="#" target="_blank">
                <img src="logo/logo whatsapp.png" alt="Whatsapp" style="width: 30px; height: 30px;"></a>
            <a href="#" target="_blank">
                <img src="logo/logo youtube.png" alt="Youtube" style="width: 30px; height: 30px;"></a>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2024 Miyami Recipe. All Rights Reserved.</p>
    </div>
</footer>

<script>
function deleteRecipe(recipeId) {
    if (confirm('Apakah Anda yakin ingin menghapus resep ini?')) {
        window.location.href = 'delete_recipe.php?id=' + recipeId;
    }
}
</script>
</html>