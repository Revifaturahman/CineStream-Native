<?php
/**
 * CineStream - Footer Component
 */
?>
    <!-- Footer Section -->
    <footer class="site-footer bg-darker text-secondary pt-5 pb-4 mt-5 border-top border-dark">
        <div class="container">
            <div class="row g-4">
                <!-- Col 1: Brand & About -->
                <div class="col-lg-5 col-md-6">
                    <div class="footer-brand mb-3">
                        <a class="d-flex align-items-center gap-2 fw-bold fs-3 text-light text-decoration-none" href="index.php">
                            <i class="bi bi-play-circle-fill text-danger"></i>
                            <span>Cine<span class="text-danger">Stream</span></span>
                        </a>
                    </div>
                    <p class="small text-muted mb-3">
                        <?= htmlspecialchars(APP_NAME); ?> adalah platform streaming film online modern berbasis PHP Native. Menyajikan informasi film terkini, ulasan, rating, dan trending terpopuler dari TMDB API.
                    </p>
                    <div class="d-flex gap-3 social-links">
                        <a href="#" class="text-secondary hover-danger fs-5" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-secondary hover-danger fs-5" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-secondary hover-danger fs-5" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-secondary hover-danger fs-5" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                        <a href="#" class="text-secondary hover-danger fs-5" aria-label="GitHub"><i class="bi bi-github"></i></a>
                    </div>
                </div>

                <!-- Col 2: Kategori Populer -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-light fw-bold mb-3 text-uppercase letter-spacing-1">Genre Populer</h6>
                    <div class="row g-2 small">
                        <div class="col-6">
                            <ul class="list-unstyled mb-0">
                                <li><a href="index.php?genre=28" class="footer-link">Action</a></li>
                                <li><a href="index.php?genre=12" class="footer-link">Adventure</a></li>
                                <li><a href="index.php?genre=16" class="footer-link">Animation</a></li>
                                <li><a href="index.php?genre=35" class="footer-link">Comedy</a></li>
                                <li><a href="index.php?genre=80" class="footer-link">Crime</a></li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul class="list-unstyled mb-0">
                                <li><a href="index.php?genre=18" class="footer-link">Drama</a></li>
                                <li><a href="index.php?genre=14" class="footer-link">Fantasy</a></li>
                                <li><a href="index.php?genre=27" class="footer-link">Horror</a></li>
                                <li><a href="index.php?genre=53" class="footer-link">Thriller</a></li>
                                <li><a href="index.php?genre=99" class="footer-link">Documentary</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Col 3: TMDB Attribution & Info -->
                <div class="col-lg-4 col-md-12">
                    <h6 class="text-light fw-bold mb-3 text-uppercase letter-spacing-1">Sumber Data</h6>
                    <p class="small text-muted mb-2">
                        Website ini menggunakan data dan gambar resmi dari <strong class="text-light">The Movie Database (TMDB) API</strong>.
                    </p>
                    <div class="tmdb-badge-container p-2 rounded bg-dark border border-secondary mb-3 d-inline-block">
                        <span class="badge bg-primary me-2">TMDB API</span>
                        <span class="small text-light">This product uses the TMDB API but is not endorsed or certified by TMDB.</span>
                    </div>
                </div>
            </div>

            <hr class="border-dark my-4">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start small text-muted mb-2 mb-md-0">
                    &copy; <?= date('Y'); ?> <strong class="text-light"><?= htmlspecialchars(APP_NAME); ?></strong>. All rights reserved. Built with PHP Native.
                </div>
                <div class="col-md-6 text-center text-md-end small">
                    <a href="index.php" class="text-secondary hover-light text-decoration-none me-3">Home</a>
                    <a href="index.php?filter=popular" class="text-secondary hover-light text-decoration-none me-3">Popular</a>
                    <a href="index.php?filter=top_rated" class="text-secondary hover-light text-decoration-none">Top Rated</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery 3.7.1 CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5.3 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Owl Carousel 2 JS CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <!-- CineStream Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
