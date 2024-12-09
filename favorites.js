// Inisialisasi favorit dari localStorage atau array kosong
let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

// Fungsi untuk menambah atau menghapus resep favorit
function toggleFavorite(recipeId) {
    const index = favorites.indexOf(recipeId);
    if (index === -1) {
        // Tambah ke favorit
        favorites.push(recipeId);
        alert('Resep ditambahkan ke favorit!');
    } else {
        // Hapus dari favorit
        favorites.splice(index, 1);
        alert('Resep dihapus dari favorit!');
    }
    // Simpan perubahan ke localStorage
    localStorage.setItem('favorites', JSON.stringify(favorites));
    updateFavoriteButtons();
    saveFavoritesToServer();
}

// Fungsi untuk memperbarui status tombol favorit
function updateFavoriteButtons() {
    document.querySelectorAll('.favorite-btn').forEach(button => {
        const recipeId = parseInt(button.dataset.recipeId, 10);
        if (favorites.includes(recipeId)) {
            button.textContent = 'Unfavorite';
            button.classList.add('unfavorite');
            button.classList.remove('favorite');
        } else {
            button.textContent = 'Favorite';
            button.classList.add('favorite');
            button.classList.remove('unfavorite');
        }
    });
}

// Fungsi untuk menyimpan daftar favorit ke server
function saveFavoritesToServer() {
    fetch('save_favorites.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ favorites })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              console.log('Favorites saved successfully');
          } else {
              console.error('Failed to save favorites');
          }
      });
}

// Panggil fungsi saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    updateFavoriteButtons();
});