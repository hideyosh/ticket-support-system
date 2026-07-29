# MyDesk - Ticket Support System

Sistem manajemen tiket dukungan pelanggan berbasis role (Admin, Supervisor, Agent, Customer), dibangun dengan Laravel. Mendukung workflow status tiket, SLA tracking, activity log, notifikasi (email + dalam-app), dan REST API.

## 📑 Daftar Isi

- [Ringkasan Fitur](#ringkasan-fitur)
- [Tech Stack](#tech-stack)
- [Instalasi & Setup](#instalasi--setup)
- [Menjalankan Test](#menjalankan-test)
- [Kredensial User Seeder](#kredensial-user-seeder)
- [Catatan Arsitektur](#catatan-arsitektur)
- [Relasi Database](#relasi-database)
- [Contoh Pemakaian API](#contoh-pemakaian-api)
- [Keterbatasan Saat Ini](#keterbatasan-saat-ini)

---

## Ringkasan Fitur

### Customer
- Register & login
- Membuat tiket baru
- Melihat & melacak tiket miliknya sendiri (tidak bisa lihat tiket orang lain)
- Menambahkan komentar publik
- Upload lampiran
- Melihat riwayat aktivitas ticket (versi simplified — tanpa detail old/new value)

### Agent
- Melihat tiket yang di-assign kepadanya saja
- Menambahkan komentar (publik & internal note)
- Upload lampiran

### Supervisor
- Memantau tiket dari agent-agent dalam timnya
- Assign / reassign ticket ke agent dalam timnya sendiri (tidak bisa assign ke agent tim lain)
- Melihat halaman Activity Logs, dibatasi hanya untuk tiket timnya
- Mengelola data tim

### Administrator
- Manajemen tiket penuh (CRUD)
- Manajemen user, role, kategori, prioritas, label, SLA rule, tim
- Melihat seluruh Activity Log tanpa batasan
- Assign ticket ke agent manapun
- Mengubah status ticket (dengan validasi transisi)

### Fitur Sistem
- Role-based access control (RBAC) custom, lewat model `Role` + Laravel Policy (`TicketPolicy`, `CommentPolicy`, `UserPolicy`)
- Workflow status tiket dengan validasi transisi ketat (`TicketStatusService`) — mencegah lompat status yang tidak valid (misal `open` langsung ke `resolved`)
- Kalkulasi SLA due-date otomatis berdasarkan priority (`TicketSlaService`)
- Lampiran file untuk ticket & comment, tervalidasi ukuran dan tipe file
- Activity Log custom yang mencatat: siapa, aksi apa, ticket mana, nilai lama, nilai baru, kapan
- Notifikasi lewat Laravel Notifications — channel **mail** dan **database** (bell icon di navbar), sebagian di-queue
- REST API via Laravel Sanctum untuk operasi ticket dari luar aplikasi web

---

## Tech Stack

* **Framework:** Laravel 13
* **PHP:** 8.4
* **Frontend:** Blade + Bootstrap 5 (AdminLTE-style layout)
* **Autentikasi Web:** Laravel session-based auth
* **Autentikasi API:** Laravel Sanctum (token-based)
* **Database:** MySQL
* **Testing:** Pest
* **Mail testing (lokal):** Mailtrap / Log driver

---

## Instalasi & Setup

1. **Clone & install dependencies:**
   ```bash
   git clone <https://github.com/hideyosh/ticket-support-system.git>
   cd ticket-support-system
   composer install
   npm install && npm run build
   ```

2. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan koneksi database di `.env`. Untuk testing email secara lokal, set `MAIL_MAILER=log`, atau pakai Mailtrap (lihat kredensial di dashboard Mailtrap Anda, JANGAN commit ke repo).

3. **Migration & seeding:**
   ```bash
   php artisan migrate --seed
   ```

4. **Storage link** (dibutuhkan untuk lampiran ticket/comment):
   ```bash
   php artisan storage:link
   ```

5. **Setup API (Sanctum):**
   ```bash
   php artisan install:api
   ```
   *(Skip kalau `routes/api.php` dan tabel `personal_access_tokens` sudah ada.)*

6. **Queue worker** (dibutuhkan untuk notifikasi yang di-queue, misal saat agent di-assign ticket):
   ```bash
   php artisan queue:work
   ```

7. **Jalankan aplikasi:**
   ```bash
   php artisan serve
   ```
   Akses di `http://127.0.0.1:8000/`.

---

## Menjalankan Test

```bash
php artisan test
```

<!-- TODO: dokumentasikan test suite di sini setelah dibuat (Feature Tests + Unit Tests) -->

---

## Kredensial User Seeder

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `password` |
| **Supervisor** | `supervisor@example.com` | `password` |
| **Agent** | `agent@example.com` | `password` |
| **Customer** | `customer@example.com` | `password` |

---

## Screenshots

### Role: Administrator

| Administrator Dashboard | Administrator Ticket |
| :---: | :---: |
|<img width="1468" height="776" alt="Screenshot 2026-07-29 193923" src="https://github.com/user-attachments/assets/05808d6e-babf-47b0-b2af-4d52206749f8" /> | <img width="1469" height="778" alt="Screenshot 2026-07-29 194302" src="https://github.com/user-attachments/assets/7f885079-4c9c-4c7a-875a-714206b56391" />|


| Ticket Detail | Communication |
| :---: | :---: |
| <img width="1468" height="776" alt="Screenshot 2026-07-29 194047" src="https://github.com/user-attachments/assets/29f519b8-ae16-45c0-876d-1ab93975592a" /> | <img width="1468" height="776" alt="Screenshot 2026-07-29 200314" src="https://github.com/user-attachments/assets/4a73f507-6739-4d63-8e16-753b1f370d25" />|

### Role: Supervisor

| Supervisor Dashboard | Supervisor Ticket |
| :---: | :---: |
| <img width="1469" height="778" alt="Screenshot 2026-07-29 224911" src="https://github.com/user-attachments/assets/0635232f-9055-4c52-bf18-637fcc30a710" />|<img width="1469" height="778" alt="Screenshot 2026-07-29 224922" src="https://github.com/user-attachments/assets/29eac069-c288-4bab-a19c-df8d47f5e9d3" />|

| Ticket Detail | Communication |
| :---: | :---: |
| <img width="1469" height="778" alt="Screenshot 2026-07-29 225003" src="https://github.com/user-attachments/assets/8c6ec674-c9ea-4cb9-b894-1ab304cc9035" />| <img width="1469" height="778" alt="Screenshot 2026-07-29 225009" src="https://github.com/user-attachments/assets/da51bf12-10ef-45a0-9e2b-493d90422ed7" />|

### Role: Agent

| Agent Dashboard | Agent Ticket |
| :---: | :---: |
| <img width="1469" height="776" alt="Screenshot 2026-07-29 230448" src="https://github.com/user-attachments/assets/f89b21c9-9c37-47fa-94a0-2f345c584ea1" />|<img width="1469" height="776" alt="Screenshot 2026-07-29 230116" src="https://github.com/user-attachments/assets/fdbc9582-9d85-4572-9b03-e100c2250718" />|

| Ticket Detail | Communication |
| :---: | :---: |
| <img width="1469" height="777" alt="Screenshot 2026-07-29 230125" src="https://github.com/user-attachments/assets/335847fa-fd8c-4605-aa04-962dc21fabd3" />|<img width="1469" height="777" alt="image" src="https://github.com/user-attachments/assets/d52797d0-ae38-41fe-a72c-b71c9828b82e" />|

### Role: Customer

| Customer Dashboard | Customer Ticket |
| :---: | :---: |
| <img width="1468" height="776" alt="Screenshot 2026-07-29 201429" src="https://github.com/user-attachments/assets/fe116d38-8daf-42ba-b3ba-9dc0f964f982" />|<img width="1468" height="776" alt="Screenshot 2026-07-29 214709" src="https://github.com/user-attachments/assets/6071698a-4a34-43e3-955d-0535b580af61" /> |

| Ticket Detail | Communication |
| :---: | :---: |
|<img width="1469" height="773" alt="Screenshot 2026-07-29 214735" src="https://github.com/user-attachments/assets/47b0a328-967e-4f15-bf68-57f869781e89" />|<img width="1469" height="773" alt="Screenshot 2026-07-29 214743" src="https://github.com/user-attachments/assets/cfe88f2a-1939-4d8b-98b3-ec4629a1a75b" />|

## Catatan Arsitektur

Struktur aplikasi dipisah berdasarkan tanggung jawab, supaya controller tetap ringkas:

* **Controllers** (dipisah per role: `Admin/`, `Supervisor/`, `Agent/`, `Customer/`, `Api/`) — menangani HTTP layer & routing.
* **Services** (`App\Services`):
  - `TicketStatusService` — satu sumber kebenaran untuk aturan transisi status (`open → assigned → in_progress → resolved`, dst), mencegah transisi tidak valid lewat `InvalidStatusTransitionException`.
  - `TicketSlaService` — kalkulasi `due_date` berdasarkan `SlaRule` dan priority ticket.
  - `ActivityLogger` — helper terpusat untuk mencatat ke tabel `activity_logs` (siapa, aksi, ticket, field, old/new value).
* **Policies** (`App\Policies`) — otorisasi ketat di level backend (`TicketPolicy`, `CommentPolicy`, `UserPolicy`), mencegah IDOR (misal customer mengakses ticket milik customer lain lewat manipulasi URL).
* **Notifications** (`App\Notifications`) — 6 event notifikasi (ticket created, assigned, commented, resolved, escalated, SLA overdue), masing-masing lewat channel `mail` + `database`.

---

## Relasi Database

* **User & Team:** `Team belongsTo User (supervisor)`, `User belongsTo Team`. Satu supervisor mengelola satu tim, agent-agent ditempatkan di tim itu lewat `team_id`.
* **Ticket:** `Ticket belongsTo User (creator via created_by)`, `Ticket belongsTo User (assignedAgent via assigned_to)`.
* **Master data:** Ticket punya `Category`, `Priority` (belongsTo), dan `Label` (many-to-many lewat pivot).
* **Lampiran:** `Ticket hasMany Attachment`, `Comment hasMany Attachment` — masing-masing menyimpan `uploaded_by`, path file, mime type, dan ukuran.
* **Activity Log:** `ActivityLog belongsTo User` (pelaku) dan `belongsTo Ticket` — kolom eksplisit `action`, `field`, `old_value`, `new_value`, `created_at`.

---

## Contoh Pemakaian API

**1. Login (dapat token):**
```http
POST /api/login
Content-Type: application/json

{
  "email": "customer@example.com",
  "password": "password",
  "device_name": "postman"
}
```

**2. Buat ticket:**
```http
POST /api/tickets/create
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Tidak bisa login setelah reset password",
  "description": "Link reset password sudah expired.",
  "category_id": 1,
  "priority_id": 2,
  "labels": [1, 3]
}
```

**3. List ticket milik user yang login:**
```http
GET /api/tickets
Authorization: Bearer {token}
```

**4. Assign agent** *(admin/supervisor):*
```http
PATCH /api/tickets/{ticket}/assign
Authorization: Bearer {token}
Content-Type: application/json

{
  "assigned_to": 5
}
```

**5. Ubah status ticket:**
```http
PATCH /api/tickets/{ticket}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "resolved"
}
```

---

## Keterbatasan Saat Ini

* **Kalkulasi SLA** masih linear — belum memperhitungkan jam kerja, akhir pekan, atau hari libur.
* **Update comment real-time** belum ada — masih butuh refresh halaman untuk lihat komentar baru (belum pakai WebSocket/Laravel Reverb).
* **Notifikasi SLA overdue** dijalankan lewat Artisan Command terjadwal (`tickets:check-sla-overdue`) — butuh cron/scheduler aktif di server produksi supaya jalan otomatis.

