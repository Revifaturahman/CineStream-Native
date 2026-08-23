<?php
/**
 * CineStream - TMDB API Helper
 * Mengelola semua permintaan HTTP ke TMDB API tanpa dependency external.
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Melakukan HTTP request ke endpoint TMDB API
 * 
 * @param string $endpoint Endpoint TMDB (misal: '/trending/movie/week')
 * @param array $params Parameter query string tambahan
 * @return array Data respons JSON yang di-decode menjadi array assosiatif
 */
function tmdb_request($endpoint, $params = []) {
    $apiKey = defined('TMDB_API_KEY') ? TMDB_API_KEY : '';

    if (empty($apiKey) || $apiKey === 'YOUR_TMDB_API_KEY_HERE') {
        return [
            'success' => false,
            'error' => 'API_KEY_MISSING',
            'message' => 'TMDB API Key belum dikonfigurasi. Silakan isi TMDB_API_KEY pada file .env',
            'results' => []
        ];
    }

    $defaultParams = [
        'api_key' => $apiKey,
        'language' => TMDB_DEFAULT_LANG
    ];

    $queryParams = array_merge($defaultParams, $params);
    $url = TMDB_BASE_URL . $endpoint . '?' . http_build_query($queryParams);

    $response = null;

    // Gunakan cURL jika tersedia
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CineStream/1.0');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'error' => 'CURL_ERROR',
                'message' => 'Gagal terhubung ke TMDB: ' . $curlError,
                'results' => []
            ];
        }

        if ($httpCode >= 400) {
            $errorData = json_decode($response, true);
            $msg = isset($errorData['status_message']) ? $errorData['status_message'] : "HTTP Error {$httpCode}";
            return [
                'success' => false,
                'error' => 'HTTP_ERROR',
                'http_code' => $httpCode,
                'message' => $msg,
                'results' => []
            ];
        }
    } else {
        // Fallback file_get_contents dengan stream context
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: CineStream/1.0\r\n",
                'timeout' => 10,
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return [
                'success' => false,
                'error' => 'STREAM_ERROR',
                'message' => 'Gagal mengambil data dari TMDB menggunakan stream context.',
                'results' => []
            ];
        }
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'JSON_DECODE_ERROR',
            'message' => 'Gagal memproses data respons JSON dari TMDB.',
            'results' => []
        ];
    }

    $data['success'] = true;
    return $data;
}

/**
 * Mengambil film yang sedang trending minggu ini
 * Endpoint: GET /trending/movie/week
 */
function get_trending_movies() {
    return tmdb_request('/trending/movie/week');
}

/**
 * Mengambil film discover dengan opsi filter genre
 * Endpoint: GET /discover/movie
 * 
 * @param int|null $genreId ID genre TMDB
 * @param int $page Halaman data
 */
function get_discover_movies($genreId = null, $page = 1) {
    $params = ['page' => $page];
    if ($genreId !== null && !empty($genreId)) {
        $params['with_genres'] = $genreId;
    }
    return tmdb_request('/discover/movie', $params);
}

/**
 * Mengambil film berdasarkan pencarian kata kunci
 * Endpoint: GET /search/movie
 * 
 * @param string $query Kata kunci pencarian
 * @param int $page Halaman data
 */
function get_search_movies($query, $page = 1) {
    return tmdb_request('/search/movie', [
        'query' => $query,
        'page' => $page
    ]);
}

/**
 * Mengambil film terpopuler
 * Endpoint: GET /movie/popular
 * 
 * @param int $page Halaman data
 */
function get_popular_movies($page = 1) {
    return tmdb_request('/movie/popular', ['page' => $page]);
}

/**
 * Mengambil film dengan rating tertinggi
 * Endpoint: GET /movie/top_rated
 * 
 * @param int $page Halaman data
 */
function get_top_rated_movies($page = 1) {
    return tmdb_request('/movie/top_rated', ['page' => $page]);
}

/**
 * Mengambil detail informasi lengkap suatu film
 * Endpoint: GET /movie/{movie_id}
 * 
 * @param int|string $movieId ID Film TMDB
 */
function get_movie_detail($movieId) {
    $movieId = (int)$movieId;
    return tmdb_request("/movie/{$movieId}");
}

/**
 * Mengambil daftar cast / credits dari suatu film
 * Endpoint: GET /movie/{movie_id}/credits
 * 
 * @param int|string $movieId ID Film TMDB
 */
function get_movie_credits($movieId) {
    $movieId = (int)$movieId;
    return tmdb_request("/movie/{$movieId}/credits");
}

/**
 * Helper untuk mendapatkan URL gambar poster film
 * 
 * @param string|null $posterPath Path poster dari data TMDB
 * @param string $fallback Gambar alternatif jika poster tidak ada
 * @return string URL gambar poster
 */
function get_poster_url($posterPath, $fallback = 'assets/img/family1.jpg') {
    if (!empty($posterPath)) {
        return TMDB_IMG_POSTER . $posterPath;
    }
    return $fallback;
}

/**
 * Helper untuk mendapatkan URL gambar backdrop film
 * 
 * @param string|null $backdropPath Path backdrop dari data TMDB
 * @param string $fallback Gambar alternatif jika backdrop tidak ada
 * @return string URL gambar backdrop
 */
function get_backdrop_url($backdropPath, $fallback = 'assets/img/family1.jpg') {
    if (!empty($backdropPath)) {
        return TMDB_IMG_BACKDROP . $backdropPath;
    }
    return $fallback;
}

/**
 * Helper untuk mendapatkan URL gambar foto profil pemeran/cast
 * 
 * @param string|null $profilePath Path profil dari data TMDB
 * @param string $fallback Gambar alternatif jika foto tidak ada
 * @return string URL gambar foto profil
 */
function get_profile_url($profilePath, $fallback = 'https://placehold.co/185x278/1f2333/a0a6b5?text=No+Photo') {
    if (!empty($profilePath)) {
        return TMDB_IMG_PROFILE . $profilePath;
    }
    return $fallback;
}

