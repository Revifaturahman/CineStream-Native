<?php
/**
 * CineStream - Home Page
 * Halaman utama platform streaming film CineStream Native PHP
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/api.php';

// Ambil parameter filter dari URL
$searchQuery  = isset($_GET['search']) ? trim($_GET['search']) : '';
$genreId      = isset($_GET['genre']) && is_numeric($_GET['genre']) ? (int)$_GET['genre'] : null;
$filterType   = isset($_GET['filter']) ? trim($_GET['filter']) : '';

// Inisialisasi variabel penampung data
$trendingMovies = [];
$gridMovies     = [];
$heroMovie      = null;
$pageTitle      = 'Jelajahi Film Populer';
$activeBadge    = '';
$apiError       = null;

// Periksa apakah API Key sudah dikonfigurasi
$isApiKeyConfigured = !empty(TMDB_API_KEY) && TMDB_API_KEY !== 'YOUR_TMDB_API_KEY_HERE';

if ($isApiKeyConfigured) {
    // 1. Logika Pengambilan Data Grid Sesuai Request Pengguna
    if (!empty($searchQuery)) {
        // Mode Pencarian Film
        $searchResponse = get_search_movies($searchQuery);
        if (!empty($searchResponse['success'])) {
            $gridMovies = $searchResponse['results'] ?? [];
        } else {
            $apiError = $searchResponse['message'] ?? 'Gagal mencari film.';
        }
        $pageTitle = 'Hasil Pencarian: "' . htmlspecialchars($searchQuery) . '"';
        $activeBadge = 'Pencarian';
    } elseif (!empty($genreId) && isset(GENRES_LIST[$genreId])) {
        // Mode Filter Berdasarkan Genre
        $genreResponse = get_discover_movies($genreId);
        if (!empty($genreResponse['success'])) {
            $gridMovies = $genreResponse['results'] ?? [];
        } else {
            $apiError = $genreResponse['message'] ?? 'Gagal memuat film berdasarkan genre.';
        }
        $pageTitle = 'Kategori Genre: ' . htmlspecialchars(GENRES_LIST[$genreId]);
        $activeBadge = 'Genre: ' . GENRES_LIST[$genreId];
    } elseif ($filterType === 'popular') {
        // Mode Filter Popular
        $popularResponse = get_popular_movies();
        if (!empty($popularResponse['success'])) {
            $gridMovies = $popularResponse['results'] ?? [];
        } else {
            $apiError = $popularResponse['message'] ?? 'Gagal memuat film terpopuler.';
        }
        $pageTitle = 'Film Terpopuler (Popular Movies)';
        $activeBadge = 'Popular';
    } elseif ($filterType === 'top_rated') {
        // Mode Filter Top Rated
        $topRatedResponse = get_top_rated_movies();
        if (!empty($topRatedResponse['success'])) {
            $gridMovies = $topRatedResponse['results'] ?? [];
        } else {
            $apiError = $topRatedResponse['message'] ?? 'Gagal memuat film rating tertinggi.';
        }
        $pageTitle = 'Film Rating Tertinggi (Top Rated)';
        $activeBadge = 'Top Rated';
    } else {
        // Mode Home Default: Muat Trending Carousel & Discover Movies Grid
        $trendingResponse = get_trending_movies();
        if (!empty($trendingResponse['success'])) {
            $trendingMovies = $trendingResponse['results'] ?? [];
            if (!empty($trendingMovies)) {
                $heroMovie = $trendingMovies[0]; // Jadikan film trending #1 sebagai hero banner
            }
        }

        $discoverResponse = get_discover_movies();
        if (!empty($discoverResponse['success'])) {
            $gridMovies = $discoverResponse['results'] ?? [];
        } else {
            $apiError = $discoverResponse['message'] ?? 'Gagal memuat film discover.';
        }
        $pageTitle = 'Jelajahi Film Pilihan (Discover Movies)';
    }
}

// Muat Header & Navbar
require_once __DIR__ . '/includes/header.php';
?>

<main class="main-content">

    <?php if (!$isApiKeyConfigured): ?>
        <!-- Peringatan API Key Belum Dikonfigurasi -->
        <div class="container" style="margin-top: 100px; min-height: 60vh;">
            <div class="alert alert-warning border-0 shadow-lg p-4 rounded-4 bg-dark text-light border-start border-warning border-5">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                    <div>
                        <h4 class="fw-bold text-warning mb-2">TMDB API Key Diperlukan!</h4>
                        <p class="text-secondary mb-3">
                            Aplikasi CineStream memerlukan API Key resmi dari The Movie Database (TMDB) untuk menampilkan katalog film secara langsung.
                        </p>
                        <div class="bg-darker p-3 rounded-3 font-monospace small mb-3 border border-secondary">
                            <span class="text-muted"># Langkah konfigurasi:</span><br>
                            1. Buka file <strong>.env</strong> di root folder proyek.<br>
                            2. Ganti nilai <strong>TMDB_API_KEY</strong> dengan API key TMDB Anda:<br>
                            <span class="text-danger">TMDB_API_KEY</span>=<span class="text-info">your_tmdb_api_key_here</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Belum punya API Key? Dapatkan secara gratis di <a href="https://www.themoviedb.org/settings/api" target="_blank" rel="noopener noreferrer" class="text-danger fw-bold">themoviedb.org</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>

        <?php if (!empty($apiError)): ?>
            <div class="container pt-5 mt-5">
                <div class="alert alert-danger d-flex align-items-center gap-3 rounded-3" role="alert">
                    <i class="bi bi-x-circle-fill fs-4"></i>
                    <div>
                        <strong>Terjadi Kesalahan:</strong> <?= htmlspecialchars($apiError); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php 
        // Render Hero Banner & Trending Carousel hanya di halaman Home Utama (ketika tidak sedang filter/search)
        $isHomePage = empty($searchQuery) && empty($genreId) && empty($filterType);
        ?>

        <?php if ($isHomePage && !empty($heroMovie)): ?>
            <!-- Hero Banner Section -->
            <?php 
            $heroBackdrop = !empty($heroMovie['backdrop_path']) 
                ? TMDB_IMG_BACKDROP . $heroMovie['backdrop_path'] 
                : (!empty($heroMovie['poster_path']) ? TMDB_IMG_POSTER . $heroMovie['poster_path'] : '');
            ?>
            <section class="hero-banner" style="background-image: url('<?= htmlspecialchars($heroBackdrop); ?>');">
                <div class="hero-overlay"></div>
                <div class="hero-overlay-side"></div>
                <div class="container">
                    <div class="hero-content">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-danger px-3 py-2 text-uppercase fw-bold letter-spacing-1">
                                <i class="bi bi-fire me-1"></i> Trending #1 Minggu Ini
                            </span>
                            <?php if (!empty($heroMovie['vote_average'])): ?>
                                <span class="badge bg-dark border border-secondary text-warning px-2 py-2 fw-bold">
                                    <i class="bi bi-star-fill me-1"></i> <?= number_format($heroMovie['vote_average'], 1); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="hero-title text-white mb-3">
                            <?= htmlspecialchars($heroMovie['title'] ?? 'Film Pilihan'); ?>
                        </h1>
                        
                        <p class="hero-overview mb-4">
                            <?= htmlspecialchars($heroMovie['overview'] ?? 'Saksikan film pilihan terpopuler minggu ini dengan kualitas streaming terbaik di CineStream.'); ?>
                        </p>
                        
                        <div class="d-flex flex-wrap gap-3">
                            <a href="detail.php?id=<?= (int)$heroMovie['id']; ?>" class="btn btn-danger btn-lg px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow">
                                <i class="bi bi-play-fill fs-4"></i> Nonton Sekarang
                            </a>
                            <a href="detail.php?id=<?= (int)$heroMovie['id']; ?>" class="btn btn-outline-light btn-lg px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle fs-5"></i> Info Selengkapnya
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <div class="container <?= $isHomePage ? 'mt-4' : 'pt-5 mt-4'; ?>">

            <?php if ($isHomePage && !empty($trendingMovies)): ?>
                <!-- Trending Movies Carousel Section -->
                <section class="trending-section mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="section-title text-light mb-0">Trending Movies</h2>
                    </div>

                    <div class="trending-carousel-container">
                        <div class="owl-carousel owl-theme trending-carousel">
                            <?php foreach ($trendingMovies as $movie): ?>
                                <?php 
                                $posterUrl = get_poster_url($movie['poster_path'] ?? null);
                                $rating = isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : '0.0';
                                $releaseYear = !empty($movie['release_date']) ? substr($movie['release_date'], 0, 4) : 'N/A';
                                $movieId = (int)($movie['id'] ?? 0);
                                ?>
                                <div class="item">
                                    <a href="detail.php?id=<?= $movieId; ?>" class="movie-card-link">
                                        <div class="movie-card">
                                            <div class="movie-poster-wrapper">
                                                <span class="movie-badge-rating">
                                                    <i class="bi bi-star-fill me-1"></i><?= $rating; ?>
                                                </span>
                                                <img src="<?= htmlspecialchars($posterUrl); ?>" 
                                                     alt="<?= htmlspecialchars($movie['title'] ?? 'Poster Film'); ?>" 
                                                     loading="lazy"
                                                     onerror="this.src='https://placehold.co/500x750/161824/ffffff?text=Poster+Unavailable'">
                                                <div class="movie-card-overlay">
                                                    <span class="movie-play-btn">
                                                        <i class="bi bi-play-fill"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="movie-card-body">
                                                <h3 class="movie-title" title="<?= htmlspecialchars($movie['title'] ?? ''); ?>">
                                                    <?= htmlspecialchars($movie['title'] ?? 'Untitled'); ?>
                                                </h3>
                                                <div class="movie-meta">
                                                    <span><?= $releaseYear; ?></span>
                                                    <span class="badge bg-dark border border-secondary text-secondary">Trending</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Movies Grid Section (Discover / Genre / Filter / Search) -->
            <section class="movies-grid-section mb-5">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom border-dark">
                    <div>
                        <h2 class="section-title text-light mb-1"><?= $pageTitle; ?></h2>
                        <?php if (!empty($activeBadge)): ?>
                            <span class="badge bg-danger fs-6 mt-1"><?= $activeBadge; ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (!$isHomePage): ?>
                        <a href="index.php" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($gridMovies)): ?>
                    <div class="text-center py-5 my-4 bg-surface rounded-4 p-5">
                        <i class="bi bi-film fs-1 text-secondary mb-3 d-block"></i>
                        <h4 class="text-light fw-bold">Tidak ada film yang ditemukan</h4>
                        <p class="text-muted small mb-4">Silakan coba kata kunci pencarian lain atau pilih kategori genre yang tersedia.</p>
                        <a href="index.php" class="btn btn-danger px-4">
                            <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row g-3 g-md-4">
                        <?php foreach ($gridMovies as $movie): ?>
                            <?php 
                            $posterUrl = get_poster_url($movie['poster_path'] ?? null);
                            $rating = isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : '0.0';
                            $releaseYear = !empty($movie['release_date']) ? substr($movie['release_date'], 0, 4) : 'N/A';
                            $movieId = (int)($movie['id'] ?? 0);
                            ?>
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <a href="detail.php?id=<?= $movieId; ?>" class="movie-card-link">
                                    <div class="movie-card">
                                        <div class="movie-poster-wrapper">
                                            <span class="movie-badge-rating">
                                                <i class="bi bi-star-fill me-1"></i><?= $rating; ?>
                                            </span>
                                            <img src="<?= htmlspecialchars($posterUrl); ?>" 
                                                 alt="<?= htmlspecialchars($movie['title'] ?? 'Poster Film'); ?>" 
                                                 loading="lazy"
                                                 onerror="this.src='https://placehold.co/500x750/161824/ffffff?text=Poster+Unavailable'">
                                            <div class="movie-card-overlay">
                                                <span class="movie-play-btn">
                                                    <i class="bi bi-play-fill"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="movie-card-body">
                                            <h3 class="movie-title" title="<?= htmlspecialchars($movie['title'] ?? ''); ?>">
                                                <?= htmlspecialchars($movie['title'] ?? 'Untitled'); ?>
                                            </h3>
                                            <div class="movie-meta">
                                                <span><?= $releaseYear; ?></span>
                                                <span class="badge bg-dark border border-secondary text-secondary">Movie</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </div>

    <?php endif; ?>

</main>

<?php
// Muat Footer
require_once __DIR__ . '/includes/footer.php';
?>
