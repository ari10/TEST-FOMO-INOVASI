<?php
$apiUrl = 'http://localhost/tokoonline/api.php'; // Sesuaikan URL
$data = json_encode(['product_id' => 1, 'quantity' => 1]);

$mh = curl_multi_init();
$curls = [];
$totalRequests = 50; // Tembak 50 request barengan buat simulasi burst

echo "Simulasi flash sale dengan $totalRequests request...\n";

for ($i = 0; $i < $totalRequests; $i++) {
    $c = curl_init($apiUrl);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_POST, true);
    curl_setopt($c, CURLOPT_POSTFIELDS, $data);
    curl_setopt($c, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_multi_add_handle($mh, $c);
    $curls[] = $c;
}

$active = null;
do {
    curl_multi_exec($mh, $active);
} while ($active);

$sukses = 0;
$gagal = 0;

foreach ($curls as $c) {
    $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
    if ($code == 201) {
        $sukses++;
    } else {
        $gagal++;
    }
    curl_multi_remove_handle($mh, $c);
}
curl_multi_close($mh);

echo "Hasil Test:\n";
echo "- Order Berhasil (Status 201): $sukses\n";
echo "- Order Gagal/Ditolak karena antrean stock: $gagal\n";    