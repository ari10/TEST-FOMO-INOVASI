<?php
header('Content-Type: application/json');

// Setup koneksi DB, ganti sesuai config lokal
try {
    $db = new PDO('mysql:host=localhost;dbname=fomo_store', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Koneksi database gagal']);
    exit;
}

?>