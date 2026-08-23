<?php
/**
 * CineStream - Configuration File
 * Memuat environment variables dari file .env dan mendefinisikan konstanta konfigurasi.
 */

// Fungsi parser .env sederhana tanpa dependency external
function load_env($filePath) {
    if (!file_exists($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Lewati baris kosong atau komentar
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Hapus tanda kutip jika ada
            $value = trim($value, '"\'');

            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Muat file .env dari root folder proyek
load_env(__DIR__ . '/../.env');

// Ambil TMDB API Key dari environment variable
$tmdbApiKey = getenv('TMDB_API_KEY') ?: ($_ENV['TMDB_API_KEY'] ?? ($_SERVER['TMDB_API_KEY'] ?? ''));

// Konstanta Aplikasi
define('APP_NAME', 'CineStream');
define('APP_TAGLINE', 'Nonton Film Streaming Online');

// Konstanta TMDB API
define('TMDB_API_KEY', $tmdbApiKey);
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3');
define('TMDB_IMG_POSTER', 'https://image.tmdb.org/t/p/w500');
define('TMDB_IMG_BACKDROP', 'https://image.tmdb.org/t/p/w1280');
define('TMDB_IMG_PROFILE', 'https://image.tmdb.org/t/p/w185');
define('TMDB_DEFAULT_LANG', 'id-ID');

// Daftar Genre Film yang Didukung
define('GENRES_LIST', [
    28 => 'Action',
    12 => 'Adventure',
    16 => 'Animation',
    35 => 'Comedy',
    80 => 'Crime',
    99 => 'Documentary',
    18 => 'Drama',
    14 => 'Fantasy',
    27 => 'Horror',
    53 => 'Thriller'
]);
