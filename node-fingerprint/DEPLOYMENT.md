# Deployment Guide - Node.js Fingerprint Service

## 📦 Deployment ke Windows Server

### Step 1: Copy Folder ke Windows Server

1. Copy folder `node-fingerprint` ke Windows Server
2. Lokasi recommended: `C:\Services\fingerprint-sync\` atau sesuai kebutuhan

### Step 2: Install Node.js di Windows Server

1. Download Node.js dari https://nodejs.org/
2. Install Node.js (versi 16.0.0 atau lebih baru)
3. Verify installation:
   ```powershell
   node --version
   npm --version
   ```

### Step 3: Install Dependencies

```powershell
cd C:\Services\fingerprint-sync
npm install
```

### Step 4: Setup Configuration

1. Copy `env.example` ke `.env`:
   ```powershell
   copy env.example .env
   ```

2. Edit `.env` dengan konfigurasi production:
   ```env
   # Fingerprint Device (IP dari kantor)
   FINGERPRINT_DEVICE_IP=192.168.0.201
   FINGERPRINT_DEVICE_PORT=80
   FINGERPRINT_COMM_KEY=0

   # Laravel API
   # Development (local dengan port 8000):
   LARAVEL_API_URL=http://127.0.0.1:8000/api
   # Production (ganti dengan domain Anda):
   # LARAVEL_API_URL=https://yourdomain.com/api
   LARAVEL_API_TOKEN=your-secret-token-here

   # Sync Interval
   SYNC_INTERVAL=5000
   ```

### Step 5: Test Connection

```powershell
# Test koneksi ke fingerprint device
ping 192.168.0.201

# Test koneksi ke Laravel API (jika accessible dari server)
curl https://yourdomain.com/api/fingerprint/sync
```

### Step 6: Install PM2 (Process Manager)

```powershell
npm install -g pm2
npm install -g pm2-windows-startup
```

### Step 7: Start Service dengan PM2

```powershell
cd C:\Services\fingerprint-sync

# Start service
pm2 start index.js --name fingerprint-sync

# Setup auto-start saat boot Windows
pm2-startup install
pm2 save
```

### Step 8: Verify Service Running

```powershell
# List semua service
pm2 list

# Monitor service
pm2 monit

# Check logs
pm2 logs fingerprint-sync

# Check logs real-time
pm2 logs fingerprint-sync --lines 100
```

## 🔧 Service Management

### Start Service
```powershell
pm2 start fingerprint-sync
```

### Stop Service
```powershell
pm2 stop fingerprint-sync
```

### Restart Service
```powershell
pm2 restart fingerprint-sync
```

### Delete Service
```powershell
pm2 delete fingerprint-sync
```

### View Logs
```powershell
pm2 logs fingerprint-sync
```

### View Logs (Last 100 lines)
```powershell
pm2 logs fingerprint-sync --lines 100
```

## 📊 Monitoring

### Check Service Status
```powershell
pm2 status
```

### View Real-time Monitoring
```powershell
pm2 monit
```

### Check Service Info
```powershell
pm2 describe fingerprint-sync
```

## 🔍 Troubleshooting

### Service Tidak Running
1. Check PM2 status: `pm2 list`
2. Check logs: `pm2 logs fingerprint-sync`
3. Restart service: `pm2 restart fingerprint-sync`

### Error: Cannot Connect to Fingerprint
1. Verify IP address: `ping 192.168.0.201`
2. Check firewall settings
3. Verify fingerprint device is on and connected

### Error: Cannot Connect to Laravel API
1. Test URL: `curl https://yourdomain.com/api/fingerprint/sync`
2. Check internet connection
3. Verify API token is correct
4. Check Laravel application is running

### Service Auto-Start Tidak Berfungsi
1. Re-run: `pm2-startup install`
2. Save: `pm2 save`
3. Restart Windows Server dan check

## 📝 Logs Location

- PM2 Logs: `C:\Users\[Username]\.pm2\logs\`
- Application Logs: `C:\Services\fingerprint-sync\logs\fingerprint.log`

## 🔐 Security Notes

1. **API Token**: Pastikan `LARAVEL_API_TOKEN` di-set untuk production
2. **.env File**: Jangan commit `.env` ke git (sudah di .gitignore)
3. **Firewall**: Pastikan Windows Server bisa access internet (outbound)
4. **Network**: Pastikan server bisa access fingerprint device (local network)

## ✅ Production Checklist

- [ ] Node.js installed (>= 16.0.0)
- [ ] PM2 installed and configured
- [ ] Service running: `pm2 list`
- [ ] Auto-start configured: `pm2-startup install`
- [ ] Logs accessible: `pm2 logs fingerprint-sync`
- [ ] Connection to fingerprint device working
- [ ] Connection to Laravel API working
- [ ] API token configured in `.env`
- [ ] Service running 24/7 (check setelah restart server)

## 🎯 Next Steps

1. Monitor logs selama 24 jam pertama
2. Verify data ter-sync ke Laravel
3. Check attendance sheet menampilkan data dari fingerprint
4. Setup monitoring/alerting jika diperlukan

