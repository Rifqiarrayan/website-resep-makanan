<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data profil pengguna
$stmt = $conn->prepare("SELECT username, email, bio, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_photo);
$stmt->fetch();
$stmt->close();

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_bio = isset($_POST['bio']) ? $_POST['bio'] : $bio;

    // Update profil
    if (!empty($_POST['password'])) {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $new_username, $new_email, $new_password, $new_bio, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_username, $new_email, $new_bio, $user_id);
    }

    $stmt->execute();
    $stmt->close();

    // Proses upload foto profil
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_name = time() . '_' . basename($_FILES['profile_photo']['name']);
        $target_file = $upload_dir . $file_name;

        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        // Validasi ukuran dan tipe file
        if ($_FILES['profile_photo']['size'] > $max_file_size) {
            die("Ukuran file terlalu besar. Maksimal 2 MB.");
        }

        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                // Simpan nama file ke database
                $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                $stmt->bind_param("si", $file_name, $user_id);
                $stmt->execute();
                $stmt->close();
            } else {
                die("Gagal memindahkan file ke folder upload.");
            }
        } else {
            die("Format file tidak didukung. Hanya JPG, JPEG, PNG, dan GIF.");
        }
    }

    header("Location: profile.php?message=Profil berhasil diperbarui");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
   
<nav class="navbar sticky">
    <div class="containerr">
        <div class="logo-container">
            <img src="logo/logo resep.png" style="width: 100px; height: 100px;" alt="Logo" class="logo-image">
            <h1 class="brand-name">Miyami Recipe</h1>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
    </div>
</nav>

     
    

    <div class="profile-container">
        <h1>Profil Pengguna</h1>
        <div class="profile-header">
            <div class="profile-photo">
                <img id="profile-image" src="uploads/<?php echo $profile_photo ? htmlspecialchars($profile_photo) : 'default-profile.png'; ?>" alt="Foto Profil">
            </div>
            <div class="profile-info">
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Biografi:</strong> <?php echo htmlspecialchars($bio ?: "Belum diatur"); ?></p>
                <button class="edit-profile-btn" onclick="document.getElementById('profile-form').style.display='block'">Edit Profil</button>
            </div>
        </div>

        <div id="profile-form" class="profile-form" style="display: none;">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Nama:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password (kosongkan jika tidak ingin mengubah):</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="form-group">
                    <label for="bio">Biografi:</label>
                    <textarea id="bio" name="bio" rows="3"><?php echo htmlspecialchars($bio); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="profile_photo">Foto Profil:</label>
                    <input type="file" name="profile_photo" accept="image/*">
                </div>
                <button type="submit" class="btn-submit">Perbarui Profil</button>
            </form>
        </div>
        <div class="favorite-recipes">
    <h2>Resep Favorit Saya</h2>
    </div>
</div>

    </div>
</body>
</html>
