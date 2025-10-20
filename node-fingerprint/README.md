# Node.js Fingerprint Service

Service Node.js untuk mengambil data log dari mesin fingerprint dan menyinkronkannya dengan database Laravel Attendance Management System.

## Fitur

- 🔄 Sinkronisasi otomatis dengan mesin fingerprint
- 📊 Pengambilan data log kehadiran
- 🗄️ Integrasi dengan database MySQL Laravel
- ⏰ Sinkronisasi terjadwal (cron job)
- 📝 Logging lengkap untuk monitoring
- 🔧 Konfigurasi mudah melalui file .env

## Struktur Proyek

```
node-fingerprint/
├── index.js          # File utama service
├── package.json      # Dependencies Node.js
├── .env             # Konfigurasi environment
├── env.example      # Template konfigurasi
└── README.md        # Dokumentasi
```

## Instalasi

1. **Masuk ke folder node-fingerprint**
   ```bash
   cd node-fingerprint
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Setup konfigurasi**
   ```bash
   copy env.example .env
   ```
   
   Edit file `.env` dengan konfigurasi yang sesuai:
   ```env
   # Database Configuration
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=attendance_management_system
   DB_USER=root
   DB_PASSWORD=

   # Fingerprint Device Configuration
   FINGERPRINT_DEVICE_IP=192.168.1.100
   FINGERPRINT_DEVICE_PORT=4370
   FINGERPRINT_DEVICE_PASSWORD=0

   # Sync Configuration
   SYNC_INTERVAL=300000
   ```

4. **Jalankan service**
   ```bash
   # Development mode
   npm run dev
   
   # Production mode
   npm start
   ```

## Konfigurasi

### Database
- `DB_HOST`: Host database MySQL
- `DB_PORT`: Port database (default: 3306)
- `DB_NAME`: Nama database Laravel
- `DB_USER`: Username database
- `DB_PASSWORD`: Password database

### Fingerprint Device
- `FINGERPRINT_DEVICE_IP`: IP address mesin fingerprint
- `FINGERPRINT_DEVICE_PORT`: Port mesin fingerprint
- `FINGERPRINT_DEVICE_PASSWORD`: Password mesin fingerprint

### Sync Configuration
- `SYNC_INTERVAL`: Interval sinkronisasi dalam milidetik (default: 300000 = 5 menit)

### Logging
- `LOG_LEVEL`: Level logging (info, warn, error)
- `LOG_FILE`: File log output

## Cara Kerja

1. **Koneksi Database**: Service terhubung ke database MySQL Laravel
2. **Sinkronisasi Device**: Mengambil daftar mesin fingerprint aktif dari database
3. **Pengambilan Data**: Mengambil data log dari setiap mesin fingerprint
4. **Proses Data**: Memproses data log dan menyimpannya ke tabel attendances
5. **Logging**: Mencatat semua aktivitas untuk monitoring

## Tabel Database

Service ini menggunakan tabel-tabel berikut dari Laravel:

- `employees`: Data karyawan
- `attendances`: Data kehadiran
- `finger_devices`: Data mesin fingerprint
- `fingerprint_logs`: Log data fingerprint (akan dibuat otomatis)

## Scripts

- `npm start`: Jalankan service dalam mode production
- `npm run dev`: Jalankan service dalam mode development dengan nodemon

## Monitoring

Service akan mencatat semua aktivitas dalam file log. Periksa file log untuk:
- Status koneksi database
- Aktivitas sinkronisasi
- Error dan warning
- Data yang diproses

## Troubleshooting

### Database Connection Error
- Pastikan database MySQL Laravel sudah berjalan
- Periksa konfigurasi database di file .env
- Pastikan user database memiliki akses yang cukup

### Fingerprint Device Error
- Pastikan mesin fingerprint terhubung ke network
- Periksa IP address dan port mesin fingerprint
- Pastikan password mesin fingerprint benar

### Sync Error
- Periksa log untuk detail error
- Pastikan tabel database sudah ada
- Periksa koneksi network ke mesin fingerprint

## Dependencies

- **mysql2**: Driver MySQL untuk Node.js
- **node-cron**: Scheduler untuk sinkronisasi otomatis
- **moment**: Manipulasi tanggal dan waktu
- **winston**: Logging system
- **dotenv**: Environment variables
- **axios**: HTTP client (untuk API calls)

## Lisensi

MIT License

