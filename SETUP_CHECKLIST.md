# Environment Setup Checklist

**Version**: 1.0.0  
**Last Updated**: 2026-05-05  
**Application**: KIR Antrean (Vehicle Inspection Queue System)

---

## Quick Start (5 minutes)

### For New Developers

```bash
# 1. Clone repository (skip if already cloned)
git clone <repository-url> kir-antrean
cd kir-antrean

# 2. Install dependencies
composer install
npm install

# 3. Set up environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Run migrations
php artisan migrate

# 6. Seed database
php artisan db:seed

# 7. Start development server
php artisan serve

# 8. In another terminal, start Vite
npm run dev
```

---

## Complete Setup Checklist

### Prerequisites

- [ ] PHP 8.1+ installed
- [ ] Composer installed
- [ ] Node.js & npm installed
- [ ] MySQL 8.0+ running
- [ ] Git installed
- [ ] Code editor (VS Code, PHPStorm, etc.)

### Step 1: Project Setup

- [ ] Clone repository
- [ ] Navigate to project directory
- [ ] Verify `.gitignore` includes `.env`

### Step 2: Environment Configuration

- [ ] Copy `.env.example` to `.env`
- [ ] Update `APP_NAME="KIR Antrean"`
- [ ] Update `APP_ENV=local` (for development)
- [ ] Update `APP_DEBUG=true` (for development)
- [ ] Generate `APP_KEY`: `php artisan key:generate`

### Step 3: Database Setup

- [ ] Verify MySQL is running
- [ ] Update `DB_HOST=127.0.0.1`
- [ ] Update `DB_PORT=3306`
- [ ] Update `DB_DATABASE=antrean_uji_kendaraan`
- [ ] Update `DB_USERNAME=root`
- [ ] Update `DB_PASSWORD=` (empty for local)
- [ ] Create database: `mysql -u root -e "CREATE DATABASE antrean_uji_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"`

### Step 4: PHP Dependencies

- [ ] Run `composer install`
- [ ] Verify no errors during installation
- [ ] Check vendor directory exists

### Step 5: Node Dependencies

- [ ] Run `npm install`
- [ ] Verify node_modules directory exists
- [ ] Check package-lock.json is updated

### Step 6: Database Migrations

- [ ] Run `php artisan migrate`
- [ ] Verify all migrations completed successfully
- [ ] Check tables created in database

### Step 7: Database Seeding (Optional)

- [ ] Run `php artisan db:seed`
- [ ] Verify test data created
- [ ] Check users, vehicles, queues tables have data

### Step 8: Cache & Configuration

- [ ] Clear cache: `php artisan cache:clear`
- [ ] Clear config: `php artisan config:clear`
- [ ] Clear routes: `php artisan route:clear`
- [ ] Cache config: `php artisan config:cache`

### Step 9: Testing Setup

- [ ] Create `.env.testing` from `.env.example`
- [ ] Update test database: `DB_DATABASE=antrean_uji_kendaraan_test`
- [ ] Run migrations for test DB: `php artisan migrate --env=testing`
- [ ] Run tests: `php artisan test`
- [ ] Verify all tests pass (35/35)

### Step 10: WhatsApp Configuration (Optional)

- [ ] Set `WHATSAPP_PROVIDER=twilio`
- [ ] Add provider credentials:
  - [ ] `TWILIO_ACCOUNT_SID=xxx`
  - [ ] `TWILIO_AUTH_TOKEN=xxx`
- [ ] Test via API: `POST /api/whatsapp-configs/{id}/test`

### Step 11: Email Configuration (Optional)

- For Local Development:
  - [ ] Set `MAIL_MAILER=smtp`
  - [ ] Set `MAIL_HOST=mailpit`
  - [ ] Set `MAIL_PORT=1025`

- For Production:
  - [ ] Set `MAIL_MAILER=smtp`
  - [ ] Set `MAIL_HOST=smtp.gmail.com`
  - [ ] Set `MAIL_USERNAME=your-email@gmail.com`
  - [ ] Set `MAIL_PASSWORD=your-app-password`
  - [ ] Set `MAIL_ENCRYPTION=tls`

### Step 12: Development Server

- [ ] Start Laravel: `php artisan serve`
- [ ] Start Vite: `npm run dev`
- [ ] Access application: `http://localhost:8000`
- [ ] Verify no errors

### Step 13: IDE Configuration (VS Code)

- [ ] Install PHP Intelephense extension
- [ ] Install Laravel Extension Pack
- [ ] Install REST Client extension
- [ ] Configure PHP path: `Settings > PHP > Validate: Executable Path`

---

## Environment Variables Guide

### Application

```env
APP_NAME=KIR Antrean           # Display name
APP_ENV=local                   # local, staging, production
APP_KEY=base64:xxx             # Generated key
APP_DEBUG=true                  # Enable debug (false in production!)
APP_URL=http://localhost:8000   # Base URL
```

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antrean_uji_kendaraan
DB_USERNAME=root
DB_PASSWORD=
```

### Cache & Session

```env
CACHE_DRIVER=file              # or redis, memcached
SESSION_DRIVER=file            # or database, redis
QUEUE_CONNECTION=sync          # or database, redis
```

### API & Security

```env
SANCTUM_EXPIRATION=1440        # Token expiration (minutes)
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
RATE_LIMIT_API=200,1           # 200 requests per minute
```

### WhatsApp (Optional)

```env
WHATSAPP_PROVIDER=twilio
WHATSAPP_API_KEY=xxx
WHATSAPP_API_SECRET=xxx
WHATSAPP_PHONE_NUMBER=+62812345678
```

### Email (Optional)

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS=kir-antrean@example.com
```

---

## Automated Setup Script

### PowerShell Setup Script (Windows)

**File**: `setup.ps1`

```powershell
# KIR Antrean - Automated Setup Script

Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  KIR Antrean - Development Setup      ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Color output helper
function Write-Success {
    param([string]$Message)
    Write-Host "✓ $Message" -ForegroundColor Green
}

function Write-Error {
    param([string]$Message)
    Write-Host "✗ $Message" -ForegroundColor Red
}

function Write-Info {
    param([string]$Message)
    Write-Host "ℹ $Message" -ForegroundColor Cyan
}

# Check prerequisites
Write-Info "Checking prerequisites..."

# Check PHP
if (Get-Command php -ErrorAction SilentlyContinue) {
    Write-Success "PHP found"
} else {
    Write-Error "PHP not found. Please install PHP 8.1+"
    exit 1
}

# Check Composer
if (Get-Command composer -ErrorAction SilentlyContinue) {
    Write-Success "Composer found"
} else {
    Write-Error "Composer not found. Please install Composer"
    exit 1
}

# Check Node.js
if (Get-Command node -ErrorAction SilentlyContinue) {
    Write-Success "Node.js found"
} else {
    Write-Error "Node.js not found. Please install Node.js"
    exit 1
}

# Check MySQL
if (Get-Command mysql -ErrorAction SilentlyContinue) {
    Write-Success "MySQL found"
} else {
    Write-Error "MySQL not found. Please install MySQL 8.0+"
    exit 1
}

Write-Host ""
Write-Info "Installing dependencies..."

# Install PHP dependencies
Write-Info "Running composer install..."
composer install
if ($LASTEXITCODE -eq 0) {
    Write-Success "Composer dependencies installed"
} else {
    Write-Error "Composer install failed"
    exit 1
}

# Install Node dependencies
Write-Info "Running npm install..."
npm install
if ($LASTEXITCODE -eq 0) {
    Write-Success "NPM dependencies installed"
} else {
    Write-Error "NPM install failed"
    exit 1
}

Write-Host ""
Write-Info "Setting up environment..."

# Copy .env if doesn't exist
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Success ".env file created"
} else {
    Write-Info ".env file already exists"
}

# Generate APP_KEY if not set
$envContent = Get-Content ".env"
if ($envContent -match "APP_KEY=$") {
    Write-Info "Generating APP_KEY..."
    php artisan key:generate
    Write-Success "APP_KEY generated"
} else {
    Write-Info "APP_KEY already set"
}

Write-Host ""
Write-Info "Setting up database..."

# Create database
Write-Info "Creating database..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS antrean_uji_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
Write-Success "Database created"

# Run migrations
Write-Info "Running migrations..."
php artisan migrate
if ($LASTEXITCODE -eq 0) {
    Write-Success "Migrations completed"
} else {
    Write-Error "Migrations failed"
}

# Seed database (optional)
$seedResponse = Read-Host "Seed database with test data? (y/n)"
if ($seedResponse -eq "y") {
    Write-Info "Seeding database..."
    php artisan db:seed
    Write-Success "Database seeded"
}

Write-Host ""
Write-Info "Clearing caches..."

php artisan cache:clear | Out-Null
php artisan config:clear | Out-Null
php artisan route:clear | Out-Null
Write-Success "Caches cleared"

Write-Host ""
Write-Success "Setup completed successfully!"
Write-Host ""
Write-Info "Next steps:"
Write-Host "  1. Start Laravel: php artisan serve"
Write-Host "  2. Start Vite: npm run dev"
Write-Host "  3. Visit: http://localhost:8000"
Write-Host ""
```

### Bash Setup Script (Linux/macOS)

**File**: `setup.sh`

```bash
#!/bin/bash

# KIR Antrean - Automated Setup Script

echo "╔════════════════════════════════════════╗"
echo "║  KIR Antrean - Development Setup      ║"
echo "╚════════════════════════════════════════╝"
echo ""

# Color output helpers
success() { echo "✓ $1" >&2; }
error() { echo "✗ $1" >&2; }
info() { echo "ℹ $1" >&2; }

# Check prerequisites
info "Checking prerequisites..."

command -v php &> /dev/null && success "PHP found" || { error "PHP not found"; exit 1; }
command -v composer &> /dev/null && success "Composer found" || { error "Composer not found"; exit 1; }
command -v node &> /dev/null && success "Node.js found" || { error "Node.js not found"; exit 1; }
command -v mysql &> /dev/null && success "MySQL found" || { error "MySQL not found"; exit 1; }

echo ""
info "Installing dependencies..."

# Install PHP dependencies
info "Running composer install..."
composer install && success "Composer dependencies installed" || { error "Composer install failed"; exit 1; }

# Install Node dependencies
info "Running npm install..."
npm install && success "NPM dependencies installed" || { error "NPM install failed"; exit 1; }

echo ""
info "Setting up environment..."

# Copy .env if doesn't exist
if [ ! -f ".env" ]; then
    cp .env.example .env
    success ".env file created"
else
    info ".env file already exists"
fi

# Generate APP_KEY
grep -q "^APP_KEY=$" .env && {
    info "Generating APP_KEY..."
    php artisan key:generate
    success "APP_KEY generated"
} || info "APP_KEY already set"

echo ""
info "Setting up database..."

# Create database
info "Creating database..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS antrean_uji_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
success "Database created"

# Run migrations
info "Running migrations..."
php artisan migrate && success "Migrations completed" || { error "Migrations failed"; }

# Seed database (optional)
read -p "Seed database with test data? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    info "Seeding database..."
    php artisan db:seed && success "Database seeded"
fi

echo ""
info "Clearing caches..."
php artisan cache:clear > /dev/null
php artisan config:clear > /dev/null
php artisan route:clear > /dev/null
success "Caches cleared"

echo ""
success "Setup completed successfully!"
echo ""
info "Next steps:"
echo "  1. Start Laravel: php artisan serve"
echo "  2. Start Vite: npm run dev"
echo "  3. Visit: http://localhost:8000"
echo ""
```

### Running Setup Script

#### Windows (PowerShell)
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\setup.ps1
```

#### Linux/macOS
```bash
chmod +x setup.sh
./setup.sh
```

---

## Troubleshooting

### Common Setup Issues

#### Issue: "PHP not found"
```bash
# Add PHP to PATH or use full path
C:\php\php.exe artisan key:generate

# Or install Laragon which includes PHP
# Download from: https://laragon.org
```

#### Issue: "Composer not found"
```bash
# Install Composer
# Download from: https://getcomposer.org

# Or install globally
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
```

#### Issue: "Database connection failed"
```bash
# Check MySQL is running
# Verify credentials in .env
# Ensure database exists
mysql -u root -e "SHOW DATABASES;"
```

#### Issue: "SQLSTATE[HY000]: General error"
```bash
# Clear cache
php artisan cache:clear

# Recreate database
php artisan migrate:refresh
```

#### Issue: "APP_KEY not set"
```bash
php artisan key:generate
```

#### Issue: Tests failing
```bash
# Create test database
mysql -u root -e "CREATE DATABASE antrean_uji_kendaraan_test"

# Run migrations for test
php artisan migrate --env=testing

# Run tests
php artisan test
```

---

## After Setup

### Running Application

```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start Vite asset compilation
npm run dev

# Terminal 3 (optional): Queue worker
php artisan queue:work
```

### Accessing Application

- **Web Application**: http://localhost:8000
- **Mailpit (Email)**: http://localhost:8025
- **API Documentation**: http://localhost:8000/api/docs
- **Postman Collection**: Import `API_POSTMAN_COLLECTION.json`

### First Login

- **Email**: Test user created during seeding
- **Password**: Check `database/seeders/DatabaseSeeder.php`

### Development Commands

```bash
# Run tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthTest.php

# Create model with migration
php artisan make:model ModelName -m

# Create controller
php artisan make:controller ControllerName

# Create migration
php artisan make:migration create_table_name

# Cache configuration (production)
php artisan config:cache
php artisan route:cache
```

---

**For detailed configuration**, see `CONFIG_SETUP.md`  
**For API documentation**, see `API_DOCUMENTATION.md`  
**For database backup**, see `BACKUP_RECOVERY.md`
