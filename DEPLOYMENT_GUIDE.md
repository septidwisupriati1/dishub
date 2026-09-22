# KIR Antrean - Deployment Guide

**Version**: 1.0.0  
**Last Updated**: 2026-05-05  
**Environment**: Production Ready

---

## 📋 Table of Contents

1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Server Requirements](#server-requirements)
3. [Deployment Steps](#deployment-steps)
4. [Environment Configuration](#environment-configuration)
5. [Database Setup](#database-setup)
6. [SSL/HTTPS Configuration](#sslhttps-configuration)
7. [Performance Optimization](#performance-optimization)
8. [Monitoring & Logging](#monitoring--logging)
9. [Post-Deployment Verification](#post-deployment-verification)
10. [Troubleshooting](#troubleshooting)
11. [Rollback Procedures](#rollback-procedures)

---

## ✅ Pre-Deployment Checklist

### Code Quality
- [ ] All tests passing: `php artisan test` ✅ (35/35 tests)
- [ ] No compilation errors or warnings
- [ ] Code review completed
- [ ] All features tested on staging
- [ ] Git branch is clean and up-to-date

### Configuration
- [ ] Production `.env` file created (NOT checked in git)
- [ ] `APP_DEBUG=false` (CRITICAL!)
- [ ] `APP_ENV=production` (CRITICAL!)
- [ ] Strong `APP_KEY` set: `php artisan key:generate`
- [ ] Database credentials are secure
- [ ] API tokens and secrets are strong

### Security
- [ ] CORS properly configured for production domain
- [ ] Rate limiting enabled and tested
- [ ] Authentication tokens have expiration set
- [ ] Sanctum token expiration configured (1440 minutes)
- [ ] HTTPS/SSL certificate ready

### Infrastructure
- [ ] Server has sufficient resources (2GB RAM minimum)
- [ ] PHP 8.2+ installed and configured
- [ ] MySQL 8.0+ installed and optimized
- [ ] Composer dependencies locked (composer.lock)
- [ ] Node.js dependencies locked (package-lock.json)

### Backups
- [ ] Database backup created: `backup_20260505_031436.sql` ✅
- [ ] Application code backed up (Git tag)
- [ ] Backup restoration tested
- [ ] Disaster recovery plan reviewed

### Documentation
- [ ] Deployment runbook prepared
- [ ] Monitoring dashboards configured
- [ ] Alert thresholds set
- [ ] On-call procedures documented

---

## 🖥️ Server Requirements

### Minimum Specifications
```
CPU:      2 cores (4+ cores recommended)
RAM:      2 GB (4GB+ recommended for production)
Storage:  20 GB SSD (separate from backups)
Network:  1 Gbps (or cloud equivalent)
```

### Required Software

#### Server OS
- Ubuntu 22.04 LTS (recommended)
- CentOS 8 / Rocky Linux 8
- Debian 11+
- Windows Server 2019+ (not recommended for production)

#### Application Stack
```
PHP:            8.2+ (with extensions: curl, json, openssl, pdo, mbstring, dom)
MySQL:          8.0+ (UTF8MB4, 64-bit)
Nginx/Apache:   Latest stable version
Node.js:        18+ (for asset compilation)
Composer:       2.4+
Git:            2.30+
```

#### Optional Services
- Redis (caching & sessions) - recommended
- Supervisor (queue processing)
- Certbot (SSL/HTTPS automation)
- New Relic / DataDog (APM)

### PHP Extensions Required

```bash
# Ubuntu/Debian
sudo apt install php8.2-fpm php8.2-cli php8.2-mysql \
  php8.2-curl php8.2-json php8.2-openssl php8.2-pdo \
  php8.2-mbstring php8.2-xml php8.2-zip php8.2-gd \
  php8.2-bcmath php8.2-redis php8.2-memcached

# Verify installation
php -v
php -m | grep -E 'curl|json|openssl|pdo|mbstring|xml'
```

---

## 🚀 Deployment Steps

### Step 1: Clone Repository

```bash
# SSH to your server
ssh user@your-server.com

# Navigate to web directory
cd /var/www

# Clone repository
git clone <repository-url> kir-antrean
cd kir-antrean

# Verify repository
git branch -a
git log --oneline -5
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm install --legacy-peer-deps

# Build assets for production
npm run build

# Verify installations
composer show | grep laravel
npm list
```

### Step 3: Set Up Environment

```bash
# Copy environment template
cp .env.example .env

# Generate application key (if not already set)
php artisan key:generate

# Set proper permissions
sudo chown -R www-data:www-data /var/www/kir-antrean
sudo chmod -R 755 /var/www/kir-antrean
sudo chmod -R 775 /var/www/kir-antrean/storage
sudo chmod -R 775 /var/www/kir-antrean/bootstrap/cache
```

### Step 4: Configure Production Environment

Edit `.env` with production values:

```bash
# Use nano or your preferred editor
nano .env
```

**Critical Settings:**
```env
# Application
APP_NAME="KIR Antrean"
APP_ENV=production           # CRITICAL!
APP_DEBUG=false              # CRITICAL!
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antrean_kir_prod
DB_USERNAME=kir_user
DB_PASSWORD=<STRONG-PASSWORD>

# Cache & Session
CACHE_DRIVER=redis          # Better than file
SESSION_DRIVER=redis        # Better than file
QUEUE_CONNECTION=database   # Or redis

# Mail
MAIL_MAILER=sendgrid        # Or smtp for custom
MAIL_USERNAME=apikey
MAIL_PASSWORD=<SENDGRID-API-KEY>

# Security
SANCTUM_EXPIRATION=1440     # 24 hours
SANCTUM_STATEFUL_DOMAINS=yourdomain.com

# Logging
LOG_CHANNEL=daily           # Daily log rotation
LOG_LEVEL=warning           # Production level

# WhatsApp
WHATSAPP_PROVIDER=fonnte    # Your provider
FONNTE_API_TOKEN=<TOKEN>

# Security Keys (Generate these!)
APP_KEY=<GENERATED-BY-artisan-key-generate>
```

### Step 5: Set Up Database

```bash
# Create database
mysql -u root -p << EOF
CREATE DATABASE antrean_kir_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kir_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON antrean_kir_prod.* TO 'kir_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF

# Run migrations
php artisan migrate --force --env=production

# Seed database (optional - depends on your data)
php artisan db:seed --force --env=production

# Verify database
php artisan tinker
# Type: User::count() - should show users
# Type: exit
```

### Step 6: Configure Web Server

#### For Nginx (Recommended)

```bash
# Create Nginx configuration
sudo nano /etc/nginx/sites-available/kir-antrean
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    # SSL Certificates (from Certbot)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Gzip compression
    gzip on;
    gzip_types text/plain text/css text/xml text/javascript 
               application/x-javascript application/xml+rss 
               application/json application/javascript;
    gzip_disable "MSIE [1-6]\.";
    
    root /var/www/kir-antrean/public;
    index index.php index.html;
    
    # Log files
    access_log /var/log/nginx/kir-antrean-access.log;
    error_log /var/log/nginx/kir-antrean-error.log;
    
    # Deny access to sensitive files
    location ~ /\.env {
        deny all;
        return 404;
    }
    
    location ~ /storage {
        deny all;
        return 404;
    }
    
    location ~ /\.git {
        deny all;
        return 404;
    }
    
    # Static files caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 32k;
        fastcgi_buffers 16 16k;
    }
    
    # Front-controller pattern
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

**Enable Nginx Configuration:**
```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/kir-antrean \
           /etc/nginx/sites-enabled/kir-antrean

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
sudo systemctl status nginx
```

#### For Apache

```bash
# Create Apache configuration
sudo nano /etc/apache2/sites-available/kir-antrean.conf
```

**Apache Configuration:**
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    
    DocumentRoot /var/www/kir-antrean/public
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
    
    # Security headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/kir-antrean-error.log
    CustomLog ${APACHE_LOG_DIR}/kir-antrean-access.log combined
    
    # Enable .htaccess
    <Directory /var/www/kir-antrean/public>
        AllowOverride All
        Require all granted
        
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^ index.php [QSA,L]
        </IfModule>
    </Directory>
</VirtualHost>

<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    Redirect 301 / https://yourdomain.com/
</VirtualHost>
```

**Enable Apache Configuration:**
```bash
# Enable site
sudo a2ensite kir-antrean.conf

# Enable mod_rewrite
sudo a2enmod rewrite

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
sudo systemctl status apache2
```

### Step 7: Configure SSL/HTTPS

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx
# OR for Apache:
sudo apt install certbot python3-certbot-apache

# Obtain certificate
sudo certbot certonly --nginx -d yourdomain.com -d www.yourdomain.com
# OR for Apache:
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Auto-renewal setup (runs twice daily)
sudo certbot renew --dry-run

# Check renewal status
sudo systemctl status certbot.timer
```

### Step 8: Optimize Laravel

```bash
# Cache configuration (speeds up startup)
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# List all cached files
php artisan config:show | head -20
```

### Step 9: Set Up Scheduled Tasks

**For Linux (Cron):**

```bash
# Edit crontab
crontab -e

# Add this line for Laravel scheduler
* * * * * cd /var/www/kir-antrean && php artisan schedule:run >> /dev/null 2>&1

# Verify crontab
crontab -l
```

**For Windows Server (Task Scheduler):**

```powershell
# Create scheduled task
$action = New-ScheduledTaskAction -Execute "php.exe" `
  -Argument "C:\path\to\kir-antrean\artisan schedule:run"

$trigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 1) `
  -At (Get-Date) -RepetitionDuration (New-TimeSpan -Days 365)

Register-ScheduledTask -Action $action -Trigger $trigger `
  -TaskName "Laravel-Scheduler" -Description "Run Laravel scheduler"
```

### Step 10: Configure Queue Processing (Optional)

If using `QUEUE_CONNECTION=database` with Supervisor:

```bash
# Install Supervisor
sudo apt install supervisor

# Create configuration
sudo nano /etc/supervisor/conf.d/kir-antrean-worker.conf
```

**Supervisor Configuration:**
```ini
[program:kir-antrean-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kir-antrean/artisan queue:work --queue=default,low --tries=3 --timeout=90
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/supervisor/kir-antrean-worker.log
user=www-data

[group:kir-antrean]
programs=kir-antrean-worker

[inet_http_server]
port=127.0.0.1:9001
```

**Start Supervisor:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kir-antrean:*
sudo supervisorctl status
```

---

## 🔧 Environment Configuration

### Production `.env` Template

```env
# ======================
# Application Settings
# ======================
APP_NAME="KIR Antrean"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Jakarta

# ======================
# Database Configuration
# ======================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antrean_kir_prod
DB_USERNAME=kir_user
DB_PASSWORD=your_strong_password_here
DB_COLLATION=utf8mb4_unicode_ci

# ======================
# Cache Configuration
# ======================
CACHE_DRIVER=redis
CACHE_PREFIX=kir_cache_
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ======================
# Session Configuration
# ======================
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_DOMAIN=yourdomain.com
SESSION_SECURE_COOKIES=true
SESSION_HTTP_ONLY=true

# ======================
# Queue Configuration
# ======================
QUEUE_CONNECTION=database
# or
# QUEUE_CONNECTION=redis

# ======================
# Mail Configuration
# ======================
MAIL_MAILER=sendgrid
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="KIR Antrean"

# ======================
# Security Configuration
# ======================
APP_KEY=base64:xxxxxxxxxxxxx
SANCTUM_EXPIRATION=1440
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
CORS_ALLOWED_ORIGINS=https://yourdomain.com

# ======================
# Logging Configuration
# ======================
LOG_CHANNEL=daily
LOG_LEVEL=warning
LOG_DAILY_PATH=storage/logs/laravel-%Y-%m-%d.log

# ======================
# WhatsApp Configuration
# ======================
WHATSAPP_PROVIDER=fonnte
FONNTE_API_TOKEN=your_token_here
FONNTE_API_URL=https://api.fonnte.com

# ======================
# Admin Configuration
# ======================
ADMIN_EMAIL=admin@yourdomain.com
SUPPORT_EMAIL=support@yourdomain.com
```

---

## 📦 Database Setup

### Create Production Database

```bash
# Connect to MySQL
mysql -u root -p

# Create database
CREATE DATABASE antrean_kir_prod 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

# Create user
CREATE USER 'kir_user'@'localhost' 
IDENTIFIED BY 'your_very_strong_password_123!@#';

# Grant privileges
GRANT ALL PRIVILEGES ON antrean_kir_prod.* 
TO 'kir_user'@'localhost';

# For remote access (if needed)
CREATE USER 'kir_user'@'192.168.1.%' 
IDENTIFIED BY 'your_very_strong_password_123!@#';

GRANT ALL PRIVILEGES ON antrean_kir_prod.* 
TO 'kir_user'@'192.168.1.%';

# Flush privileges
FLUSH PRIVILEGES;

# Exit
EXIT;
```

### Run Migrations

```bash
# Run migrations on production database
php artisan migrate --force --env=production

# Show migration status
php artisan migrate:status

# Verify tables created
mysql -u kir_user -p antrean_kir_prod -e "SHOW TABLES;"
```

### Seed Initial Data (Optional)

```bash
# Seed database
php artisan db:seed --force --env=production

# Seed specific seeder
php artisan db:seed --class=UserSeeder --force --env=production

# Verify data
php artisan tinker
# > User::count()
# > Vehicle::count()
# > exit
```

### Database Optimization

```sql
-- Analyze tables
ANALYZE TABLE users;
ANALYZE TABLE vehicles;
ANALYZE TABLE queues;
ANALYZE TABLE test_results;
ANALYZE TABLE test_schedules;

-- Check table status
SHOW TABLE STATUS FROM antrean_kir_prod;

-- Optimize tables
OPTIMIZE TABLE users;
OPTIMIZE TABLE vehicles;
OPTIMIZE TABLE queues;
OPTIMIZE TABLE test_results;
OPTIMIZE TABLE test_schedules;
```

---

## 🔐 SSL/HTTPS Configuration

### Obtain SSL Certificate with Let's Encrypt

```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-nginx

# For Nginx
sudo certbot certonly --nginx \
  -d yourdomain.com \
  -d www.yourdomain.com

# For Apache
sudo certbot certonly --apache \
  -d yourdomain.com \
  -d www.yourdomain.com

# Verification
sudo certbot certificates

# Auto-renewal test
sudo certbot renew --dry-run
```

### SSL Configuration Best Practices

```nginx
# In Nginx configuration
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers HIGH:!aNULL:!MD5;
ssl_prefer_server_ciphers on;

# HSTS (HTTP Strict Transport Security)
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline'" always;

# Redirect HTTP to HTTPS
return 301 https://$server_name$request_uri;
```

### Verify SSL Configuration

```bash
# Test SSL
openssl s_client -connect yourdomain.com:443

# Check certificate details
openssl x509 -in /etc/letsencrypt/live/yourdomain.com/fullchain.pem -text -noout

# SSL Labs test
# Visit: https://www.ssllabs.com/ssltest/analyze.html?d=yourdomain.com

# Mozilla SSL Configuration Generator
# Visit: https://ssl-config.mozilla.org/
```

---

## ⚡ Performance Optimization

### Laravel Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize --no-dev

# Clear all caches
php artisan cache:clear
```

### PHP Configuration

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
; Memory
memory_limit = 256M

; Execution time
max_execution_time = 300
max_input_time = 300

; Upload
upload_max_filesize = 50M
post_max_size = 50M

; OPcache (speeds up PHP)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 100000
opcache.validate_timestamps = 0
opcache.revalidate_freq = 0

; Session
session.gc_maxlifetime = 14400
session.gc_probability = 1
session.gc_divisor = 100
```

### MySQL Optimization

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
; Connection pool
max_connections = 100
max_allowed_packet = 64M

; Memory
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M

; Query cache (MySQL 5.7+)
query_cache_size = 0
query_cache_type = 0

; Slow query log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

; Binary logging (for replication/backup)
log_bin = /var/log/mysql/mysql-bin.log
binlog_format = ROW
expire_logs_days = 7
```

### Nginx Optimization

```nginx
# Worker processes
worker_processes auto;

# Connection settings
worker_connections 1024;
keepalive_timeout 65;

# Buffering
client_body_buffer_size 128k;
client_max_body_size 50m;
client_header_buffer_size 1k;
large_client_header_buffers 4 8k;

# Gzip compression
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css text/xml text/javascript 
           application/json application/javascript application/xml+rss;

# FastCGI cache
fastcgi_cache_path /var/cache/nginx levels=1:2 keys_zone=my_cache:10m inactive=10m;
fastcgi_cache_key "$scheme$request_method$host$request_uri";
```

### Redis Optimization

```bash
# Edit /etc/redis/redis.conf

# Memory management
maxmemory 512mb
maxmemory-policy allkeys-lru

# Persistence (optional)
save 900 1
save 300 10
save 60 10000

# Replication (if using)
replicaof master_ip master_port
```

---

## 📊 Monitoring & Logging

### Application Logs

```bash
# View logs
tail -f /var/www/kir-antrean/storage/logs/laravel.log

# Specific errors
grep ERROR /var/www/kir-antrean/storage/logs/laravel.log

# Last 100 lines
tail -100 /var/www/kir-antrean/storage/logs/laravel.log

# Search for specific user
grep "user_id" /var/www/kir-antrean/storage/logs/laravel.log

# Real-time monitoring
watch -n 1 'tail -20 /var/www/kir-antrean/storage/logs/laravel.log'
```

### Web Server Logs

```bash
# Nginx logs
tail -f /var/log/nginx/kir-antrean-access.log
tail -f /var/log/nginx/kir-antrean-error.log

# Apache logs
tail -f /var/log/apache2/kir-antrean-access.log
tail -f /var/log/apache2/kir-antrean-error.log

# Analyze slow requests
tail -100 /var/log/nginx/kir-antrean-access.log | sort -k4 -t' ' -rn | head -20
```

### Database Logs

```bash
# MySQL slow query log
tail -f /var/log/mysql/slow-query.log

# Binary logs
mysqlbinlog /var/log/mysql/mysql-bin.000001 | less
```

### System Monitoring

```bash
# CPU and Memory
htop

# Disk usage
df -h
du -sh /var/www/kir-antrean/*

# Network connections
netstat -tulpn | grep LISTEN

# Process info
ps aux | grep php
ps aux | grep nginx
```

### Set Up Monitoring Alerts

```bash
# Create monitoring script
nano /usr/local/bin/monitor-kir-antrean.sh
```

```bash
#!/bin/bash

# Monitor disk usage
DISK=$(df /var/www/kir-antrean | awk 'NR==2 {print $5}' | cut -d'%' -f1)
if [ $DISK -gt 80 ]; then
    echo "WARNING: Disk usage is at ${DISK}%" | mail -s "Disk Alert" admin@yourdomain.com
fi

# Monitor memory
MEMORY=$(free | grep Mem | awk '{print ($3/$2) * 100.0}')
if (( $(echo "$MEMORY > 80" | bc -l) )); then
    echo "WARNING: Memory usage is at ${MEMORY}%" | mail -s "Memory Alert" admin@yourdomain.com
fi

# Check PHP-FPM status
systemctl is-active --quiet php8.2-fpm || \
    echo "ERROR: PHP-FPM is not running" | mail -s "PHP-FPM Alert" admin@yourdomain.com

# Check Nginx status
systemctl is-active --quiet nginx || \
    echo "ERROR: Nginx is not running" | mail -s "Nginx Alert" admin@yourdomain.com

# Check MySQL status
systemctl is-active --quiet mysql || \
    echo "ERROR: MySQL is not running" | mail -s "MySQL Alert" admin@yourdomain.com
```

**Add to Crontab:**
```bash
# Run every 5 minutes
*/5 * * * * /usr/local/bin/monitor-kir-antrean.sh
```

---

## ✅ Post-Deployment Verification

### Automated Verification Script

```bash
#!/bin/bash
# deployment-check.sh

echo "🔍 Deployment Verification"
echo "================================"

# 1. Check Laravel installation
echo "✓ Checking Laravel installation..."
php artisan --version

# 2. Check dependencies
echo "✓ Checking PHP dependencies..."
php -m | grep -E 'curl|json|openssl|pdo|mbstring|xml'

# 3. Check database connection
echo "✓ Checking database connection..."
php artisan migrate:status | head -5

# 4. Check file permissions
echo "✓ Checking file permissions..."
ls -ld storage bootstrap/cache

# 5. Check configuration
echo "✓ Checking configuration..."
php artisan config:show APP_ENV
php artisan config:show APP_DEBUG

# 6. Test database
echo "✓ Testing database..."
php artisan tinker << 'EOF'
echo "Users: " . User::count();
echo "Vehicles: " . Vehicle::count();
exit;
EOF

# 7. Check API health
echo "✓ Checking API health..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" https://yourdomain.com/api/health

# 8. Check SSL certificate
echo "✓ Checking SSL certificate..."
echo | openssl s_client -servername yourdomain.com -connect yourdomain.com:443 2>/dev/null | \
  openssl x509 -noout -dates

# 9. Check disk space
echo "✓ Checking disk space..."
df -h /var/www/kir-antrean

# 10. Check service status
echo "✓ Checking service status..."
systemctl status nginx --no-pager | grep Active
systemctl status php8.2-fpm --no-pager | grep Active
systemctl status mysql --no-pager | grep Active

echo "================================"
echo "✅ Deployment verification complete"
```

**Run verification:**
```bash
chmod +x deployment-check.sh
./deployment-check.sh
```

### Manual Verification

```bash
# 1. Access application
curl -I https://yourdomain.com

# 2. Check API response
curl https://yourdomain.com/api/health -H "Accept: application/json"

# 3. Test authentication
curl -X POST https://yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# 4. Test a protected endpoint
curl https://yourdomain.com/api/vehicles \
  -H "Authorization: Bearer YOUR_TOKEN"

# 5. Check response headers
curl -i https://yourdomain.com/api/health | grep -E "Content-Type|X-"
```

---

## 🔄 Rollback Procedures

### Rollback to Previous Version

```bash
# If using Git tags
git tag -l                           # List all tags
git checkout <previous-tag>          # Switch to previous version

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations if needed
php artisan migrate

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Verify rollback
php artisan --version
curl -I https://yourdomain.com
```

### Database Rollback

```bash
# Rollback last migration batch
php artisan migrate:rollback

# Rollback specific number of batches
php artisan migrate:rollback --step=2

# Rollback all migrations
php artisan migrate:rollback --reset

# Restore from backup
mysql -u kir_user -p antrean_kir_prod < backup_20260505_031436.sql

# Verify restore
mysql -u kir_user -p antrean_kir_prod -e "SELECT COUNT(*) FROM users;"
```

### Full Disaster Recovery

```bash
# 1. Stop services
sudo systemctl stop nginx
sudo systemctl stop php8.2-fpm
sudo systemctl stop mysql

# 2. Backup current data (just in case)
mysqldump -u kir_user -p antrean_kir_prod > /tmp/current-backup.sql

# 3. Restore from backup
mysql -u kir_user -p antrean_kir_prod < /path/to/backup.sql

# 4. Restore application code
git reset --hard <commit-hash>
git checkout <previous-tag>

# 5. Start services
sudo systemctl start mysql
sudo systemctl start php8.2-fpm
sudo systemctl start nginx

# 6. Verify
curl -I https://yourdomain.com
php artisan tinker
# > User::count()
# > exit
```

---

## 🐛 Troubleshooting

### Common Deployment Issues

#### 1. "502 Bad Gateway" Error

```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Check PHP logs
tail -f /var/log/php8.2-fpm.log

# Check Nginx error log
tail -f /var/log/nginx/kir-antrean-error.log

# Verify socket permissions
ls -l /run/php/php8.2-fpm.sock
```

#### 2. "500 Internal Server Error"

```bash
# Check Laravel logs
tail -f /var/www/kir-antrean/storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data /var/www/kir-antrean/storage
sudo chmod -R 775 /var/www/kir-antrean/storage

# Clear caches
php artisan cache:clear
php artisan config:clear

# Check database connection
php artisan tinker
# > DB::connection()->getPdo();
```

#### 3. Database Connection Error

```bash
# Test MySQL connection
mysql -h 127.0.0.1 -u kir_user -p -e "SELECT 1"

# Verify .env credentials
grep DB_ /var/www/kir-antrean/.env

# Check MySQL is running
sudo systemctl status mysql

# Restart MySQL
sudo systemctl restart mysql

# View MySQL error log
tail -f /var/log/mysql/error.log
```

#### 4. SSL Certificate Issues

```bash
# Check certificate expiration
openssl x509 -enddate -noout \
  -in /etc/letsencrypt/live/yourdomain.com/fullchain.pem

# Renew certificate manually
sudo certbot renew --force-renewal

# Check Certbot auto-renewal
sudo systemctl status certbot.timer

# View renewal log
sudo tail -f /var/log/letsencrypt/letsencrypt.log
```

#### 5. High Memory Usage

```bash
# Check memory usage
free -h

# Find memory-heavy processes
ps aux --sort=-%mem | head -10

# Check Redis memory
redis-cli info memory

# Check PHP process memory
ps aux | grep php

# Increase PHP memory limit
nano /etc/php/8.2/fpm/php.ini
# Find: memory_limit = 128M
# Change to: memory_limit = 256M

# Restart PHP
sudo systemctl restart php8.2-fpm
```

#### 6. Slow Application Performance

```bash
# Check slow query log
tail -f /var/log/mysql/slow-query.log

# Check database indexes
php artisan tinker
# > DB::table('users')->explain()->get();

# Analyze tables
php artisan db:seed --seeder=AnalyzeTablesSeeder

# Clear application cache
php artisan cache:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache

# Check top CPU-intensive processes
top -b -n 1 | head -20
```

---

## 📋 Deployment Checklist Summary

### Pre-Deployment
- [ ] All tests passing (35/35)
- [ ] Code review completed
- [ ] Security audit passed
- [ ] Database backup created
- [ ] Environment variables prepared
- [ ] SSL certificate ready

### Deployment
- [ ] Repository cloned
- [ ] Dependencies installed (composer, npm)
- [ ] Assets built (npm run build)
- [ ] Environment configured
- [ ] Database setup and migrations run
- [ ] Web server configured
- [ ] SSL/HTTPS enabled
- [ ] Cron jobs configured
- [ ] Monitoring configured

### Post-Deployment
- [ ] Application accessible via HTTPS
- [ ] API endpoints working
- [ ] Database connected and populated
- [ ] Email configured and tested
- [ ] WhatsApp integration tested
- [ ] Logs monitored and alerts configured
- [ ] Backups verified and tested
- [ ] Performance baseline established

---

## 🚀 Quick Deployment Summary

**For Experienced DevOps:**

```bash
# Quick deployment script
git clone <repo> && cd kir-antrean
composer install --no-dev --optimize-autoloader
npm install --legacy-peer-deps && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --force
php artisan config:cache && php artisan route:cache
sudo chown -R www-data:www-data . && sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
sudo systemctl restart nginx php8.2-fpm mysql
php artisan test
echo "✅ Deployment complete"
```

---

**Version**: 1.0.0  
**Last Updated**: 2026-05-05  
**Environment**: KIR Antrean v1.0.0  
**Status**: ✅ Production Ready
