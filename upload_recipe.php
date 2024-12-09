<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $steps = $_POST['steps'];
    $user_id = $_SESSION['user_id'];

    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO recipes (user_id, title, image, steps) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $image, $steps);
    $stmt->execute();
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
    <!-- Summernote CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    <!-- Bootstrap CSS (required for Summernote) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="upload-container">
        <div class="upload-card">
            <h2 class="upload-title">Upload Resep Baru</h2>
            <form method="post" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <label for="title">Judul Resep</label>
                    <input type="text" name="title" id="title" placeholder="Judul Resep" required>
                </div>
                <div class="form-group">
                    <label for="image">Gambar Resep</label>
                    <input type="file" name="image" id="image" required>
                </div>
                <div class="form-group">
                    <label for="steps">Langkah-langkah</label>
                    <textarea name="steps" id="steps" placeholder="Deskripsikan langkah-langkah" rows="5" required></textarea>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="upload-button">Unggah Resep</button>
                    <a href="index.php" class="cancel-button">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (required for Summernote) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#steps').summernote({
                height: 300, // Set editor height
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
</body>
</html>