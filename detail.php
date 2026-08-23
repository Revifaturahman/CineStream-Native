<?php
/**
 * CineStream - Detail Film Page
 * Menampilkan detail lengkap informasi film dan daftar pemain (cast) dari TMDB API
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/api.php';

// Ambil dan validasi ID Film
$movieId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

$movie = null;
$credits = null;
$castList = [];
$apiError = null;

// Periksa apakah API Key sudah dikonfigurasi
$isApiKeyConfigured = !empty(TMDB_API_KEY) && TMDB_API_KEY !== 'YOUR_TMDB_API_KEY_HERE';

if ($movieId <= 0) {
    $apiError = 'ID Film tidak valid atau tidak ditemukan.';
} elseif ($isApiKeyConfigured) {
    // Ambil detail film dari TMDB
    $detailResponse = get_movie_detail($movieId);
    if (!empty($detailResponse['success']) && !empty($detailResponse['id'])) {
        $movie = $detailResponse;

        // Ambil daftar pemain / cast dari TMDB
        $creditsResponse = get_movie_credits($movieId);
        if (!empty($creditsResponse['success']) && !empty($creditsResponse['cast'])) {
            // Ambil maksimal 12 cast utama
            $castList = array_slice($creditsResponse['cast'], 0, 12);
        }
    } else {
        $apiError = $detailResponse['message'] ?? 'Film tidak ditemukan di basis data TMDB.';
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
                            Aplikasi CineStream memerlukan API Key resmi dari The Movie Database (TMDB) untuk memuat detail film.
                        </p>
                        <div class="bg-darker p-3 rounded-3 font-monospace small mb-3 border border-secondary">
                            1. Buka file <strong>.env</strong> di root folder proyek.<br>
                            2. Masukkan TMDB_API_KEY Anda.
                        </div>
                        <a href="index.php" class="btn btn-outline-warning">
                            <i class="bi bi-house-door me-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif (!empty($apiError) || empty($movie)): ?>
        <!-- Tampilan Error / Film Tidak Ditemukan -->
        <div class="container" style="margin-top: 120px; min-height: 60vh;">
            <div class="text-center py-5 bg-surface rounded-4 p-5 shadow">
                <i class="bi bi-film fs-1 text-danger mb-3 d-block"></i>
                <h3 class="text-light fw-bold mb-2">Film Tidak Ditemukan</h3>
                <p class="text-muted mb-4"><?= htmlspecialchars($apiError ?? 'Informasi film tidak dapat dimuat.'); ?></p>
                <a href="index.php" class="btn btn-danger px-4 py-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Tampilan Detail Film -->
        <?php
        $title = $movie['title'] ?? ($movie['original_title'] ?? 'Film');
        $backdropUrl = !empty($movie['backdrop_path']) ? get_backdrop_url($movie['backdrop_path']) : '';
        $posterUrl = get_poster_url($movie['poster_path'] ?? null);
        $rating = isset($movie['vote_average']) ? number_format($movie['vote_average'], 1) : '0.0';
        $voteCount = isset($movie['vote_count']) ? number_format($movie['vote_count']) : '0';
        $releaseDate = !empty($movie['release_date']) ? $movie['release_date'] : 'N/A';
        $releaseYear = !empty($movie['release_date']) ? substr($movie['release_date'], 0, 4) : 'N/A';
        
        // Format Runtime (Menit -> Jam & Menit)
        $runtimeFormatted = 'N/A';
        if (!empty($movie['runtime'])) {
            $hours = floor($movie['runtime'] / 60);
            $minutes = $movie['runtime'] % 60;
            $runtimeFormatted = ($hours > 0 ? "{$hours}j " : '') . "{$minutes}m";
        }
        ?>

        <!-- Detail Hero Section -->
        <section class="detail-hero" style="<?= !empty($backdropUrl) ? "background-image: url('{$backdropUrl}');" : ''; ?>">
            <div class="detail-overlay"></div>
            <div class="detail-overlay-radial"></div>

            <div class="container">
                <!-- Tombol Navigasi Kembali -->
                <div class="mb-4 position-relative" style="z-index: 5;">
                    <a href="javascript:history.back()" class="btn btn-outline-light btn-sm d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="row align-items-center g-4 g-lg-5">
                    <!-- Poster Film -->
                    <div class="col-md-4 col-lg-3 text-center text-md-start">
                        <div class="detail-poster-container">
                            <img src="<?= htmlspecialchars($posterUrl); ?>" 
                                 alt="<?= htmlspecialchars($title); ?>" 
                                 class="detail-poster-img"
                                 onerror="this.src='https://placehold.co/500x750/161824/ffffff?text=Poster+Unavailable'">
                        </div>
                    </div>

                    <!-- Informasi Film -->
                    <div class="col-md-8 col-lg-9">
                        <div class="detail-content">
                            <!-- Judul Film -->
                            <h1 class="detail-title text-white mb-2">
                                <?= htmlspecialchars($title); ?>
                                <span class="text-muted fw-normal fs-4 ms-1">(<?= $releaseYear; ?>)</span>
                            </h1>

                            <!-- Tagline jika ada -->
                            <?php if (!empty($movie['tagline'])): ?>
                                <p class="detail-tagline mb-3 text-danger-emphasis">
                                    "<?= htmlspecialchars($movie['tagline']); ?>"
                                </p>
                            <?php endif; ?>

                            <!-- Metadata Badges -->
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                <span class="meta-pill meta-pill-rating">
                                    <i class="bi bi-star-fill"></i> <?= $rating; ?>
                                    <small class="text-muted ms-1">(<?= $voteCount; ?> vote)</small>
                                </span>

                                <span class="meta-pill">
                                    <i class="bi bi-calendar3"></i> <?= htmlspecialchars($releaseDate); ?>
                                </span>

                                <span class="meta-pill">
                                    <i class="bi bi-clock"></i> <?= $runtimeFormatted; ?>
                                </span>

                                <?php if (!empty($movie['status'])): ?>
                                    <span class="meta-pill">
                                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($movie['status']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Genre Badges -->
                            <?php if (!empty($movie['genres'])): ?>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                                    <span class="small text-muted fw-bold me-1">GENRE:</span>
                                    <?php foreach ($movie['genres'] as $genre): ?>
                                        <a href="index.php?genre=<?= (int)$genre['id']; ?>" class="genre-pill">
                                            <?= htmlspecialchars($genre['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Tombol Aksi Nonton Sekarang -->
                            <div class="d-flex flex-wrap gap-3 mb-4">
                                <a href="#player" class="btn btn-danger btn-lg px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow btn-scroll-player">
                                    <i class="bi bi-play-fill fs-4"></i> Nonton Sekarang
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary btn-lg px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-house-door fs-5"></i> Ke Beranda
                                </a>
                            </div>

                            <!-- Sinopsis / Overview -->
                            <div class="detail-overview-box">
                                <h5 class="fw-bold text-light mb-2">
                                    <i class="bi bi-card-text text-danger me-2"></i>Sinopsis
                                </h5>
                                <p class="text-light-50 mb-0 lh-base" style="color: #cbd5e1;">
                                    <?= !empty($movie['overview']) ? nl2br(htmlspecialchars($movie['overview'])) : 'Sinopsis belum tersedia untuk film ini dalam bahasa Indonesia.'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Streaming Video Player Section (Sebelum Bagian Cast) -->
        <section id="player" class="player-section container py-5">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-2 border-bottom border-dark">
                <div>
                    <h2 class="section-title text-light mb-1">
                        <i class="bi bi-play-btn-fill text-danger me-2"></i>Nonton Streaming: <?= htmlspecialchars($title); ?>
                    </h2>
                    <p class="text-muted small mb-0">Pilih server pemutar jika video mengalami kendala saat dimuat.</p>
                </div>
                
                <!-- Server Switcher Options -->
                <div class="btn-group btn-group-sm server-switcher shadow-sm" role="group" aria-label="Server Pemutar">
                    <button type="button" class="btn btn-danger server-btn active" data-src="https://vidsrc.to/embed/movie/<?= (int)$movieId; ?>">
                        <i class="bi bi-hdd-network me-1"></i> Server 1 (HD)
                    </button>
                    <button type="button" class="btn btn-outline-secondary server-btn" data-src="https://autoembed.co/movie/tmdb/<?= (int)$movieId; ?>">
                        <i class="bi bi-hdd-network me-1"></i> Server 2 (Fast)
                    </button>
                    <button type="button" class="btn btn-outline-secondary server-btn" data-src="https://multiembed.mov/?video_id=<?= (int)$movieId; ?>&tmdb=1">
                        <i class="bi bi-hdd-network me-1"></i> Server 3 (Multi)
                    </button>
                </div>
            </div>

            <!-- Player Video Iframe Responsive 16:9 -->
            <div class="player-wrapper shadow-lg">
                <div class="ratio ratio-16x9 player-ratio-container">
                    <iframe id="videoPlayerFrame" 
                            src="https://vidsrc.to/embed/movie/<?= (int)$movieId; ?>" 
                            title="<?= htmlspecialchars($title); ?> - Video Player" 
                            allowfullscreen 
                            webkitallowfullscreen 
                            mozallowfullscreen 
                            allow="autoplay; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy">
                    </iframe>
                </div>
            </div>

            <!-- Info Bantuan Pemutar -->
            <div class="player-footer-info d-flex flex-wrap align-items-center justify-content-between mt-3 p-3 bg-surface rounded-3 border border-dark small text-muted">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill text-info fs-5"></i>
                    <span>Jika video buffering atau tidak dapat diputar, silakan beralih ke Server 2 atau Server 3 di atas.</span>
                </div>
                <div class="d-none d-md-block">
                    <span class="badge bg-dark border border-secondary text-secondary"><i class="bi bi-shield-check text-success me-1"></i>Secure Stream</span>
                </div>
            </div>
        </section>

        <!-- Informasi Pemain / Cast Section -->
        <section class="cast-section container py-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-dark">
                <h2 class="section-title text-light mb-0">Pemeran Utama (Cast)</h2>
            </div>

            <?php if (empty($castList)): ?>
                <div class="bg-surface rounded-4 p-4 text-center text-muted">
                    <i class="bi bi-people fs-2 mb-2 d-block"></i>
                    Informasi daftar pemeran tidak tersedia.
                </div>
            <?php else: ?>
                <div class="row g-3 g-md-4">
                    <?php foreach ($castList as $cast): ?>
                        <?php
                        $actorName = $cast['name'] ?? 'Pemeran';
                        $characterName = !empty($cast['character']) ? $cast['character'] : 'Karakter tidak diketahui';
                        $profilePhoto = get_profile_url($cast['profile_path'] ?? null);
                        ?>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="cast-card">
                                <div class="cast-photo-wrapper">
                                    <img src="<?= htmlspecialchars($profilePhoto); ?>" 
                                         alt="<?= htmlspecialchars($actorName); ?>" 
                                         loading="lazy"
                                         onerror="this.src='https://placehold.co/185x278/1f2333/a0a6b5?text=No+Photo'">
                                </div>
                                <div class="cast-card-body">
                                    <h4 class="cast-name" title="<?= htmlspecialchars($actorName); ?>">
                                        <?= htmlspecialchars($actorName); ?>
                                    </h4>
                                    <p class="cast-character mb-0" title="<?= htmlspecialchars($characterName); ?>">
                                        <?= htmlspecialchars($characterName); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    <?php endif; ?>

</main>

<?php
// Muat Footer
require_once __DIR__ . '/includes/footer.php';
?>
