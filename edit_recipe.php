<?php
session_start();
include 'db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $recipeId = $_GET['id'];
    $userId = $_SESSION['user_id'];

    // Ambil data resep
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $recipeId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();

    if (!$recipe) {
        die("Resep tidak ditemukan atau Anda tidak memiliki akses.");
    }
} else {
    header("Location: index.php?error=Invalid request");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $steps = $_POST['steps'];

    $stmt = $conn->prepare("UPDATE recipes SET title = ?, steps = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $title, $steps, $recipeId, $userId);

    if ($stmt->execute()) {
        header("Location: index.php?message=Resep berhasil diperbarui");
    } else {
        echo "Gagal memperbarui resep.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="edit_recipe.css">
    <!-- Summernote CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
    <!-- Bootstrap CSS (required for Summernote) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit Resep</title>
</head>
<body>
    <div class="container">
        <div class="edit-form">
            <h1>Edit Resep</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Judul Resep:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="steps">Langkah-langkah:</label>
                    <textarea id="steps" name="steps" required><?php echo htmlspecialchars($recipe['steps']); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
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
