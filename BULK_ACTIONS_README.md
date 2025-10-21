# 📦 Fitur Bulk Actions untuk Manajemen Sertifikat & Force Complete

## 🎯 Overview

Fitur ini menambahkan kemampuan untuk melakukan operasi massal (bulk actions) pada:
1. **Manajemen Sertifikat** - Download banyak sertifikat sekaligus
2. **Force Complete** - Force complete banyak peserta sekaligus dengan/tanpa generate sertifikat otomatis

Dirancang khusus untuk menangani **1000+ peserta** dengan efisien menggunakan **Laravel Queue**.

---

## ✨ Fitur-Fitur Baru

### 1. Bulk Download Sertifikat (ZIP)

**Lokasi:** Halaman Manajemen Sertifikat (`/certificate-management`)

**Cara Penggunaan:**
1. Centang sertifikat yang ingin diunduh (bisa pilih semua dengan checkbox "Pilih Semua")
2. Klik tombol **"📥 Download Terpilih (ZIP)"**
3. File ZIP akan otomatis terdownload dengan nama: `certificates_YYYY-MM-DD_HHMMSS.zip`

**Fitur:**
- ✅ Download multiple sertifikat sekaligus dalam 1 file ZIP
- ✅ Nama file otomatis: `Sertifikat-{Course}-{Nama}-{Code}.pdf`
- ✅ File ZIP langsung terdownload
- ✅ Temporary files dibersihkan otomatis

**Route:** `POST /certificate-management/bulk-action` (action: `download`)

---

### 2. Bulk Force Complete Peserta

**Lokasi:** Halaman Force Complete (`/admin/force-complete`)

**Cara Penggunaan:**
1. Pilih kursus dari dropdown
2. Centang peserta yang ingin di-force complete
3. (Opsional) Centang **"Generate sertifikat otomatis"** jika ingin langsung generate sertifikat
4. Klik tombol **"Force Complete Terpilih"**

**Fitur:**
- ✅ Force complete banyak peserta sekaligus
- ✅ Otomatis generate sertifikat untuk peserta yang memenuhi syarat (opsional)
- ✅ Checkbox "Pilih Semua" untuk select semua peserta
- ✅ Counter jumlah peserta terpilih
- ✅ Untuk >50 peserta: Otomatis menggunakan **Background Queue**
- ✅ Progress tracking via log dengan Batch ID

**Route:** `POST /admin/force-complete/bulk`

---

### 3. Bulk Generate Sertifikat

**Lokasi:** Halaman Force Complete (`/admin/force-complete`)

**Cara Penggunaan:**
1. Pilih kursus dari dropdown
2. Centang peserta yang ingin di-generate sertifikatnya
3. Klik tombol **"Generate Sertifikat Terpilih"**

**Fitur:**
- ✅ Generate sertifikat untuk banyak peserta sekaligus
- ✅ Skip otomatis jika sertifikat sudah ada
- ✅ Untuk >50 peserta: Otomatis menggunakan **Background Queue**
- ✅ Progress tracking via log dengan Batch ID

**Route:** `POST /admin/force-complete/bulk-certificates`

---

## 🚀 Queue System untuk Performa Optimal

### Mengapa Queue?

Untuk menangani **1000+ peserta**, proses berjalan di background menggunakan Laravel Queue untuk:
- ⚡ **Tidak freeze browser** - User bisa langsung melanjutkan pekerjaan lain
- 🔄 **Retry otomatis** - Jika ada error, job akan retry otomatis (3x)
- 📊 **Scalable** - Dapat memproses ribuan peserta tanpa timeout
- 🛡️ **Safe** - Error di satu peserta tidak menghentikan proses keseluruhan

### Konfigurasi Queue

**Untuk Development (Quick Test):**
```bash
# Gunakan sync driver (langsung diproses)
# .env
QUEUE_CONNECTION=sync
```

**Untuk Production (Recommended):**
```bash
# Gunakan database atau redis
# .env
QUEUE_CONNECTION=database

# Jalankan queue worker
php artisan queue:work --tries=3 --timeout=600

# Atau gunakan supervisor untuk auto-restart
```

### Monitoring Progress

Track progress bulk actions melalui log:

```bash
# Monitor log real-time
tail -f storage/logs/laravel.log

# Cari berdasarkan Batch ID
grep "batch_fc_" storage/logs/laravel.log
grep "bulk_cert_" storage/logs/laravel.log
```

**Log Format:**
```
Bulk Force Complete Job Started: batch_id=bulk_fc_67890, course_id=123, user_count=1000
...
Bulk Force Complete Job Completed: processed=998, errors=2, total=1000
```

---

## 📋 File-File Baru

### 1. Jobs

#### `app/Jobs/BulkForceCompleteJob.php`
- Memproses force complete untuk batch peserta (50 users/job)
- Auto-generate sertifikat (opsional)
- Timeout: 10 minutes
- Retry: 3x

#### `app/Jobs/BulkGenerateCertificatesJob.php`
- Memproses generate sertifikat untuk batch peserta (50 users/job)
- Skip jika sertifikat sudah ada
- Timeout: 10 minutes
- Retry: 3x

### 2. Controller Methods

**CertificateController:**
- `bulkDownload()` - Download multiple certificates as ZIP
- Updated `bulkAction()` - Support action: `download`

**ForceCompleteController:**
- `bulkForceComplete()` - Bulk force complete dengan queue
- `bulkGenerateCertificates()` - Bulk generate certificates dengan queue

### 3. Views

**Updated:**
- `resources/views/admin/force-complete/index.blade.php` - Tambah bulk action UI
- `resources/views/certificate-management/index.blade.php` - Tambah bulk download

### 4. Routes

**Added:**
```php
// Force Complete Bulk
POST /admin/force-complete/bulk
POST /admin/force-complete/bulk-certificates

// Certificate Management Bulk
POST /certificate-management/bulk-action (action: download)
```

---

## 💡 Best Practices

### 1. Untuk <50 Peserta
- Proses langsung (synchronous)
- Response cepat (dalam hitungan detik)
- User melihat hasil langsung

### 2. Untuk 50-500 Peserta
- Otomatis gunakan queue
- Split menjadi multiple jobs (50 users/job)
- Total: 10 jobs untuk 500 peserta
- User dapat Batch ID untuk tracking

### 3. Untuk >500 Peserta
- Gunakan queue dengan worker
- Monitor via log
- Proses berjalan di background
- User mendapat notifikasi via Batch ID

---

## 🔧 Troubleshooting

### Queue Tidak Jalan

**Cek queue connection:**
```bash
php artisan queue:failed
```

**Restart queue worker:**
```bash
php artisan queue:restart
```

### Error di Specific User

**Check logs dengan Batch ID:**
```bash
grep "batch_fc_YOUR_BATCH_ID" storage/logs/laravel.log
```

**Logs akan menunjukkan:**
- User ID yang error
- Error message
- Total processed vs errors

### Download ZIP Gagal

**Cek:**
1. Extension `php-zip` sudah diinstall
2. Folder `storage/app/temp` writable
3. Cek file sertifikat exists: `storage/app/public/certificates/`

```bash
# Install php-zip (jika belum)
sudo apt-get install php-zip

# Set permission
chmod -R 775 storage/
```

---

## 📊 Performance Metrics

### Benchmark (Approximate)

| Jumlah Peserta | Mode      | Waktu Estimasi | Memory |
|----------------|-----------|----------------|--------|
| 10 peserta     | Sync      | ~5 detik       | 50MB   |
| 50 peserta     | Sync      | ~30 detik      | 100MB  |
| 100 peserta    | Queue     | ~2 menit       | 150MB  |
| 500 peserta    | Queue     | ~10 menit      | 200MB  |
| 1000 peserta   | Queue     | ~20 menit      | 300MB  |

*Note: Waktu tergantung pada server specs dan kompleksitas sertifikat

---

## 🎨 UI/UX Improvements

### Force Complete Page
- ✅ Checkbox per peserta
- ✅ "Pilih Semua" checkbox
- ✅ Bulk action panel dengan counter
- ✅ Progress indicator dengan warna (hijau = 100%, orange = <100%)
- ✅ Konfirmasi dialog dengan info queue

### Certificate Management Page
- ✅ Counter sertifikat terpilih
- ✅ Bulk action panel (Download, Update Template, Delete)
- ✅ Improved UX dengan status badge
- ✅ Better error handling

---

## 🔒 Security & Permissions

**Authorization:**
- Force Complete: Requires `update` permission on Course
- Certificate Management: Requires `view progress reports` permission
- Bulk Download: Each certificate checked with `view` policy

**Validation:**
- User IDs validated against database
- Course ID required dan validated
- Certificate IDs validated

---

## 🧪 Testing

### Manual Testing Steps

**1. Test Bulk Download (Small):**
```
1. Buka /certificate-management
2. Pilih 3-5 sertifikat
3. Klik "Download Terpilih (ZIP)"
4. Verify ZIP contains correct PDFs
```

**2. Test Bulk Force Complete (Medium):**
```
1. Buka /admin/force-complete
2. Pilih kursus dengan 20-30 peserta
3. Pilih 10 peserta
4. Centang "Generate sertifikat otomatis"
5. Klik "Force Complete Terpilih"
6. Verify semua peserta progress = 100%
```

**3. Test Queue System (Large):**
```
1. Setup queue: QUEUE_CONNECTION=database
2. Run: php artisan queue:work
3. Pilih 100+ peserta
4. Process bulk force complete
5. Monitor logs untuk Batch ID
6. Verify completion di log
```

---

## 📝 Future Enhancements

Potensi improvement di masa depan:
- [ ] Real-time progress bar dengan WebSocket
- [ ] Email notification saat batch selesai
- [ ] Export hasil bulk action ke Excel
- [ ] Scheduled bulk operations
- [ ] Bulk edit certificate data
- [ ] Undo/rollback untuk bulk operations

---

## 📞 Support

Jika ada issue atau pertanyaan:
1. Check log: `storage/logs/laravel.log`
2. Check queue table: `select * from jobs;`
3. Check failed jobs: `select * from failed_jobs;`

---

**Dibuat oleh:** Claude Code Assistant
**Tanggal:** 2025-10-21
**Versi Laravel:** 11.x
