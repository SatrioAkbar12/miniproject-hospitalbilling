# RS Delta Surya - Sistem Kasir & Dashboard Marketing

Sistem manajemen transaksi medis dan dashboard analitik yang dirancang untuk mempercepat proses administrasi kasir dan memberikan wawasan data bagi departemen marketing.

## Stack Teknologi
- **Framework:** Laravel 12
- **Frontend:** Vue.js 3 (Composition API) via Inertia.js
- **Styling:** Tailwind CSS
- **Database:** PostgreSQL

## Fitur Utama
1. **Dashboard Marketing:** Visualisasi data.
2. **Master Voucher:** Manajemen diskon khusus asuransi (Reliance, Allianz, Prudential) dengan validasi periode Januari 2026.
3. **Transaksi Kasir:** - Input transaksi multi-tindakan medis (Data prosedur ditarik dari API Eksternal).
   - Kalkulasi diskon otomatis per tindakan sesuai aturan asuransi.
   - Cetak bukti pembayaran PDF.
4. **Automated Reporting:** Cron Job setiap pukul 01:00 AM untuk mengirimkan laporan Excel transaksi harian ke email marketing.
5. **Audit Log:** Pencatatan setiap perubahan data (Create/Update/Delete) untuk integritas data.

## Persyaratan Sistem
- PHP 8.5
- Composer
- Node.js & NPM
- PostgreSQL

## List tabel
1. **Master Voucher**  
id: *uuid*  
insurance_id: *uuid*  
name: *string*  
type: *string*  
discount_value: *decimal*  
max_discount_amount: *decimal*  
start_date: *date*  
end_date: *date*  
is_active: *boolean*  
created_by: *uuid*  
created_at: *timestamp*  
updated_at: *timestamp*

2. **Transactions**  
id: *uuid*  
transaction_code: *uuid*  
patient_name: *string*  
insurance_id: *uuid*  
voucher_id: *uuid*  
total_amount_original: *decimal*  
discount_amount: *decimal*  
total_amount_final: *decimal*  
status: *string*  
created_by: *uuid*  
created_at: *timestamp*  
updated_at: *timestamp*

3. **Transaction Details**  
id: *uuid*  
transaction_id: *uuid*  
procedure_id: *uuid*  
procedure_name: *string*  
price: *decimal*  
discount_amount: *decimal*  
final_price: *decimal*  
created_at: *timestamp*  
updated_at: *timestamp*

## Instalasi

1. **Clone repositori:**
   ```bash
   git clone [https://github.com/username/rs-delta-surya.git](https://github.com/username/rs-delta-surya.git)
   cd rs-delta-surya
   
2. **Instal dependensi PHP:**
    ```bash
   composer install

3. **Instal dependensi Frontend:**
    ```bash
   npm install
   npm run build

4. **Konfigurasi Environment:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Atur konfigurasi DB_DATABASE, MAIL_MAILER, dan API_URL di dalam file .env.
5. **Migrasi Database:**
    ```bash
    php artisan migrate
6. **Jalankan Aplikasi:**
    ```bash
    php artisan serve

## Konfigurasi cron job
Untuk menjalankan laporan otomatis pukul 01:00, pastikan cron job server Anda memanggil scheduler Laravel setiap menit:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
