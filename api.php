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
// Ambil payload dari request
$payload = json_decode(file_get_contents('php://input'), true);
$prodId = $payload['product_id'] ?? null;
$qty = $payload['quantity'] ?? null;

// Validasi input dasar
if (!$prodId || !$qty || $qty < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Data input tidak valid']);
    exit;
}

try {
    $db->beginTransaction();

    // Pake atomic update buat handle race condition pas flash sale
    // Jadi stock dicek dan dikurangin di satu query yang sama biar nggak tembus minus
    $qUpdate = $db->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :qty");
    $qUpdate->execute(['qty' => $qty, 'id' => $prodId]);

    if ($qUpdate->rowCount() > 0) {
        // Kalau berhasil motong stock, baru insert ke tabel orders
        $qOrder = $db->prepare("INSERT INTO orders (status, created_at) VALUES ('PAID', NOW())");
        $qOrder->execute();
        $newOrderId = $db->lastInsertId();

        // Insert relasi ke order_items
        $qItem = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (:oid, :pid, :qty)");
        $qItem->execute(['oid' => $newOrderId, 'pid' => $prodId, 'qty' => $qty]);

        $db->commit();
        http_response_code(201);
        echo json_encode(['message' => 'Order sukses dibuat', 'order_id' => $newOrderId]);
    } else {
        // Stock habis atau keduluan transaksi lain
        $db->rollBack();
        http_response_code(409);
        echo json_encode(['error' => 'Stock habis / tidak mencukupi']);
    }
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
}

?>