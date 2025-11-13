## Dwiprasetia API

API RESTful untuk platform blog pribadi yang menampilkan konten publik, interaksi pengguna, dan CMS admin. Dibuat menggunakan Laravel 12 dengan autentikasi Sanctum.

---

## Kebutuhan Sistem

- PHP 8.2+
- Composer
- Node.js 18+ 
- PostgreSQL 
- Ekstensi PHP: openssl, pdo_pgsql, mbstring, tokenizer, xml, ctype, json, fileinfo

---

## 1. Setup

```bash
cp .env.example .env         
composer install
php artisan key:generate

php artisan migrate           
php artisan storage:link      
```

Build aset opsional:

```bash
npm install
npm run dev
```

---

## 2. Membuat Admin

Semua akun baru secara default memiliki role `user`. Ubah role akun secara manual (ganti ID/email sesuai kebutuhan):

```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'admin@example.com')->first();
>>> $user->role = 'admin';
>>> $user->save();
>>> exit
```

---

## 3. Alur Autentikasi (Bearer Token)

1. Registrasi atau login untuk menerima Sanctum plain-text token.

   ```http
   POST /api/v1/auth/register
   {
     "name": "Dwi Prasetia",
     "email": "wiwidwwi@example.com",
     "password": "secret123",
     "password_confirmation": "secret123",
     "bio": "Optional"
   }

   POST /api/v1/auth/login
   {
     "email": "wiwidwwi@example.com",
     "password": "secret123"
   }
   ```

2. Simpan `token` yang diterima di sisi klien.
3. Sertakan token pada request berikutnya:

   ```
   Authorization: Bearer <token>
   Accept: application/json
   ```

4. `POST /api/v1/auth/logout` akan menonaktifkan token saat ini.

Mode Sanctum berbasis cookie (SPA) dinonaktifkan secara default; gunakan bearer token untuk integrasi.

---

## 4. Fitur Utama

### Publik

- Melihat post terbit dengan informasi penulis, jumlah like, dan jumlah komentar.
- Melihat komentar bertingkat (hanya yang berstatus tampil).
- Mengirim pesan kontak.
- Membaca pengaturan situs (nama, logo, tentang).

### Pengguna Terautentikasi

- Memperbarui profil (nama, email, sandi, bio, unggah avatar).
- Berkomentar dan membalas post, termasuk edit/hapus komentar milik sendiri.
- Melaporkan komentar yang tidak pantas.
- Menyukai post (idempotent).
- Menyimpan/membookmark post.
- Melihat daftar post yang disimpan.

### Admin

- CRUD post lengkap, termasuk status draft dan unggah featured image.
- Meninjau laporan komentar (tandai pending/reviewed/dismissed, sembunyikan/pulihkan komentar).
- Melihat statistik dashboard dan snapshot post terbaru.
- Mengelola pengaturan situs (nama, tentang, unggah logo).
- Meninjau dan menghapus pesan kontak.
- Mengelola akun pengguna (daftar & ubah role/detail).
- Mengelola media library untuk gambar yang dapat digunakan ulang.

---

## 5. Endpoint API

| Method | URL | Auth | Deskripsi |
|--------|-----|------|-----------|
| POST | `/api/v1/auth/register` | Publik | Registrasi user baru dan kembalikan token |
| POST | `/api/v1/auth/login` | Publik | Login dan kembalikan token |
| POST | `/api/v1/auth/logout` | Bearer | Mencabut token saat ini |
| GET | `/api/v1/auth/me` | Bearer | Mengambil data user saat ini |
| GET | `/api/v1/profile` | Bearer | Detail profil |
| PUT/PATCH | `/api/v1/profile` | Bearer | Perbarui profil (mendukung unggah avatar) |
| GET | `/api/v1/posts` | Opsional | Daftar post (admin bisa memfilter status/draft) |
| GET | `/api/v1/posts/{post}` | Opsional | Detail post tunggal (draft khusus admin) |
| POST | `/api/v1/posts` | Admin | Buat post |
| PUT/PATCH | `/api/v1/posts/{post}` | Admin | Perbarui post |
| DELETE | `/api/v1/posts/{post}` | Admin | Hapus post |
| GET | `/api/v1/posts/{post}/comments` | Opsional | Komentar tingkat atas beserta balasan |
| POST | `/api/v1/posts/{post}/comments` | Bearer | Buat komentar atau balasan |
| PATCH | `/api/v1/comments/{comment}` | Bearer | Perbarui komentar (pemilik/admin) |
| DELETE | `/api/v1/comments/{comment}` | Bearer | Hapus komentar (pemilik/admin) |
| POST | `/api/v1/comments/{comment}/report` | Bearer | Laporkan komentar |
| GET | `/api/v1/admin/comment-reports` | Admin | Daftar laporan |
| PATCH | `/api/v1/admin/comment-reports/{report}` | Admin | Ubah status laporan dan moderasi |
| POST | `/api/v1/posts/{post}/like` | Bearer | Like post |
| DELETE | `/api/v1/posts/{post}/like` | Bearer | Batalkan like |
| POST | `/api/v1/posts/{post}/save` | Bearer | Bookmark post |
| DELETE | `/api/v1/posts/{post}/save` | Bearer | Hapus bookmark |
| GET | `/api/v1/me/saved-posts` | Bearer | Lihat post yang disimpan |
| POST | `/api/v1/messages` | Publik | Kirim formulir kontak |
| GET | `/api/v1/admin/messages` | Admin | Daftar pesan kontak |
| GET | `/api/v1/admin/messages/{message}` | Admin | Detail pesan |
| DELETE | `/api/v1/admin/messages/{message}` | Admin | Hapus pesan |
| GET | `/api/v1/settings` | Publik | Ambil pengaturan situs |
| PUT | `/api/v1/settings` | Admin | Perbarui pengaturan (nama/tentang/logo) |
| GET | `/api/v1/admin/dashboard` | Admin | Ringkasan metrik dan post terbaru |
| GET | `/api/v1/admin/users` | Admin | Daftar pengguna dengan filter |
| GET | `/api/v1/admin/users/{user}` | Admin | Detail pengguna |
| PUT/PATCH | `/api/v1/admin/users/{user}` | Admin | Perbarui pengguna (field profil & role) |
| GET | `/api/v1/admin/media` | Admin | Daftar aset media (mendukung `search`, `user_id`, `mime`, `per_page`) |
| POST | `/api/v1/admin/media` | Admin | Unggah gambar (`file`, opsional `alt_text`, `caption`) |
| GET | `/api/v1/admin/media/{media}` | Admin | Detail media |
| PUT/PATCH | `/api/v1/admin/media/{media}` | Admin | Perbarui metadata atau ganti file |
| DELETE | `/api/v1/admin/media/{media}` | Admin | Hapus media dan file terkait |


Aset media disimpan pada disk `public` di `storage/app/public/media`. Jalankan `php artisan storage:link` diawal.

---

## 6. Tips Postman

- Kirim header `Accept: application/json` agar respons dalam format JSON.
- Hapus cookie untuk `127.0.0.1` jika mendapat respons HTML; HTML berarti request masuk ke web route.
- Gunakan Postman Tests untuk menyimpan bearer token:

  ```js
  const data = pm.response.json();
  if (data.token) {
    pm.collectionVariables.set('apiToken', data.token);
  }
  ```

  Lalu set `Authorization: Bearer {{apiToken}}` pada request yang butuh autentikasi.

---

## 7. Penyimpanan & Manajemen File

- Avatar yang diunggah → `storage/app/public/avatars`
- Gambar post → `storage/app/public/posts`
- Logo situs → `storage/app/public/settings`
- Jalankan `php artisan storage:link` agar URL `/storage/...` pada respons dapat diakses.
- File lama otomatis dihapus saat diganti.

---

## 8. Pengujian

```bash
php artisan test
```

Tambahkan feature test di `tests/Feature` untuk mencakup alur penting (auth, post, komentar, aksi admin). // belum ada

---

## 9. Troubleshooting

- **401 Unauthorized:** Bearer token hilang/tidak valid. Login ulang dan perbarui header `Authorization`.
- **405 pada `/api/v1/auth/me`:** Request dialihkan ke `/login` karena header token tidak ada. Sertakan bearer token dan `Accept: application/json`.
- **Halaman error HTML:** Request API tidak menyertakan `Accept: application/json`; Laravel menampilkan halaman debug HTML.
- **`relation "sessions" does not exist`:** Jalankan `php artisan migrate`; migrasi tabel sessions sudah disediakan.

---

Dokumentasi dibuat pakai grok wkwk
