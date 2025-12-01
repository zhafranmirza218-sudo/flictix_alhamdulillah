<?php
session_start();
include 'config.php';

// Get movies from database
$movies_query = "SELECT * FROM movies ORDER BY created_at DESC LIMIT 20";
$movies_result = $conn->query($movies_query);
$movies = $movies_result->fetch_all(MYSQLI_ASSOC);

// Get user ID if logged in
$user_id = $_SESSION['user_id'] ?? null;

// Check if movies are in user's watchlist
$watchlist_movies = [];
if ($user_id) {
    $watchlist_query = "SELECT movie_id FROM watchlist WHERE user_id = ?";
    $stmt = $conn->prepare($watchlist_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $watchlist_result = $stmt->get_result();
    while ($row = $watchlist_result->fetch_assoc()) {
        $watchlist_movies[$row['movie_id']] = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flictix - Daftar Film</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="navbar-left">
            <div class="logo">
                <span>🎬</span>
                <span>FLICTIX</span>
            </div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#films">Films</a></li>
                <li><a href="#popular">Popular</a></li>
                <li><a href="list.php">My List</a></li>
            </ul>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-welcome">Welcome, <?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="logout-btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero">
        <div class="hero-content">
            <h1>Dendam Malam Kelam</h1>
            <p>Fueled by his lust and greed, a husband murders his wife — but when her body vanishes from the morgue, paranoia mounts and haunting signs emerge.</p>
            <div class="hero-buttons">
                <button class="btn btn-play">▶ Play</button>
                <button class="btn btn-info">ℹ More Info</button>
            </div>
        </div>
    </div>

    <div class="content">
        <section class="section">
            <h2 class="section-title">Film Terbaru</h2>
            <div class="movie-row">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card" style="background-image: url('<?php echo !empty($movie['thumb']) ? htmlspecialchars($movie['thumb']) : 'https://via.placeholder.com/300x450/333333/FFFFFF?text=No+Image'; ?>');">
                        <div class="movie-overlay">
                            <div class="movie-actions">
                                <?php if ($user_id): ?>
                                    <form method="POST" action="add_watchlist.php" class="watchlist-form">
                                        <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                        <button type="submit" class="watchlist-btn <?php echo isset($watchlist_movies[$movie['id']]) ? 'in-watchlist' : ''; ?>" 
                                                title="<?php echo isset($watchlist_movies[$movie['id']]) ? 'Remove from Watchlist' : 'Add to Watchlist'; ?>">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="watchlist-btn" title="Login to add to watchlist">
                                        <i class="fas fa-bookmark"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="movie-info">
                            <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                            <div class="movie-meta"><?php echo $movie['year']; ?> • <?php echo $movie['duration']; ?> mins</div>
                            <div class="movie-description"><?php echo htmlspecialchars(substr($movie['description'], 0, 100) . '...'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Additional sections can be added here with different movie categories -->
        <section class="section">
            <h2 class="section-title">Semua Film</h2>
            <div class="movie-row">
                <?php 
                // Get all movies for the second section
                $all_movies_query = "SELECT * FROM movies ORDER BY year DESC LIMIT 12";
                $all_movies_result = $conn->query($all_movies_query);
                $all_movies = $all_movies_result->fetch_all(MYSQLI_ASSOC);
                
                foreach ($all_movies as $movie): ?>
                    <div class="movie-card" style="background-image: url('<?php echo !empty($movie['thumb']) ? htmlspecialchars($movie['thumb']) : 'https://via.placeholder.com/300x450/333333/FFFFFF?text=No+Image'; ?>');">
                        <div class="movie-overlay">
                            <div class="movie-actions">
                                <?php if ($user_id): ?>
                                    <form method="POST" action="add_to_watchlist.php" class="watchlist-form">
                                        <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                                        <button type="submit" class="watchlist-btn <?php echo isset($watchlist_movies[$movie['id']]) ? 'in-watchlist' : ''; ?>" 
                                                title="<?php echo isset($watchlist_movies[$movie['id']]) ? 'Remove from Watchlist' : 'Add to Watchlist'; ?>">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="watchlist-btn" title="Login to add to watchlist">
                                        <i class="fas fa-bookmark"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="movie-info">
                            <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                            <div class="movie-meta"><?php echo $movie['year']; ?> • <?php echo $movie['duration']; ?> mins</div>
                            <div class="movie-description"><?php echo htmlspecialchars(substr($movie['description'], 0, 100) . '...'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <script>
        const navbar = document.getElementById('navbar');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        document.querySelectorAll('.movie-card').forEach(card => {
            card.addEventListener('click', (e) => {
                // Don't trigger if click is on watchlist button
                if (!e.target.closest('.watchlist-btn') && !e.target.closest('.watchlist-form')) {
                    alert('Film akan diputar!');
                }
            });
        });

        document.querySelector('.btn-play').addEventListener('click', (e) => {
            e.stopPropagation();
            alert('Memutar film...');
        });

        document.querySelector('.btn-info').addEventListener('click', (e) => {
            e.stopPropagation();
            alert('Menampilkan informasi lebih lanjut...');
        });

        // Watchlist button animation
        document.querySelectorAll('.watchlist-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.classList.contains('in-watchlist')) {
                    this.classList.remove('in-watchlist');
                    this.style.animation = 'pulse-remove 0.5s';
                } else {
                    this.classList.add('in-watchlist');
                    this.style.animation = 'pulse-add 0.5s';
                }
                
                setTimeout(() => {
                    this.style.animation = '';
                }, 500);
            });
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse-add {
                0% { transform: scale(1); }
                50% { transform: scale(1.3); }
                100% { transform: scale(1); }
            }
            @keyframes pulse-remove {
                0% { transform: scale(1); }
                50% { transform: scale(0.7); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>