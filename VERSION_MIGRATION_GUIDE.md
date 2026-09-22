# Version Migration Guide

Complete step-by-step guide for upgrading between Laravel versions.

## Quick Start

```bash
# Check current version
php artisan tinker
App\Helpers\LaravelVersionHelper::getVersion()

# Check version info
App\Helpers\LaravelVersionHelper::getVersionInfo()
```

## Upgrading Laravel

### Prerequisites
- Backup your database first
- Review the target Laravel version's upgrade guide
- Test in a development environment first

### Step-by-Step Upgrade Process

#### Step 1: Backup Everything
```bash
# Backup database
php artisan backup:run --only-db

# Or manual backup
mysqldump -u root -p kir_antrean > backup_$(date +%s).sql

# Backup files
git commit -am "Pre-upgrade backup"
```

#### Step 2: Update Composer Dependencies
```bash
# Update composer.json to target version
# Edit composer.json and update laravel/framework constraint
# Example: "laravel/framework": "^11.0" for Laravel 11

# Clear composer cache
composer clear-cache

# Update dependencies
composer update --no-dev

# Or with dev dependencies for development
composer update
```

#### Step 3: Run Migrations
```bash
# Check migration status
php artisan migrate:status

# Run pending migrations
php artisan migrate

# If issues, refresh (WARNING: This resets database)
php artisan migrate:refresh --seed
```

#### Step 4: Clear Caches and Optimize
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate optimized class loader
php artisan optimize
composer dump-autoload
```

#### Step 5: Republish Vendor Assets (if needed)
```bash
# Sanctum configuration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force

# Other packages (optional)
php artisan vendor:publish
```

#### Step 6: Test Application
```bash
# Start development server
php artisan serve

# Run tests
php artisan test

# Test API endpoints
# Use Postman with API_POSTMAN_COLLECTION.json
```

#### Step 7: Verify Functionality
- [ ] Login/Register endpoint working
- [ ] Vehicle CRUD operations functional
- [ ] Queue system operational
- [ ] Test results displaying correctly
- [ ] WhatsApp notifications sending (if configured)
- [ ] API responses correct format
- [ ] Database integrity maintained

## Version-Specific Upgrade Notes

### Upgrading from Laravel 10 to 11

**Breaking Changes to Watch For:**
- None affecting core functionality of this app
- All middleware, routing, and models remain compatible

**New Features to Consider:**
- Improved error reporting
- Better performance
- New collection helpers

**Migration Commands:**
```bash
# Update composer.json
# "laravel/framework": "^11.0"
# "laravel/sanctum": "^3.0|^4.0"

composer update
php artisan migrate
php artisan optimize:clear
php artisan test
```

### Upgrading from Laravel 11 to 12

**Breaking Changes to Watch For:**
- Review Laravel 12 release notes
- Check dependency compatibility

**New Features:**
- To be documented when Laravel 12 is released

**Migration Commands:**
```bash
# Update composer.json to Laravel 12 constraints
composer update
php artisan migrate
php artisan optimize:clear
php artisan test
```

### Upgrading from Laravel 12 to 13

**Breaking Changes to Watch For:**
- Review Laravel 13 release notes
- Monitor deprecation warnings in Laravel 12

**New Features:**
- To be documented when Laravel 13 is released

**Migration Commands:**
```bash
# Update composer.json to Laravel 13 constraints
composer update
php artisan migrate
php artisan optimize:clear
php artisan test
```

## Using Version-Specific Code

### Check Version in Code

```php
use App\Helpers\LaravelVersionHelper;

// Get current version
$version = LaravelVersionHelper::getVersion(); // "13.1.0"

// Get major version
$major = LaravelVersionHelper::getMajorVersion(); // 13

// Check version
if (LaravelVersionHelper::isLaravel13Plus()) {
    // Do something for Laravel 13+
}

// Check specific version
if (LaravelVersionHelper::is(13)) {
    // Do something only for Laravel 13
}

// Get version info
$info = LaravelVersionHelper::getVersionInfo();
```

### Use in Models

```php
use App\Traits\VersionCompatible;

class Vehicle extends Model
{
    use VersionCompatible;

    public function getData()
    {
        return $this->ifLaravel13Plus(function () {
            // Laravel 13+ specific logic
            return $this->getOptimizedData();
        }) ?? $this->getStandardData();
    }
}
```

### Use in Controllers

```php
use App\Helpers\LaravelVersionHelper;

class TestResultController extends Controller
{
    public function index()
    {
        $laravelVersion = LaravelVersionHelper::getMajorVersion();

        if ($laravelVersion >= 12) {
            // Use newer features
        } else {
            // Use compatible approach
        }

        return response()->json([
            'data' => $results,
            'version' => LaravelVersionHelper::getVersion(),
        ]);
    }
}
```

### Get Version-Specific Config

```php
use App\Helpers\LaravelVersionHelper;

$timeout = LaravelVersionHelper::config('queue_timeout', 60);
$cacheTtl = LaravelVersionHelper::config('cache_ttl', 3600);
```

## Troubleshooting

### Issue: "Class not found" after upgrade
**Solution:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Issue: Middleware not working
**Solution:**
```bash
# Check Kernel.php - should be unchanged
php artisan route:list
php artisan route:clear
php artisan route:cache
```

### Issue: Database migration fails
**Solution:**
```bash
php artisan migrate:status
php artisan migrate:reset
php artisan migrate
php artisan seed:db --class=DatabaseSeeder
```

### Issue: Sanctum authentication not working
**Solution:**
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
php artisan migrate
php artisan cache:clear
```

### Issue: Tests failing after upgrade
**Solution:**
```bash
# Refresh database for tests
php artisan migrate:refresh --env=testing

# Run tests with verbose output
php artisan test --verbose

# Run specific test
php artisan test tests/Feature/AuthControllerTest.php
```

### Issue: API returns wrong Laravel version in header
**Solution:**
Check middleware configuration - headers should be standard Laravel headers, not version-specific.

## Rollback Procedure

If upgrade fails and you need to rollback:

```bash
# 1. Restore database from backup
mysql -u root -p kir_antrean < backup_TIMESTAMP.sql

# 2. Checkout previous version from git
git checkout previous-version-branch
# Or manually revert composer.json

# 3. Install previous dependencies
composer install

# 4. Clear caches
php artisan optimize:clear

# 5. Test
php artisan serve
```

## Performance Comparison

Each Laravel version typically brings performance improvements:

- **Laravel 10**: Baseline
- **Laravel 11**: ~5-10% improvement due to optimizations
- **Laravel 12**: Additional improvements (TBD)
- **Laravel 13**: Additional improvements (TBD)

To measure:
```bash
# Use Laravel's built-in profiling
php artisan tinker
dd(\Illuminate\Support\Facades\Log::getLogger()->getHandlers());

# Or create a simple performance test
php artisan tinker
$start = microtime(true);
App\Models\Vehicle::all();
echo microtime(true) - $start;
```

## Dependencies Update Schedule

Keep dependencies updated with framework updates:

| Dependency | Update Pattern |
|-----------|---|
| laravel/framework | With major version |
| laravel/sanctum | As needed for feature support |
| laravel/tinker | Minor version updates |
| nunomaduro/collision | Track with framework |
| phpunit/phpunit | When Laravel version updates |
| spatie/laravel-ignition | As released |

## Documentation References

- [Laravel 10 Upgrade Guide](https://laravel.com/docs/10/upgrade)
- [Laravel 11 Upgrade Guide](https://laravel.com/docs/11/upgrade)
- [Laravel 12 Upgrade Guide](https://laravel.com/docs/12/upgrade)
- [Laravel 13 Upgrade Guide](https://laravel.com/docs/13/upgrade)
- [Release Notes](https://laravel.com/docs/releases)

## Final Checklist

Before considering upgrade complete:

- [ ] Application starts without errors
- [ ] All migrations run successfully
- [ ] Tests pass (`php artisan test`)
- [ ] API endpoints respond correctly
- [ ] Authentication working (login/register)
- [ ] Database queries optimized
- [ ] Cache working properly
- [ ] Logs showing normal operation
- [ ] Version helper showing correct version
- [ ] Code deployment ready
