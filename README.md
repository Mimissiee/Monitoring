## MONITORING
***
SiswaTrack adalah aplikasi web berbasis PHP Native yang dirancang untuk mengelola dan memantau aktivitas pembinaan siswa secara digital. Aplikasi ini mencakup pengelolaan kegiatan Rabuan, Mentoring, Bina Jasmani (Binjas), dan Operasional, dilengkapi dengan fitur CRUD, autentikasi pengguna, serta penyimpanan data terpusat. Tujuan pengembangannya adalah untuk mempermudah proses monitoring, dokumentasi, dan pengelolaan data siswa secara lebih efektif, terstruktur, dan efisien.

<img width="1598" height="706" alt="image" src="https://github.com/user-attachments/assets/474b370c-5cf0-4783-a7f2-e28392afe812" />

***
## Teknologi & Tools yang Digunakan

- Backend: PHP Native
- Frontend: HTML, CSS, JavaScript
- Database: MySQL / MariaDB
- Tools: Laragon atau XAMPP, serta Git untuk version control

*** 
## Panduan Instalasi & Menjalankan Aplikasi

Pastikan perangkat Anda sudah terinstal web server lokal seperti **Laragon** atau **XAMPP** yang mendukung **PHP 8.1+** dan **MySQL**.

### 1. Persiapan Kebutuhan Sistem
Pastikan perangkat Anda telah terinstal:
- Server lokal seperti Laragon atau XAMPP (dengan PHP 8.1+ dan MySQL)
- Git (untuk cloning repository)

### 2. Pemindahan Folder Proyek
1. Pastikan folder proyek dinamai dengan tepat, contoh: **`mentoring`**.
2. Pindahkan folder `mentoring` tersebut ke dalam direktori root web server lokal Anda:
   * **Laragon:** Taruh di `C:\laragon\www\mentoring`
   * **XAMPP:** Taruh di `C:\xampp\htdocs\mentoring`

### 3. Menjalankan Aplikasi 
Halaman Login Sistem: `http://localhost/siswatrack/login.php`
* **Username:** admin
* **password:** admin
<img width="1600" height="708" alt="image" src="https://github.com/user-attachments/assets/e09d9f06-3edd-49da-9ebb-51218e294edb" />

***

### Pemetaan Rubrik Penilaian & Penjelasan Fitur
Aplikasi SiswaTrack menerapkan beberapa konsep penting dalam pengembangan web, antara lain:

* **Struktur Web yang Baik: Halaman disusun secara terorganisir dengan menu Dashboard, Timeline, Rabuan, Mentoring, Operasional, Bina Jasmani, dan Kehadiran.**
* **Tampilan Responsif: Antarmuka dapat menyesuaikan berbagai ukuran layar sehingga nyaman digunakan pada desktop maupun perangkat mobile.**
* **Validasi Data: Formulir dilengkapi validasi pada sisi klien dan server untuk memastikan data yang disimpan valid dan lengkap.**
* **Operasi CRUD: Pengguna dapat menambah, melihat, mengubah, dan menghapus data pada setiap modul kegiatan.**
* **Autentikasi & Hak Akses: Sistem login berbasis session digunakan untuk membatasi akses pengguna sesuai perannya.**
* **Manajemen Dokumen: Modul Mentoring mendukung unggah bahan ajar dalam format PDF sebagai dokumentasi kegiatan.**

Pentingnya CRUD pada SiswaTrack

CRUD (Create, Read, Update, Delete) merupakan fitur utama yang memungkinkan pengguna mengelola data kegiatan dan siswa secara dinamis. Dengan CRUD, informasi dapat selalu diperbarui, ditampilkan, maupun dihapus sesuai kebutuhan sehingga proses monitoring kegiatan menjadi lebih efektif dan terstruktur.
<img width="1600" height="719" alt="image" src="https://github.com/user-attachments/assets/1f9e1804-2572-41dd-947d-71f7287e87d7" />

***

### Struktur file
```
SISWATRACK/
│
├── actions/
│   ├── proses_binjas.php
│   ├── proses_checklist.php
│   ├── proses_login.php
│   ├── proses_mentoring.php
│   ├── proses_operasional.php
│   ├── proses_presensi.php
│   ├── proses_rabuan.php
│   └── proses_upload.php
│
├── api/
│   ├── get_binjas.php
│   ├── get_dashboard.php
│   ├── get_kehadiran.php
│   ├── get_siswa.php
│   └── get_timeline.php
│
├── assets/
│   ├── css/
│   │   ├── components.css
│   │   ├── main.css
│   │   └── sidebar.css
│   │
│   ├── img/
│   │   └── .gitkeep
│   │
│   └── js/
│       ├── bar-kehadiran.js
│       ├── gantt-timeline.js
│       ├── radar-binjas.js
│       └── script.js
│
├── config/
│   └── koneksi.php
│
├── includes/
│   ├── auth_check.php
│   ├── footer.php
│   ├── header.php
│   └── sidebar.php
│
├── modules/
│   ├── operasional/
│   ├── binjas.php
│   ├── dashboard.php
│   ├── kehadiran.php
│   ├── mentoring.php
│   ├── rabuan.php
│   └── timeline.php
│
├── uploads/
│
├── index.php
├── login.php
└── logout.php
```
***
### Alur data aplikasi

1. Request Pengguna
Pengguna mengakses salah satu halaman pada folder ```modules/``` seperti Dashboard, Rabuan, Mentoring, Operasional, Bina Jasmani, atau Kehadiran.
2. Pengecekan Autentikasi
File ```includes/auth_check.php``` memverifikasi apakah pengguna telah login dan memiliki hak akses untuk membuka halaman tersebut.
3. Pemrosesan Data
Jika pengguna mengirim formulir, data akan diproses oleh file pada folder ```actions/``` yang sesuai dengan modulnya, seperti ```proses_mentoring.php```, ```proses_rabuan.php```, atau ```proses_binjas.php```.
4. Interaksi Database
File pemrosesan menggunakan ```config/koneksi.php``` untuk terhubung ke database dan menjalankan operasi CRUD (Create, Read, Update, Delete).
5. Penyusunan Tampilan
Halaman dibangun menggunakan komponen pada folder ```includes/```, seperti ```header.php```, ```sidebar.php```, dan ```footer.php```, kemudian menampilkan data yang diperoleh dari database.
6. Response ke Pengguna
Hasil akhir dikirim kembali ke browser sehingga pengguna dapat melihat data terbaru pada modul yang sedang digunakan.

***

### Known Issues

Pengembangan API masih belum selesai dan saat ini hanya mendukung beberapa kebutuhan internal aplikasi. Dukungan API yang lebih lengkap akan ditambahkan pada versi berikutnya.
