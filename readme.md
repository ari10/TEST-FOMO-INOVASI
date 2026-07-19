# FOMO Assessment Test - Fullstack Engineer

Repo ini isinya hasil kerjaan buat technical test PT FOMO INOVASI TEKNOLOGI. 

## Persiapan & Setup
Biar gampang dites di lokal, ikutin step ini ya:
1. Clone repo ini ke dalem htdocs atau document root lokal (contoh: `C:\laragon\www\tokoonline`).
2. Bikin database baru di lokal, kasih nama `fomo_store`.
3. Import file `fomo_store.sql` yang ada di repo ini ke database tadi. (Ini bakal otomatis bikin struktur tabel sekaligus isi dummy data produknya).
4. Kalau setup DB kamu beda dari default (user `root`, password kosong), jangan lupa sesuaikan config koneksi PDO di bagian atas file `api.php`.

---

## Task 1: Online Store (Flash Sale Race Condition)

**Cara Kerja / Logika:**
Buat nge-handle masalah *race condition* pas flash sale (waktu banyak order masuk di milidetik yang sama), saya pakai teknik **Atomic Update** langsung pas eksekusi query ke database. 

Query yang dipakai: `UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :qty`.

Dengan cara ini, proses ngecek sisa stok dan ngurangin stok dijalanin barengan di level database. Ini efektif banget buat nyegah kasus *negative inventory* (stok minus) dibandingin kalau kita pakai `SELECT` dulu baru `UPDATE` (yang gampang kebobolan pas di-hit barengan).

**Cara Ngetesnya:**
Saya udah buatin script otomatis buat ngetes *concurrent requests* pakai `curl_multi`.
1. Buka terminal atau command prompt.
2. Masuk ke folder projectnya (contoh: `cd C:\laragon\www\tokoonline`).
3. Jalanin perintah ini:
   ```bash
   php test_race.php

##  Task 2: Hidden Item
Cara Kerja / Logika:
Ini program CLI buat nyari koordinat tersembunyi. Logikanya, program bakal nge-scan map/grid secara dinamis buat nemuin titik start si pemain (X). Habis dapet koordinat awalnya, kode bakal ngelakuin nested looping ngikutin aturan gerak yang diminta (Utara -> Timur -> Selatan). Tiap langkah bakal dicek supaya nggak nabrak rintangan tembok (#).

Cara Ngetesnya & Ekspektasi Hasil:
Tinggal jalanin aja script-nya lewat terminal php hidden_item.php. Terminal bakal nge-print list titik koordinat (X, Y) rute akhirnya. Selain itu, buat menuhin syarat bonus point, program juga bakal nge-print ulang bentuk grid-nya, tapi titik lokasi itemnya udah ditandain pakai simbol $.