<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/api.php';

$movieIds = [];

/**
 * Tambahkan film ke daftar sitemap
 */
function add_movies_to_sitemap($response)
{
    global $movieIds;

    if (
        !empty($response['success']) &&
        !empty($response['results']) &&
        is_array($response['results'])
    ) {
        foreach ($response['results'] as $movie) {
            if (!empty($movie['id'])) {
                $movieIds[(int) $movie['id']] = true;
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Ambil daftar film
|--------------------------------------------------------------------------
*/

// Trending
add_movies_to_sitemap(get_trending_movies());

// Popular
for ($page = 1; $page <= 5; $page++) {
    add_movies_to_sitemap(get_popular_movies($page));
}

// Top Rated
for ($page = 1; $page <= 5; $page++) {
    add_movies_to_sitemap(get_top_rated_movies($page));
}

// Discover
for ($page = 1; $page <= 5; $page++) {
    add_movies_to_sitemap(get_discover_movies(null, $page));
}

/*
|--------------------------------------------------------------------------
| Buat XML
|--------------------------------------------------------------------------
*/

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Homepage
$xml .= '<url>';
$xml .= '<loc>https://cinestream-gold.vercel.app/</loc>';
$xml .= '</url>';

// Halaman detail film
foreach (array_keys($movieIds) as $movieId) {

    $url = 'https://cinestream-gold.vercel.app/detail.php?id=' . $movieId;

    $xml .= '<url>';
    $xml .= '<loc>' . htmlspecialchars(
        $url,
        ENT_XML1,
        'UTF-8'
    ) . '</loc>';
    $xml .= '</url>';
}

$xml .= '</urlset>';

/*
|--------------------------------------------------------------------------
| Kirim XML
|--------------------------------------------------------------------------
*/

header('Content-Type: application/xml; charset=UTF-8');

echo $xml;