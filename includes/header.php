<?php
/**
 * CineStream - Header & Navbar Component
 */
$currentGenre = isset($_GET['genre']) ? (int)$_GET['genre'] : null;
$currentFilter = isset($_GET['filter']) ? trim($_GET['filter']) : null;
$currentSearch = isset($_GET['search']) ? trim($_GET['search']) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="uhoLPZigkmGubHEpLD4OGRBcoPWJ06eIolpIyhJjwxk" />
    <?php
        $pageTitle = APP_NAME . ' - ' . APP_TAGLINE;

        if (isset($title) && !empty($title)) {
            $pageTitle = $title;

            if (isset($releaseYear) && $releaseYear !== 'N/A') {
                $pageTitle .= " ($releaseYear)";
            }

            $pageTitle .= ' - ' . APP_NAME;
        }
    ?>

    <title><?= htmlspecialchars($pageTitle); ?></title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Owl Carousel 2 CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    <!-- Custom CineStream CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark-theme text-light">

    <!-- Header / Navbar CineStream -->
    <header class="fixed-top header-navbar">
        <nav class="navbar navbar-expand-lg navbar-dark container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-3" href="index.php">
                <span class="brand-badge"><i class="bi bi-play-circle-fill text-danger"></i></span>
                <span class="brand-name">Cine<span class="text-danger">Stream</span></span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-light"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Navigation Links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link <?= (empty($currentGenre) && empty($currentFilter) && empty($currentSearch)) ? 'active' : ''; ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>

                    <!-- Dropdown Genre -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (!empty($currentGenre)) ? 'active text-danger' : ''; ?>" href="#" id="genreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid me-1"></i> Genre
                            <?php if (!empty($currentGenre) && isset(GENRES_LIST[$currentGenre])): ?>
                                <span class="badge bg-danger ms-1"><?= htmlspecialchars(GENRES_LIST[$currentGenre]); ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark custom-dropdown shadow" aria-labelledby="genreDropdown">
                            <?php foreach (GENRES_LIST as $id => $name): ?>
                                <li>
                                    <a class="dropdown-item <?= ($currentGenre === $id) ? 'active' : ''; ?>" href="index.php?genre=<?= $id; ?>">
                                        <?= htmlspecialchars($name); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <!-- Dropdown Film -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (!empty($currentFilter)) ? 'active text-danger' : ''; ?>" href="#" id="filmDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-film me-1"></i> Film
                            <?php if ($currentFilter === 'popular'): ?>
                                <span class="badge bg-danger ms-1">Popular</span>
                            <?php elseif ($currentFilter === 'top_rated'): ?>
                                <span class="badge bg-danger ms-1">Top Rated</span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark custom-dropdown shadow" aria-labelledby="filmDropdown">
                            <li>
                                <a class="dropdown-item <?= ($currentFilter === 'popular') ? 'active' : ''; ?>" href="index.php?filter=popular">
                                    <i class="bi bi-fire text-warning me-2"></i> Popular
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= ($currentFilter === 'top_rated') ? 'active' : ''; ?>" href="index.php?filter=top_rated">
                                    <i class="bi bi-star-fill text-warning me-2"></i> Top Rated
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Search Input & Button -->
                <form class="d-flex search-form my-2 my-lg-0" action="index.php" method="GET">
                    <div class="input-group">
                        <input class="form-control custom-search-input bg-dark border-secondary text-light" 
                               type="search" 
                               name="search" 
                               placeholder="Cari judul film..." 
                               aria-label="Search" 
                               value="<?= htmlspecialchars($currentSearch ?? ''); ?>"
                               required>
                        <button class="btn btn-danger px-3 custom-search-btn" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </nav>
    </header>
