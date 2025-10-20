# Fingerprint Dashboard

Node.js dashboard untuk sistem manajemen kehadiran berbasis fingerprint yang terintegrasi dengan Laravel Attendance Management System.

## Fitur

- 📊 Dashboard real-time untuk monitoring kehadiran
- 🔗 Integrasi dengan mesin fingerprint
- 📈 Statistik dan laporan kehadiran
- 🔄 Sinkronisasi data otomatis
- 📱 API RESTful untuk frontend
- 🔐 Sistem autentikasi dan otorisasi
- 📊 Chart dan visualisasi data

## Struktur Proyek

```
fingerprint-dashboard/
├── src/
│   ├── controllers/          # Controller untuk handling request
│   │   ├── fingerprintController.js
│   │   ├── attendanceController.js
│   │   └── dashboardController.js
│   ├── middleware/           # Custom middleware
│   │   ├── errorHandler.js
│   │   └── fingerprintMiddleware.js
│   ├── routes/              # Route definitions
│   │   ├── fingerprint.js
│   │   ├── attendance.js
│   │   └── dashboard.js
│   └── app.js               # Main application file
├── config/
│   └── database.js          # Database configuration
├── public/                  # Static files
├── views/                   # Template files
├── routes/                  # Additional routes
├── middleware/              # Additional middleware
├── package.json
├── env.example
└── README.md
```

## Instalasi

1. **Clone atau buat folder proyek**
   ```bash
   cd fingerprint-dashboard
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Setup environment variables**
   ```bash
   cp env.example .env
   ```
   
   Edit file `.env` dengan konfigurasi yang sesuai:
   ```env
   PORT=3001
   NODE_ENV=development
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=attendance_management_system
   DB_USER=root
   DB_PASSWORD=
   FINGERPRINT_DEVICE_IP=192.168.1.100
   FINGERPRINT_DEVICE_PORT=4370
   ```

4. **Jalankan aplikasi**
   ```bash
   # Development mode
   npm run dev
   
   # Production mode
   npm start
   ```

## API Endpoints

### Health Check
- `GET /health` - Status aplikasi

### Fingerprint Management
- `GET /api/fingerprint/devices` - Daftar device fingerprint
- `POST /api/fingerprint/devices` - Tambah device baru
- `PUT /api/fingerprint/devices/:id` - Update device
- `DELETE /api/fingerprint/devices/:id` - Hapus device
- `POST /api/fingerprint/sync` - Sinkronisasi data fingerprint
- `GET /api/fingerprint/logs` - Log data fingerprint

### Attendance Management
- `GET /api/attendance` - Daftar kehadiran
- `GET /api/attendance/employee/:id` - Kehadiran per karyawan
- `GET /api/attendance/date/:date` - Kehadiran per tanggal
- `GET /api/attendance/stats/daily` - Statistik harian
- `GET /api/attendance/stats/monthly` - Statistik bulanan

### Dashboard
- `GET /api/dashboard/overview` - Overview dashboard
- `GET /api/dashboard/stats` - Statistik dashboard
- `GET /api/dashboard/live-attendance` - Kehadiran real-time
- `GET /api/dashboard/charts/daily-attendance` - Data chart harian

## Konfigurasi Database

Aplikasi ini menggunakan database MySQL yang sama dengan Laravel Attendance Management System. Pastikan:

1. Database `attendance_management_system` sudah ada
2. Tabel-tabel berikut sudah tersedia:
   - `users`
   - `employees`
   - `attendances`
   - `schedules`
   - `finger_devices`
   - `role_users`

## Integrasi dengan Mesin Fingerprint

Untuk integrasi dengan mesin fingerprint, Anda perlu:

1. **Konfigurasi IP dan Port** mesin fingerprint di file `.env`
2. **Implementasi protokol komunikasi** dengan mesin fingerprint
3. **Setup sinkronisasi data** otomatis

## Development

### Scripts yang tersedia:
- `npm start` - Jalankan aplikasi
- `npm run dev` - Jalankan dalam mode development dengan nodemon
- `npm test` - Jalankan test

### Struktur Controller:
- `fingerprintController.js` - Handle operasi fingerprint device
- `attendanceController.js` - Handle data kehadiran
- `dashboardController.js` - Handle data dashboard dan statistik

## Dependencies Utama

- **Express.js** - Web framework
- **Sequelize** - ORM untuk database
- **MySQL2** - MySQL driver
- **Socket.io** - Real-time communication
- **Moment.js** - Date manipulation
- **Axios** - HTTP client
- **CORS** - Cross-origin resource sharing
- **Helmet** - Security middleware

## Lisensi

MIT License

## Kontribusi

1. Fork proyek ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Support

Untuk pertanyaan atau dukungan, silakan buat issue di repository ini.