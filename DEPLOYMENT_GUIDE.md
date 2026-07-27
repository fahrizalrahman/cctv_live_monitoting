# Panduan Deployment CCTV Live Monitor di Localhost (Windows XAMPP)

Panduan ini berisi langkah-langkah komprehensif dari awal hingga aplikasi CCTV Live Monitor beserta fitur streaming-nya berjalan sempurna di komputer Windows menggunakan XAMPP.

---

## TAHAP 1: Persiapan Aplikasi Pendukung (Prerequisites)

Pastikan Anda sudah mengunduh dan menginstal aplikasi berikut sebelum memulai:
1. **XAMPP** (Sangat disarankan versi PHP 8.1 atau 8.2).
2. **Composer** (Untuk dependensi PHP) - Unduh dari [getcomposer.org](https://getcomposer.org/).
3. **Node.js & npm** (Untuk build frontend TailwindCSS) - Unduh dari [nodejs.org](https://nodejs.org/).

---

## TAHAP 2: Setup Streaming Engine (FFmpeg & Go2RTC)

Aplikasi ini menggunakan **Go2RTC** sebagai server WebRTC dan **FFmpeg** untuk membantu menerjemahkan (transcoding) video stream RTSP dari CCTV agar bisa diputar di browser.

### A. Install FFmpeg
1. Download **FFmpeg** versi Windows (berformat `.zip`) dari [gyan.dev/ffmpeg/builds/](https://www.gyan.dev/ffmpeg/builds/).
2. Ekstrak file zip tersebut.
3. Buat folder baru di Drive C Anda bernama `go2rtc` (Lokasi: `C:\go2rtc`).
4. Buka folder FFmpeg yang sudah diekstrak, masuk ke dalam folder `bin`, dan salin/copy file **`ffmpeg.exe`**.
5. Paste file `ffmpeg.exe` tersebut ke dalam folder `C:\go2rtc`.

### B. Install & Konfigurasi Go2RTC
1. Download **Go2RTC** versi Windows terbaru (file bernama `go2rtc_windows_amd64.exe` atau `.zip`) dari [GitHub Go2RTC Releases](https://github.com/AlexxIT/go2rtc/releases).
2. Pindahkan file tersebut ke folder `C:\go2rtc`.
3. Ubah nama (rename) file tersebut menjadi **`go2rtc.exe`**.
4. Di dalam folder `C:\go2rtc`, buat sebuah file baru bernama **`go2rtc.yaml`** (gunakan Notepad).
5. Isi file `go2rtc.yaml` dengan daftar CCTV Anda beserta link RTSP-nya. Format penamaannya harus mengikuti ID CCTV di database (`cctv_1`, `cctv_2`, dst). Contoh:
```yaml
streams:
  # Streaming biasa (RTSP standard)
  cctv_1: "rtsp://admin:password123@192.168.1.10:8000/stream"
  
  # Jika streaming CCTV berat/berformat H.265, panggil ffmpeg untuk transcode ke H.264
  cctv_2: "ffmpeg:rtsp://admin:password123@192.168.1.11:8000/stream#video=h264#audio=aac"
```
6. Simpan file tersebut. Nantinya, file `go2rtc.exe` harus dijalankan setiap kali Anda ingin live streaming.

---

## TAHAP 3: Konfigurasi Apache (Reverse Proxy untuk Go2RTC)

Agar aplikasi web (frontend) bisa mengakses aliran video dari Go2RTC melalui port yang sama dengan XAMPP, kita perlu mengatur *Reverse Proxy* di Apache.

1. Buka **XAMPP Control Panel**.
2. Pada baris **Apache**, klik tombol **Config** lalu pilih **httpd.conf**.
3. Di dalam file teks yang terbuka, gunakan fitur pencarian (Ctrl+F) untuk mencari teks berikut, lalu **HAPUS tanda pagar (#)** di depannya agar modulnya aktif:
   - `LoadModule proxy_module modules/mod_proxy.so`
   - `LoadModule proxy_http_module modules/mod_proxy_http.so`
4. Scroll ke bagian paling bawah file tersebut, tekan Enter untuk baris baru, dan tambahkan kode berikut:
```apache
ProxyPass /go2rtc http://127.0.0.1:1984
ProxyPassReverse /go2rtc http://127.0.0.1:1984
```
5. **Save** file tersebut, lalu tutup.
6. Pada XAMPP Control Panel, klik **Stop** pada Apache, lalu klik **Start** kembali untuk me-restart.

---

## TAHAP 4: Setup Source Code Laravel (CCTV Live Monitor)

1. Pindahkan folder project CCTV Anda (misalnya `cctv_live`) ke dalam direktori Htdocs XAMPP Anda.
   *Lokasi biasanya: `C:\xampp\htdocs\cctv_live`*
2. Buka **Command Prompt (CMD)** atau **Terminal** di Windows.
3. Arahkan direktori terminal ke folder project:
   ```bash
   cd C:\xampp\htdocs\cctv_live
   ```
4. Jalankan perintah berikut untuk menginstal semua library PHP:
   ```bash
   composer install
   ```
5. Install dependensi Node.js (untuk Frontend):
   ```bash
   npm install
   ```
6. Proses (build) file CSS dan JavaScript aplikasi:
   ```bash
   npm run build
   ```

---

## TAHAP 5: Konfigurasi Environment & Database

1. Buka folder `cctv_live`, cari file bernama **`.env.example`**.
2. Ubah nama (rename) file tersebut menjadi **`.env`** (hapus `.example`).
3. Buka file `.env` menggunakan Notepad atau editor teks, lalu cari bagian konfigurasi database dan ubah menjadi seperti ini:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cctv_live
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Pastikan password disesuaikan jika root MySQL Anda memiliki password).*
4. Simpan file `.env`.
5. Buka kembali **CMD / Terminal** yang masih mengarah ke folder project, jalankan perintah ini untuk mengenerate security key Laravel:
   ```bash
   php artisan key:generate
   ```

---

## TAHAP 6: Buat Database & Import Data Bawaan (Seeder)

1. Pastikan **MySQL** sudah berstatus *Start* di XAMPP.
2. Buka web browser, lalu akses alamat: `http://localhost/phpmyadmin`
3. Buat database baru dengan persis nama: **`cctv_live`**
4. Buka kembali **CMD / Terminal** di folder project, lalu jalankan perintah ini untuk membangun tabel dan memasukkan data admin serta dummy menu secara otomatis:
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan perintah berikut untuk menautkan folder agar sistem bisa membaca logo dan file marker peta yang Anda unggah nantinya:
   ```bash
   php artisan storage:link
   ```

---

## TAHAP 7: Menjalankan Aplikasi & Testing Live!

Semua tahapan instalasi sudah selesai. Untuk menjalankan aplikasi, ada 2 layanan utama yang **wajib aktif** setiap saat:

### 1. Aktifkan Streaming Server (Go2RTC)
- Buka folder `C:\go2rtc`.
- Klik ganda (Double Click) file **`go2rtc.exe`**.
- Akan muncul jendela hitam (Command Prompt) yang mendeteksi konfigurasi `go2rtc.yaml`. **Biarkan jendela ini tetap terbuka (jangan di-close)** selama aplikasi berjalan agar streaming CCTV bisa tersambung.

### 2. Aktifkan Web Server (XAMPP / Laravel)
**Cara A: Lewat XAMPP Langsung (Rekomendasi Web Produksi Lokal)**
Pastikan Apache dan MySQL menyala, lalu langsung buka browser ke alamat: 
👉 `http://localhost/cctv_live/public`

**Cara B: Lewat Artisan Serve (Rekomendasi Development)**
Buka CMD di folder `cctv_live`, jalankan `php artisan serve`.
Lalu buka browser ke alamat: 
👉 `http://127.0.0.1:8000`

### 🔑 Akun Login Bawaan (Default):
Gunakan akun ini untuk masuk ke Dashboard CMS Admin:
- **Email:** `admin@cctv.local`
- **Password:** `password`

Anda sekarang dapat mengatur CCTV Anda di menu "Master CCTV" dan mengkonfigurasi Logo di menu "Setting Apps"!
