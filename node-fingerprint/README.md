# Node.js Fingerprint Sync Service

Service Node.js untuk mengambil data log dari mesin fingerprint Solution X401 dan menyinkronkannya dengan Laravel Attendance Management System melalui API.

## 🎯 Fitur

- ✅ Pull data attendance dari fingerprint device via SOAP Web Service
- ✅ Parse XML response dari fingerprint device
- ✅ Send data ke Laravel API endpoint
- ✅ Auto-sync setiap 5 detik (real-time)
- ✅ Error handling & retry logic
- ✅ Logging lengkap untuk monitoring
- ✅ Duplicate detection
- ✅ Connection testing untuk fingerprint & Laravel API

## 📋 Requirements

- Node.js >= 16.0.0
- Koneksi network ke fingerprint device (192.168.0.201)
- Koneksi internet untuk POST ke Laravel API
- Laravel API endpoint accessible

## 🚀 Installation

1. **Install dependencies**
   ```bash
   npm install
   ```

2. **Setup configuration**
   ```bash
   # Copy env.example ke .env
   cp env.example .env
   
   # Edit .env dengan konfigurasi yang sesuai:
   # - FINGERPRINT_DEVICE_IP=192.168.0.201
   # - LARAVEL_API_URL=https://yourdomain.com/api
   # - LARAVEL_API_TOKEN=your-secret-token
   ```

3. **Start service**
   ```bash
   # Development mode
   npm run dev
   
   # Production mode
   npm start
   ```

## ⚙️ Configuration (.env)

```env
# Fingerprint Device
FINGERPRINT_DEVICE_IP=192.168.0.201
FINGERPRINT_DEVICE_PORT=80
FINGERPRINT_COMM_KEY=0

# Laravel API
# Development (local dengan port 8000):
LARAVEL_API_URL=http://127.0.0.1:8000/api
# Production (ganti dengan domain Anda):
# LARAVEL_API_URL=https://yourdomain.com/api
LARAVEL_API_TOKEN=your-secret-token-here

# Sync Interval (milliseconds)
SYNC_INTERVAL=5000  # 5 detik untuk real-time

# Logging
LOG_LEVEL=info
LOG_FILE=logs/fingerprint.log
```

## 📡 API Endpoints

### Laravel API Endpoints:
- `POST /api/fingerprint/sync` - Sync single attendance record
- `POST /api/fingerprint/sync-bulk` - Sync multiple attendance records

### Request Format:
```json
{
  "pin": 113,
  "datetime": "2025-11-01 08:15:30",
  "verified": 15,
  "status": 0
}
```

## 🔧 Running di Windows Server

### Setup dengan PM2 (Recommended)

```powershell
# Install PM2 global
npm install -g pm2

# Start service
cd node-fingerprint
pm2 start index.js --name fingerprint-sync

# Setup auto-start saat boot
pm2 startup
pm2 save

# Monitor service
pm2 monit

# Check logs
pm2 logs fingerprint-sync

# Restart service
pm2 restart fingerprint-sync

# Stop service
pm2 stop fingerprint-sync
```

### Setup dengan Windows Service (Alternatif)

Bisa menggunakan `node-windows` atau `node-windows-service` untuk install sebagai Windows Service.

## 📊 Monitoring

### Check Logs
```bash
# View logs real-time
tail -f logs/fingerprint.log

# Atau dengan PM2
pm2 logs fingerprint-sync
```

### Log Format
```
2025-11-01 15:30:45 [INFO]: 📡 Pulling data from fingerprint device: 192.168.0.201:80
2025-11-01 15:30:46 [INFO]: ✅ Retrieved 5 attendance record(s) from fingerprint device
2025-11-01 15:30:47 [INFO]: 📤 Sending 5 attendance record(s) to Laravel API...
2025-11-01 15:30:48 [INFO]: ✅ Attendance synced: PIN 113, 2025-11-01 08:15:30
```

## 🔍 Troubleshooting

### Error: Cannot connect to fingerprint device
- ✅ Check IP address: `192.168.0.201`
- ✅ Check network connection (ping device)
- ✅ Check firewall (port 80)
- ✅ Verify device is powered on

### Error: Cannot connect to Laravel API
- ✅ Check API URL: `https://yourdomain.com/api`
- ✅ Check internet connection
- ✅ Verify Laravel application is running
- ✅ Check API token is correct

### Error: Employee not found
- ✅ Pastikan PIN di fingerprint device = `id_employees` di database
- ✅ Check employee exists in database
- ✅ Verify mapping PIN → id_employees

## 📝 Notes

- Service akan running terus menerus (24/7)
- Data yang sudah di-sync tidak akan di-sync lagi (duplicate detection)
- Service akan auto-retry jika ada error
- Logs disimpan di `logs/fingerprint.log`

## 🎯 Deployment Checklist

### Development:
- [ ] Install dependencies: `npm install`
- [ ] Setup `.env` file
- [ ] Test connection ke fingerprint device
- [ ] Test connection ke Laravel API (local)
- [ ] Run service: `npm run dev`

### Production (Windows Server):
- [ ] Copy folder `node-fingerprint` ke Windows Server
- [ ] Setup `.env` dengan production config
- [ ] Install dependencies: `npm install`
- [ ] Install PM2: `npm install -g pm2`
- [ ] Start service dengan PM2
- [ ] Setup auto-start: `pm2 startup` & `pm2 save`
- [ ] Verify service running: `pm2 list`
- [ ] Monitor logs: `pm2 logs`

## 📞 Support

Jika ada masalah, check:
1. Logs di `logs/fingerprint.log`
2. PM2 logs: `pm2 logs fingerprint-sync`
3. Network connectivity ke fingerprint device
4. Laravel API endpoint accessibility
