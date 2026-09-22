# Database Backup & Recovery Guide

**Version**: 1.0.0  
**Last Updated**: 2026-05-05  
**Application**: KIR Antrean (Vehicle Inspection Queue System)

---

## Table of Contents

1. [Backup Strategies](#backup-strategies)
2. [Creating Backups](#creating-backups)
3. [Restoring from Backup](#restoring-from-backup)
4. [Automated Backups](#automated-backups)
5. [Backup Verification](#backup-verification)
6. [Disaster Recovery](#disaster-recovery)

---

## Backup Strategies

### Backup Types

#### 1. **Full Database Backup**
- Complete copy of all database and data
- Size: ~20 KB per transaction
- Frequency: Daily
- Retention: 7-30 days

#### 2. **Incremental Backup**
- Only changed data since last backup
- Size: ~5-10 KB
- Frequency: Every 6 hours
- Retention: 3-7 days

#### 3. **Transaction Log Backup**
- Binary logs for point-in-time recovery
- Frequency: Real-time
- Retention: 30 days

---

## Creating Backups

### Manual Backup

#### Using MySQL Command Line (Laragon)

```bash
# Navigate to MySQL bin directory
cd "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"

# Full database backup
mysqldump -u root antrean_uji_kendaraan > backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql

# Backup with structure and data
mysqldump -u root --no-data antrean_uji_kendaraan > structure_only.sql

# Backup specific table
mysqldump -u root antrean_uji_kendaraan users > users_backup.sql

# Compressed backup (gzip)
mysqldump -u root antrean_uji_kendaraan | gzip > backup.sql.gz
```

#### Using PowerShell Script

```powershell
# Script to create timestamped backup
$mysqlBin = "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"
$backupDir = "c:\laragon\www\kir-antrean\database\backups"
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupFile = "$backupDir\backup_$timestamp.sql"

# Create backup directory if not exists
if (-not (Test-Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir | Out-Null
}

# Create backup
& "$mysqlBin\mysqldump.exe" -u root antrean_uji_kendaraan | Out-File -FilePath $backupFile -Encoding ASCII

# Verify backup
if (Test-Path $backupFile) {
    $size = [math]::Round((Get-Item $backupFile).Length / 1MB, 2)
    Write-Host "✓ Backup created: $backupFile ($size MB)"
}
```

#### Using Laravel Artisan (if backup package installed)

```bash
# Generate backup
php artisan backup:run

# List backups
php artisan backup:list

# Restore from backup
php artisan backup:restore
```

### Backup File Locations

```
database/backups/
├── backup_20260505_031436.sql
├── backup_20260504_031436.sql
├── backup_20260503_031436.sql
└── README.md
```

### What Gets Backed Up

✅ **Database Schema**
- Tables
- Columns
- Indexes
- Constraints
- Triggers

✅ **Data**
- All records
- User data
- Queue records
- Test results

❌ **NOT Backed Up**
- Uploaded files (in `storage/`)
- Temporary files
- Log files

---

## Restoring from Backup

### Restore Full Database

#### Using MySQL Command Line

```bash
# Restore from backup file
cd "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"

mysql -u root antrean_uji_kendaraan < C:\path\to\backup_20260505_031436.sql
```

#### Using PowerShell

```powershell
$mysqlBin = "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"
$backupFile = "C:\laragon\www\kir-antrean\database\backups\backup_20260505_031436.sql"

# Restore backup
& "$mysqlBin\mysql.exe" -u root antrean_uji_kendaraan < $backupFile

Write-Host "✓ Database restored from backup"
```

### Restore Specific Table

```bash
# Extract and restore specific table from backup
cd "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"

# Get only CREATE TABLE for a specific table
mysql -u root antrean_uji_kendaraan < backup_users_only.sql
```

### Restore to Different Database

```bash
# Create new database
mysql -u root -e "CREATE DATABASE antrean_uji_kendaraan_restored"

# Restore to new database
mysql -u root antrean_uji_kendaraan_restored < backup_20260505_031436.sql
```

### Point-in-Time Recovery

```bash
# Restore to specific timestamp
# 1. Restore full backup
mysql -u root antrean_uji_kendaraan < backup_full.sql

# 2. Apply binary logs up to specific time
mysqlbinlog --start-date="2026-05-05 10:00:00" \
            --stop-date="2026-05-05 11:00:00" \
            /var/log/mysql/mysql-bin.000001 | \
mysql -u root antrean_uji_kendaraan
```

---

## Automated Backups

### Windows Task Scheduler

#### 1. Create PowerShell Script

**File**: `backup_database.ps1`

```powershell
# Database backup script for KIR Antrean

$mysqlBin = "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"
$backupDir = "c:\laragon\www\kir-antrean\database\backups"
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupFile = "$backupDir\backup_$timestamp.sql"
$logFile = "$backupDir\backup.log"

# Create backup directory if needed
if (-not (Test-Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir | Out-Null
}

# Create backup
$startTime = Get-Date
"[{0}] Starting backup..." -f $startTime | Tee-Object -FilePath $logFile -Append

try {
    & "$mysqlBin\mysqldump.exe" -u root antrean_uji_kendaraan | Out-File -FilePath $backupFile -Encoding ASCII
    
    $size = [math]::Round((Get-Item $backupFile).Length / 1MB, 2)
    $endTime = Get-Date
    $duration = ($endTime - $startTime).TotalSeconds
    
    "[{0}] Backup completed successfully" -f $endTime | Tee-Object -FilePath $logFile -Append
    "[{0}] File: $backupFile" -f $endTime | Tee-Object -FilePath $logFile -Append
    "[{0}] Size: $size MB" -f $endTime | Tee-Object -FilePath $logFile -Append
    "[{0}] Duration: $duration seconds" -f $endTime | Tee-Object -FilePath $logFile -Append
}
catch {
    "[{0}] ERROR: $($_.Exception.Message)" -f (Get-Date) | Tee-Object -FilePath $logFile -Append
    exit 1
}

# Cleanup old backups (keep last 7 days)
$cutoffDate = (Get-Date).AddDays(-7)
Get-ChildItem -Path "$backupDir\backup_*.sql" | Where-Object { $_.LastWriteTime -lt $cutoffDate } | Remove-Item -Force
```

#### 2. Schedule Task in Windows

```powershell
# Run as Administrator in PowerShell

$trigger = New-ScheduledTaskTrigger -Daily -At 2:00AM
$action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-File C:\laragon\www\kir-antrean\backup_database.ps1"
$principal = New-ScheduledTaskPrincipal -UserID "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

Register-ScheduledTask `
    -TaskName "KIR Antrean Database Backup" `
    -Description "Daily database backup for KIR Antrean" `
    -Trigger $trigger `
    -Action $action `
    -Principal $principal
```

#### 3. Verify Task

```powershell
# List the task
Get-ScheduledTask -TaskName "KIR Antrean Database Backup"

# Check task history
Get-ScheduledTaskInfo -TaskName "KIR Antrean Database Backup"
```

### Using cron (Linux/macOS)

```bash
# Edit crontab
crontab -e

# Add entry for daily 2 AM backup
0 2 * * * /usr/bin/mysqldump -u root -p'password' antrean_uji_kendaraan > /backup/kir_antrean_$(date +\%Y\%m\%d_\%H\%M\%S).sql
```

### Using Windows Batch File

**File**: `backup_database.bat`

```batch
@echo off
setlocal enabledelayedexpansion

set MYSQL_BIN=C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin
set BACKUP_DIR=c:\laragon\www\kir-antrean\database\backups
set DB_NAME=antrean_uji_kendaraan

REM Create backup directory if not exists
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
    echo Created backup directory
)

REM Create timestamp
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c%%a%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)
set TIMESTAMP=%mydate%_%mytime%

REM Create backup
echo Backing up database...
"%MYSQL_BIN%\mysqldump.exe" -u root %DB_NAME% > "%BACKUP_DIR%\backup_%TIMESTAMP%.sql"

if %ERRORLEVEL% EQU 0 (
    echo Backup completed successfully: backup_%TIMESTAMP%.sql
) else (
    echo Backup failed with error code %ERRORLEVEL%
)
```

---

## Backup Verification

### Verify Backup Integrity

```bash
# Check backup file size
ls -lh backup_20260505_031436.sql

# Verify SQL syntax
mysql -u root --no-data < backup_20260505_031436.sql

# Count records in backup
grep "INSERT INTO" backup_20260505_031436.sql | wc -l

# List tables in backup
grep "CREATE TABLE" backup_20260505_031436.sql
```

### Test Restore Process

```bash
# Restore to temporary database
mysql -u root -e "CREATE DATABASE test_restore"
mysql -u root test_restore < backup_20260505_031436.sql

# Verify data
mysql -u root test_restore -e "SELECT COUNT(*) FROM users"
mysql -u root test_restore -e "SELECT COUNT(*) FROM queues"
mysql -u root test_restore -e "SELECT COUNT(*) FROM vehicles"

# Drop test database
mysql -u root -e "DROP DATABASE test_restore"
```

### Create Backup Checksum

```bash
# Generate checksum
certutil -hashfile backup_20260505_031436.sql SHA256 > backup_20260505_031436.sql.sha256

# Verify checksum
certutil -hashfile backup_20260505_031436.sql SHA256

# Compare checksums
Get-Content backup_20260505_031436.sql.sha256
```

---

## Disaster Recovery

### Full Database Loss - Recovery Steps

#### 1. Create Empty Database
```bash
mysql -u root -e "CREATE DATABASE antrean_uji_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
```

#### 2. Restore from Backup
```bash
cd "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"
mysql -u root antrean_uji_kendaraan < C:\path\to\backup_20260505_031436.sql
```

#### 3. Verify Data Integrity
```sql
-- Check table counts
SELECT COUNT(*) as user_count FROM users;
SELECT COUNT(*) as queue_count FROM queues;
SELECT COUNT(*) as vehicle_count FROM vehicles;
SELECT COUNT(*) as test_result_count FROM test_results;

-- Check for orphaned records
SELECT * FROM queues WHERE vehicle_id NOT IN (SELECT id FROM vehicles);
SELECT * FROM test_results WHERE queue_id NOT IN (SELECT id FROM queues);
```

#### 4. Restart Application
```bash
# Clear application cache
php artisan cache:clear
php artisan config:clear

# Restart Laravel
php artisan serve
```

### Partial Data Loss - Recovery Steps

#### 1. Identify Lost Records
```sql
-- Find records with NULL created_at (likely corrupted)
SELECT * FROM queues WHERE created_at IS NULL;

-- Find records with future dates (data corruption sign)
SELECT * FROM test_results WHERE tested_at > NOW();
```

#### 2. Restore Specific Table
```bash
# Extract only users table from backup
cd "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"

# Get CREATE TABLE and INSERT statements for users
mysql -u root antrean_uji_kendaraan < structure_users_only.sql
```

#### 3. Migrate Lost Data
```sql
-- If backup is in separate database
INSERT INTO antrean_uji_kendaraan.users 
SELECT * FROM backup_restore.users 
WHERE id NOT IN (SELECT id FROM antrean_uji_kendaraan.users);
```

### Database Corruption - Recovery Steps

#### 1. Check Table Status
```bash
# Check tables for errors
cd "C:\Laragon\bin\mysql\mysql-8.0.30-winx64\bin"

mysqlcheck -u root antrean_uji_kendaraan

# Repair tables if needed
mysqlcheck -u root -r antrean_uji_kendaraan
```

#### 2. Dump and Restore
```bash
# Create backup of corrupted database
mysqldump -u root antrean_uji_kendaraan > corrupted_backup.sql

# Drop and recreate database
mysql -u root -e "DROP DATABASE antrean_uji_kendaraan"
mysql -u root -e "CREATE DATABASE antrean_uji_kendaraan"

# Restore from good backup
mysql -u root antrean_uji_kendaraan < backup_20260505_031436.sql
```

---

## Backup Retention Policy

### Recommended Schedule

```
Daily Backups:     Keep 7 days
Weekly Backups:    Keep 4 weeks
Monthly Backups:   Keep 12 months
```

### Automatic Cleanup Script

```powershell
function Clean-OldBackups {
    param(
        [string]$BackupDir = "c:\laragon\www\kir-antrean\database\backups",
        [int]$DaysToKeep = 7
    )
    
    $cutoffDate = (Get-Date).AddDays(-$DaysToKeep)
    $oldBackups = Get-ChildItem -Path "$BackupDir\backup_*.sql" | Where-Object { $_.LastWriteTime -lt $cutoffDate }
    
    foreach ($backup in $oldBackups) {
        Write-Host "Removing old backup: $($backup.Name)"
        Remove-Item -Path $backup.FullName -Force
    }
    
    Write-Host "Cleanup completed. Kept backups from last $DaysToKeep days."
}

# Run cleanup
Clean-OldBackups
```

---

## Backup Storage Recommendations

### Off-Site Backup

1. **Cloud Storage (AWS S3, Google Cloud)**
   ```bash
   # Upload backup to cloud
   aws s3 cp backup_20260505_031436.sql s3://kir-antrean-backups/
   ```

2. **External Hard Drive**
   ```bash
   # Copy backup to external drive
   Copy-Item -Path "C:\backups\backup_20260505_031436.sql" -Destination "D:\Backups\KIR-Antrean\"
   ```

3. **Network Attached Storage (NAS)**
   ```bash
   # Mount NAS and copy
   net use Z: \\nas-server\backups /user:admin password
   Copy-Item -Path "C:\backups\*.sql" -Destination "Z:\"
   ```

---

## Monitoring & Alerts

### Backup Monitoring Script

```powershell
function Monitor-BackupStatus {
    param(
        [string]$BackupDir = "c:\laragon\www\kir-antrean\database\backups",
        [int]$MaxAgeHours = 26
    )
    
    $latestBackup = Get-ChildItem -Path "$BackupDir\backup_*.sql" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
    
    if (-not $latestBackup) {
        Write-Host "✗ No backups found!" -ForegroundColor Red
        return
    }
    
    $age = (Get-Date) - $latestBackup.LastWriteTime
    $ageHours = $age.TotalHours
    
    if ($ageHours -gt $MaxAgeHours) {
        Write-Host "⚠ WARNING: Backup is $ageHours hours old!" -ForegroundColor Yellow
    } else {
        Write-Host "✓ Latest backup: $($latestBackup.Name)" -ForegroundColor Green
        Write-Host "  Age: $([math]::Round($ageHours, 1)) hours" -ForegroundColor Green
        Write-Host "  Size: $([math]::Round($latestBackup.Length / 1MB, 2)) MB" -ForegroundColor Green
    }
}

# Run monitoring
Monitor-BackupStatus
```

---

**Last Updated**: 2026-05-05  
**For Support**: Contact development team
