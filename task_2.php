<?php
// Base map
$map = [
    "########",
    "#......#",
    "#.###..#",
    "#...#.##",
    "#X#....#",
    "########"
];

$startX = 0; 
$startY = 0;

// Cari posisi X duluan
foreach($map as $y => $baris) {
    $x = strpos($baris, 'X');
    if($x !== false) {
        $startX = $x;
        $startY = $y;
        break;
    }
}

$kemungkinan = [];

// Jalan ke Utara (Y minus)
for($a = 1; ; $a++) {
    $utaraY = $startY - $a;
    $utaraX = $startX;
    if($map[$utaraY][$utaraX] === '#') break; // Mentok

    // Jalan ke Kanan/Timur (X plus)
    for($b = 1; ; $b++) {
        $timurX = $utaraX + $b;
        $timurY = $utaraY;
        if($map[$timurY][$timurX] === '#') break; 

        // Jalan ke Selatan (Y plus)
        for($c = 1; ; $c++) {
            $selatanY = $timurY + $c;
            $selatanX = $timurX;
            if($map[$selatanY][$selatanX] === '#') break; 

            // Simpan kalau rutenya valid
            $kemungkinan[] = [$selatanX, $selatanY];
        }
    }
}

echo "Kemungkinan lokasi item (X, Y):\n";
foreach($kemungkinan as $titik) {
    echo "- (" . $titik[0] . ", " . $titik[1] . ")\n";
    // Timpa titiknya pakai $ buat nampilin bonus poin
    $map[$titik[1]][$titik[0]] = '$';
}

echo "\nMap dengan prediksi lokasi ($):\n";
foreach($map as $baris) {
    echo $baris . "\n";
}