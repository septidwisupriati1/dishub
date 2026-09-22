# KIR Antrean - Environment & Configuration Files

**Version**: 1.0.0  
**Last Updated**: 2026-05-05  
**Status**: ✅ All Tests Passing (35/35)

---

## 📋 Files Created

### 1. **Environment Configuration**

#### `.env.example` ✅
- Template for environment variables
- Complete configuration options with documentation
- Includes sections for:
  - Application settings
  - Database configuration
  - Cache & session drivers
  - Mail configuration (SMTP, Gmail, SendGrid)
  - WhatsApp integration (multiple providers)
  - Notification settings
  - Audit & logging
  - Security settings
  - Rate limiting

**Usage:**
```bash
cp .env.example .env
php artisan key:generate
```

---

### 2. **Configuration Documentation**

#### `CONFIG_SETUP.md` 📖
Comprehensive guide covering:

- ✅ Environment variable explanation
- ✅ Core configuration files (app.php, database.php, sanctum.php, etc.)
- ✅ Database configuration & setup
- ✅ WhatsApp integration (all 4 providers)
- ✅ Email configuration (development & production)
- ✅ Security configuration & HTTPS setup
- ✅ Rate limiting configuration
- ✅ Production deployment checklist
- ✅ Troubleshooting guide

**Sections:**
1. Environment Setup
2. Configuration Files (7 Laravel config files explained)
3. Database Configuration (MySQL setup)
4. WhatsApp Integration (Twilio, Fonnte, Ultramsg, Wablas)
5. Email Configuration
6. Security Configuration
7. Rate Limiting
8. Production Deployment

---

### 3. **Database Backup & Recovery**

#### `BACKUP_RECOVERY.md` 💾
Complete guide for database backup and disaster recovery:

- ✅ Backup strategies (Full, Incremental, Transaction Log)
- ✅ Creating manual backups
- ✅ Automated backup scheduling (Windows Task Scheduler, cron)
- ✅ Restore procedures (full, partial, point-in-time)
- ✅ Backup verification & testing
- ✅ Disaster recovery steps
- ✅ Corruption recovery
- ✅ Backup retention policies
- ✅ Off-site backup recommendations
- ✅ Monitoring & alerts

**Backup Created:**
- Location: `database/backups/`
- Latest: `backup_20260505_031436.sql` (20.48 KB)

**Scripts Included:**
- PowerShell backup script
- Batch file backup script
- Backup monitoring script
- Automatic cleanup script

---

### 4. **Setup Checklist**

#### `SETUP_CHECKLIST.md` ✓
Quick reference for developers setting up the project:

- ✅ Quick start (5 minutes)
- ✅ Complete setup checklist (13 steps)
- ✅ Environment variables guide
- ✅ Automated setup scripts (PowerShell & Bash)
- ✅ Troubleshooting common issues
- ✅ Post-setup development commands

**Quick Setup:**
```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
npm run dev
```

---

### 5. **Database Backup**

#### `database/backups/backup_20260505_031436.sql` 💾
- Complete database backup with schema and data
- Size: 20.48 KB
- Includes all tables: users, vehicles, queues, test_results, etc.
- Can be restored with: `mysql -u root antrean_uji_kendaraan < backup_file.sql`

---

## 🚀 Quick Start

### For New Developers

```bash
# 1. Clone repository
git clone <repo-url>
cd kir-antrean

# 2. Copy environment file
cp .env.example .env

# 3. Install dependencies
composer install
npm install

# 4. Generate app key
php artisan key:generate

# 5. Set up database
# (Update DB credentials in .env if needed)
php artisan migrate
php artisan db:seed

# 6. Start development
php artisan serve      # Terminal 1
npm run dev            # Terminal 2
```

**Access Application:**
- 🌐 Web: http://localhost:8000
- 📧 Mailpit: http://localhost:8025
- 📚 API Docs: See `API_DOCUMENTATION.md`

---

## 📁 File Structure

```
kir-antrean/
├── .env                          # Current environment (git ignored)
├── .env.example                  # ✅ NEW - Environment template
├── CONFIG_SETUP.md               # ✅ NEW - Configuration guide
├── SETUP_CHECKLIST.md            # ✅ NEW - Setup instructions
├── BACKUP_RECOVERY.md            # ✅ NEW - Backup & recovery guide
├── API_DOCUMENTATION.md          # API endpoint reference
├── API_POSTMAN_COLLECTION.json   # Postman collection
│
├── database/
│   ├── backups/
│   │   └── backup_20260505_031436.sql  # ✅ NEW - Database backup
│   ├── migrations/
│   ├── seeders/
│   └── ...
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── sanctum.php
│   └── ... (other config files)
│
├── app/
│   ├── Models/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   ├── Requests/
│   │   └── Kernel.php
│   ├── Providers/
│   ├── Services/
│   └── Console/
│
├── tests/
│   ├── Feature/
│   ├── Unit/
│   └── TestCase.php
│
├── storage/
│   ├── logs/
│   └── ...
│
├── routes/
│   ├── api.php
│   └── web.php
│
└── ...
```

---

## ⚙️ Environment Variables Summary

### Development (`.env`)
```env
APP_ENV=local
APP_DEBUG=true
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
MAIL_MAILER=mailpit
LOG_LEVEL=debug
WHATSAPP_PROVIDER=twilio
```

### Production (`.env.production`)
```env
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=database
MAIL_MAILER=sendgrid
LOG_LEVEL=warning
SANCTUM_EXPIRATION=1440
```

---

## 🗄️ Database Configuration

### Local Development
- **Host**: 127.0.0.1
- **Port**: 3306
- **Database**: antrean_uji_kendaraan
- **User**: root
- **Password**: (empty)

### Create Database
```bash
mysql -u root -e "CREATE DATABASE antrean_uji_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

### Backup & Restore
```bash
# Backup
mysqldump -u root antrean_uji_kendaraan > backup.sql

# Restore
mysql -u root antrean_uji_kendaraan < backup.sql

# Quick restore from existing backup
mysql -u root antrean_uji_kendaraan < database/backups/backup_20260505_031436.sql
```

---

## 🔧 WhatsApp Configuration

### Supported Providers

| Provider | Setup | Cost | Speed |
|----------|-------|------|-------|
| **Twilio** | ⭐⭐⭐ Easy | $$$ | Fast |
| **Fonnte** | ⭐⭐ Medium | $$ | Fast |
| **Ultramsg** | ⭐ Hard | $ | Medium |
| **Wablas** | ⭐⭐ Medium | $ | Fast |

### Configuration in `.env`

**Twilio:**
```env
WHATSAPP_PROVIDER=twilio
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxx
```

**Fonnte:**
```env
WHATSAPP_PROVIDER=fonnte
FONNTE_API_TOKEN=xxxxxxxxxxxx
FONNTE_API_URL=https://api.fonnte.com
```

See `CONFIG_SETUP.md` for detailed setup instructions.

---

## 📧 Email Configuration

### Local Development (Mailpit)
```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```
- Access: http://localhost:8025

### Production (Gmail)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### Production (SendGrid)
```env
MAIL_MAILER=sendgrid
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxx
```

---

## 🔒 Security Checklist

### Development
- ✅ `APP_DEBUG=true` (for debugging)
- ✅ `APP_ENV=local`
- ✅ Database password optional

### Production
- ❌ `APP_DEBUG=false` (MUST BE FALSE!)
- ✅ `APP_ENV=production`
- ✅ Strong database password
- ✅ HTTPS enabled
- ✅ CORS restricted to allowed domains
- ✅ Rate limiting enabled
- ✅ Audit logging enabled

---

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test
```bash
php artisan test tests/Feature/AuthTest.php
```

### Test with Coverage
```bash
php artisan test --coverage
```

### Current Status
✅ **All 35 tests passing** (140 assertions)
- AuthTest: 10/10 ✓
- VehicleTest: 7/7 ✓
- QueueTest: 7/7 ✓
- TestResultTest: 4/4 ✓
- TestScheduleTest: 5/5 ✓
- WhatsappConfigTest: 2/2 ✓

---

## 📖 Documentation Files

| File | Purpose | Status |
|------|---------|--------|
| `.env.example` | Environment template | ✅ Ready |
| `CONFIG_SETUP.md` | Configuration guide | ✅ Ready |
| `SETUP_CHECKLIST.md` | Setup instructions | ✅ Ready |
| `BACKUP_RECOVERY.md` | Backup & recovery guide | ✅ Ready |
| `API_DOCUMENTATION.md` | API reference | ✅ Ready |
| `API_POSTMAN_COLLECTION.json` | Postman collection | ✅ Ready |

---

## 🛠️ Maintenance Commands

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuild Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Commands
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Refresh database
php artisan migrate:refresh

# Seed database
php artisan db:seed
```

### Create New Components
```bash
# Model with migration
php artisan make:model ModelName -m

# Controller
php artisan make:controller ControllerName

# Request class
php artisan make:request StoreRequest

# Migration
php artisan make:migration create_table_name
```

---

## 📊 Monitoring & Logs

### Log Location
```
storage/logs/
├── laravel.log           # Main application log
└── laravel-*.log         # Daily logs (if daily driver)
```

### View Logs
```bash
# Last 100 lines
tail -100 storage/logs/laravel.log

# Real-time monitoring
tail -f storage/logs/laravel.log

# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 100
Get-Content storage/logs/laravel.log -Wait
```

### Log Configuration (`.env`)
```env
LOG_CHANNEL=stack              # Local dev
LOG_CHANNEL=daily              # Production
LOG_LEVEL=debug                # Development
LOG_LEVEL=warning              # Production
```

---

## ❓ Troubleshooting

### "APP_KEY not set"
```bash
php artisan key:generate
```

### "Database connection failed"
```bash
# Check MySQL is running
# Verify .env database credentials
# Ensure database exists
mysql -u root -e "SHOW DATABASES;"
```

### "Class not found"
```bash
composer dump-autoload
```

### "Tests failing"
```bash
# Create test database
mysql -u root -e "CREATE DATABASE antrean_uji_kendaraan_test"

# Run migrations for test
php artisan migrate --env=testing

# Run tests
php artisan test
```

### "Permission denied"
```bash
# Fix permissions (Linux/macOS)
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

---

## 🌐 Useful URLs

### Local Development
- **Application**: http://localhost:8000
- **API**: http://localhost:8000/api
- **Mailpit (Email)**: http://localhost:8025

### External Services (if configured)
- **Twilio Console**: https://www.twilio.com/console
- **Fonnte Dashboard**: https://www.fonnte.com
- **SendGrid**: https://sendgrid.com

---

## 📞 Support

For issues or questions:

1. Check `SETUP_CHECKLIST.md` for common issues
2. Read `CONFIG_SETUP.md` for configuration details
3. Review `BACKUP_RECOVERY.md` for database issues
4. Check application logs: `storage/logs/laravel.log`

---

## 📝 Version History

| Date | Version | Changes |
|------|---------|---------|
| 2026-05-05 | 1.0.0 | Initial environment setup documentation |

---

**Last Updated**: 2026-05-05  
**Application**: KIR Antrean v1.0.0  
**Tests**: ✅ 35/35 Passing
