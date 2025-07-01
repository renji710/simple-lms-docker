# Simple LMS Backend (Laravel 10 Dockerized)

## Capstone Project Pemrograman Sisi Server

Ini adalah implementasi backend API untuk sistem Learning Management System (LMS) sederhana, yang dikembangkan sebagai proyek Capstone untuk mata kuliah Pemrograman Sisi Server. Proyek ini bertujuan untuk melengkapi fungsionalitas dasar LMS dengan berbagai fitur tambahan, mengikuti persyaratan yang telah ditentukan.

Meskipun tugas awal mengacu pada repositori Django, proyek ini telah **dibangun ulang (recreated)** dari awal menggunakan **Laravel 10** dan di-containerisasi dengan **Docker**.

## Developer

Raditya Abdul Afeef

A11.2022.14203

## Fitur yang Diimplementasikan (Total: 15 Poin)

Proyek ini mengimplementasikan fitur-fitur berikut, mencapai total 15 poin sesuai ketentuan tugas:

1.  **[Endpoint +1] Register:**
    *   Memungkinkan calon pengguna untuk mendaftar langsung ke sistem dengan biodata dan kredensial login.
2.  **[Fitur +2] Manajemen Profil Pengguna:**
    *   **Show Profile:** Endpoint untuk menampilkan profil lengkap dari pengguna tertentu (termasuk dirinya sendiri atau pengguna lain jika memiliki ID).
    *   **Edit Profile:** Endpoint untuk mengedit data profil pengguna yang sedang login (nama depan, nama belakang, email, username, password).
3.  **[Endpoint +1] Batch Enroll Students:**
    *   Memungkinkan seorang `Teacher` untuk mendaftarkan beberapa `Student` sekaligus ke dalam kursus yang dia miliki.
4.  **[Endpoint +1] Content Comment Moderation:**
    *   Memungkinkan `Teacher` untuk menentukan apakah suatu komentar pada konten kursusnya boleh ditampilkan (`approved`) atau tidak (`rejected`). Perubahan ini memengaruhi tampilan komentar bagi `Student`.
5.  **[Fitur +4] Course Announcements (Paket):**
    *   Fitur tambahan agar seorang `Teacher` dapat memberikan pengumuman khusus pada kursus tertentu yang akan muncul pada tanggal tertentu.
    *   **Create Announcement:** Menambahkan pengumuman pada kursus tertentu (hanya `Teacher`).
    *   **Show Announcement:** Menampilkan semua pengumuman pada kursus tertentu (`Teacher` dan `Student` dapat menampilkan; `Student` hanya melihat yang sudah dipublikasi).
    *   **Edit Announcement:** Mengedit pengumuman (hanya `Teacher` yang membuat pengumuman tersebut).
    *   **Delete Announcement:** Menghapus pengumuman (hanya `Teacher` yang membuat pengumuman tersebut).
6.  **[Fitur +3] Content Completion Tracking (Paket):**
    *   Menambahkan fitur agar `Student` bisa menandai konten yang sudah diselesaikan.
    *   **Add Completion Tracking:** `Student` dapat menandai bahwa suatu konten sudah diselesaikan.
    *   **Show Completion:** `Student` dapat menampilkan daftar penyelesaian pada kursus yang dia ikuti.
    *   **Delete Completion:** `Student` dapat menghapus data penyelesaiannya sendiri.
7.  **[Fitur +3] Content Bookmarking (Paket):**
    *   Memungkinkan pengguna (`Student`) menandai konten kursus untuk referensi di masa mendatang.
    *   **Add Bookmarking:** `Student` dapat membuat bookmark pada konten kursus yang bisa diakses.
    *   **Show Bookmark:** Menampilkan semua bookmark yang dibuat oleh `Student` tersebut, termasuk detail konten dan kursusnya.
    *   **Delete Bookmark:** Menghapus bookmark yang pernah dibuat `Student` tersebut.

## Teknologi yang Digunakan

*   **Backend Framework:** PHP 8.2 dengan Laravel 10
*   **Database:** MySQL 8.0 (Dockerized)
*   **API Authentication:** Laravel Sanctum (Token-based Authentication)
*   **Containerization:** Docker & Docker Compose
*   **PHP/Nginx Image:** `webdevops/php-nginx-dev`
