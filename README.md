# Aplikasi Learning Management System (LMS) - Laravel

Ini adalah aplikasi Learning Management System (LMS) yang dibangun menggunakan framework Laravel. Aplikasi ini dirancang untuk memfasilitasi proses pembelajaran online, memungkinkan instruktur untuk membuat kursus, mengelola materi, dan berinteraksi dengan peserta.

## ✨ Fitur Utama

### 🔐 Manajemen Pengguna & Peran

- Sistem otentikasi lengkap (login, register, lupa password).
- Manajemen peran dan hak akses (`Admin`, `Instruktur`, `Peserta`, `Event Organizer`) menggunakan [spatie/laravel-permission](https://github.com/spatie/laravel-permission).
- Admin dapat mengelola pengguna (CRUD) dan mengimpor pengguna secara massal dari file CSV.

### 📚 Manajemen Kursus

- Instruktur dapat membuat, mengedit, dan menghapus kursus.
- Peserta dapat mendaftar ke kursus yang tersedia.
- Kursus memiliki materi pelajaran yang terstruktur dalam beberapa lesson.

### 🧠 Manajemen Materi (Konten)

- Dukungan konten teks, video, kuis, dan esai.
- Editor WYSIWYG (TinyMCE) untuk pembuatan konten kaya.
- Sistem prasyarat antar lesson.

### 📝 Sistem Penilaian & Evaluasi

- **Kuis Pilihan Ganda**: Penilaian otomatis.
- **Tugas Esai**: Dinilai manual oleh instruktur.
- **Buku Nilai (Gradebook)**: Instruktur dapat melihat dan mengelola nilai.

### 📊 Pelacakan Progres

- Peserta dapat melihat progres belajar mereka.
- Instruktur dan EO dapat memantau progres peserta.
- Laporan progres dapat diunduh dalam format PDF.

### 💬 Fitur Interaktif & Komunikasi

- Forum diskusi per materi pelajaran.
- Sistem pengumuman oleh Admin.
- Umpan balik (feedback) untuk kursus.

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.2+, Laravel 11
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Database**: MySQL (atau database lain yang didukung Laravel)

### Paket Utama

- [`laravel/breeze`](https://github.com/laravel/breeze) untuk otentikasi.
- [`spatie/laravel-permission`](https://github.com/spatie/laravel-permission) untuk manajemen peran.
- [`dompdf/dompdf`](https://github.com/dompdf/dompdf) untuk generate PDF.

## 🚀 Panduan Instalasi

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB

### Langkah-langkah Instalasi

1. **Kloning Repositori**
   ```bash
   git clone https://github.com/nama-pengguna/nama-repositori.git
   cd nama-repositori
   ```

2. **Instal Dependensi PHP**
   ```bash
   composer install
   ```

3. **Konfigurasi Lingkungan**
   ```bash
   cp .env.example .env
   ```

   Sesuaikan `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=password_anda
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Migrasi dan Seeder**
   ```bash
   php artisan migrate --seed
   ```

6. **Buat Symbolic Link untuk Penyimpanan**
   ```bash
   php artisan storage:link
   ```

7. **Instal Dependensi JavaScript**
   ```bash
   npm install
   ```

8. **Compile Aset Frontend**
   ```bash
   npm run dev
   ```

9. **Jalankan Server Pengembangan**
   ```bash
   php artisan serve
   ```

Aplikasi akan berjalan di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 👤 Akun Default

Setelah menjalankan `php artisan db:seed`, Anda dapat login dengan akun berikut:

| Role            | Email                    | Password  |
|-----------------|--------------------------|-----------|
| Admin           | admin@example.com        | password  |
| Instructor      | instructor@example.com   | password  |
| Participant     | participant@example.com  | password  |
| Event Organizer | eo@example.com           | password  |

---

> 📌 Untuk kontribusi, masalah, atau pertanyaan lainnya, silakan buat issue atau pull request di repositori ini.
