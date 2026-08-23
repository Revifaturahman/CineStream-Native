<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/api.php';

header('Content-Type: application/xml; charset=UTF-8');

// Menyimpan ID film agar tidak duplikat
$movieIds = [];

/**
 * Tambahkan film ke daftar sitemap
 */
function add_movies_to_sitemap($response) {
    global $movieIds;

    if (!empty($response['success']) && !empty($response['results'])) {
        foreach ($response['results'] as $movie) {
            if (!empty($movie['id'])) {
                $movieIds[(int)$movie['id']] = true;
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Trending
|--------------------------------------------------------------------------
*/
add_movies_to_sitemap(get_trending_movies());

/*
|--------------------------------------------------------------------------
| Popular Movies
|--------------------------------------------------------------------------
*/
for ($page = 1; $page <= 5; $page++) {
    add_movies_to_sitemap(get_popular_movies($page));
}

/*
|--------------------------------------------------------------------------
| Top Rated Movies
|--------------------------------------------------------------------------
*/
for ($page = 1; $page <= 5; $page++) {
    add_movies_to_sitemap(get_top_rated_movies($page));
}

/*
|--------------------------------------------------------------------------
| Discover Movies
|--------------------------------------------------------------------------
*/
for ($page = 1; $page <= 5; $page++) {
    add_movies_to_sitemap(get_discover_movies(null, $page));
}

/*
|--------------------------------------------------------------------------
| Generate XML Sitemap
|--------------------------------------------------------------------------
*/

echo '<?xml version="1.0" encoding="UTF-8"?>';

echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Homepage
echo '<url>';
echo '<loc>https://cinestream-gold.vercel.app/</loc>';
echo '</url>';

// Movie detail pages
foreach (array_keys($movieIds) as $movieId) {

    $url = 'https://cinestream-gold.vercel.app/detail.php?id=' . $movieId;

    echo '<url>';
    echo '<loc>' . htmlspecialchars(
        $url,
        ENT_XML1,
        'UTF-8'
    ) . '</loc>';
    echo '</url>';
}

echo '</urlset>';