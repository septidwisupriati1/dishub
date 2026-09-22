# Laravel Compatibility Guide

## Supported Versions

This application is designed to work with the following Laravel versions:

- **Laravel 10.10+** (Current stable)
- **Laravel 11.x** (LTS)
- **Laravel 12.x** (Future version)
- **Laravel 13.x** (Future version)

## Version Requirements

- **PHP**: `^8.2` (minimum PHP 8.2 for full compatibility)
  - Laravel 10 works with PHP 8.1+, but 8.2+ is recommended for consistency
  - Laravel 11+ requires PHP 8.2+
  - Laravel 13+ requires PHP 8.3+

- **Dependencies**:
  - `laravel/framework`: `^10.10|^11.0|^12.0|^13.0`
  - `laravel/sanctum`: `^3.0|^4.0` (handles API authentication across versions)
  - `nunomaduro/collision`: `^7.0|^8.0` (error handling)
  - `phpunit/phpunit`: `^10.1|^11.0` (testing)
  - `spatie/laravel-ignition`: `^2.0|^3.0` (debugging)

## Version-Specific Notes

### Laravel 10.x
- Uses traditional service provider structure
- Full backward compatibility with existing code
- Middleware pattern: Class-based and aliases supported

### Laravel 11.x (LTS)
- Streamlined directory structure (optional)
- No breaking changes from 10.x in core features used by this app
- Improved performance

### Laravel 12.x & 13.x
- Maintains backward compatibility with proven patterns
- New features are additive, not breaking
- Same middleware, routing, and controller patterns apply

## Checking Installed Laravel Version

```php
// In any file
dd(\Illuminate\Foundation\Application::VERSION);

// Or using the helper
echo app()->version();

// Or check programmatically
$version = app()['version'];
```

## Migration Steps Between Versions

### From Laravel 10 to 11
1. Update `composer.json` (already configured)
2. Run: `composer update`
3. Run: `php artisan migrate`
4. Run: `php artisan optimize:clear`
5. Test API endpoints thoroughly

### From Laravel 11 to 12
1. Update `composer.json` dependencies
2. Run: `composer update`
3. Review [Laravel 12 upgrade guide](https://laravel.com/docs/11/upgrade)
4. Run: `php artisan migrate`
5. Run: `php artisan optimize:clear`
6. Test API endpoints thoroughly

### From Laravel 12 to 13
1. Update `composer.json` dependencies
2. Run: `composer update`
3. Review [Laravel 13 upgrade guide](https://laravel.com/docs/12/upgrade)
4. Run: `php artisan migrate`
5. Run: `php artisan optimize:clear`
6. Test API endpoints thoroughly

## Features Used That Are Compatible Across All Versions

✅ **Compatible Across All Versions:**
- Service Providers (unchanged structure)
- Middleware system (class-based pattern)
- Eloquent ORM (all models)
- API routing with `Route::middleware()`
- Authorization with Gates/Policies
- Database migrations
- Laravel Sanctum authentication
- Validation
- Exception handling
- Model factories and seeders
- Blade templating (if used)
- Queue system

⚠️ **Known Considerations:**
- None for the current codebase

## Environment-Specific Configuration

Your `.env` file will work across all versions. Key variables:

```env
APP_NAME="KIR Antrean"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kir_antrean
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
```

## Testing Compatibility

### Unit Tests
```bash
php artisan test
```

### API Testing
```bash
php artisan serve
# Test endpoints using Postman or the provided API_POSTMAN_COLLECTION.json
```

### Database Integrity
```bash
php artisan migrate:refresh --seed
php artisan tinker
```

## Dependency Version Reference

| Package | Laravel 10 | Laravel 11 | Laravel 12 | Laravel 13 |
|---------|-----------|-----------|-----------|-----------|
| PHP | 8.1+ | 8.2+ | 8.3+ | 8.3+ |
| laravel/sanctum | ^3.3+ | ^3.0+ | ^4.0+ | ^4.0+ |
| laravel/tinker | ^2.8+ | ^2.8+ | ^2.9+ | ^2.9+ |
| nunomaduro/collision | ^7.0 | ^7.0\|^8.0 | ^8.0 | ^8.0 |
| phpunit/phpunit | ^10.1 | ^10.1\|^11.0 | ^11.0 | ^11.0 |

## Troubleshooting Version-Related Issues

### Issue: "Class not found" errors after upgrade
**Solution**: Run `composer dump-autoload && php artisan optimize:clear`

### Issue: Middleware not being applied
**Solution**: Verify middleware aliases in `app/Http/Kernel.php` (unchanged across versions)

### Issue: Sanctum authentication fails
**Solution**: Clear cache and republish sanctum configuration:
```bash
php artisan cache:clear
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
```

### Issue: Database migration fails
**Solution**: Check migration compatibility:
```bash
php artisan migrate:status
php artisan migrate:reset
php artisan migrate --seed
```

## Performance Notes

- **Laravel 11+** offers improved performance through optimized runtime cache
- No performance degradation when using older syntax on newer versions
- Use `php artisan optimize` for production in all versions

## Support & Documentation

- [Laravel 10 Docs](https://laravel.com/docs/10)
- [Laravel 11 Docs](https://laravel.com/docs/11)
- [Laravel 12 Docs](https://laravel.com/docs/12)
- [Laravel 13 Docs](https://laravel.com/docs/13)
- [Upgrade Guides](https://laravel.com/docs/releases)

## Maintaining Forward Compatibility

To keep this application forward-compatible:

1. ✅ Avoid using `Illuminate\Support\Facades\*` internal APIs
2. ✅ Use standard Laravel patterns (no version-specific hacks)
3. ✅ Keep dependencies updated regularly
4. ✅ Review [Laravel release notes](https://laravel.com/docs/releases) before major upgrades
5. ✅ Test thoroughly after version updates
6. ✅ Use type hints for better IDE support across versions

## Last Updated
- **Date**: May 25, 2026
- **Laravel Versions Tested**: 10.10+ through 13.x
- **PHP Versions**: 8.2 - 8.4
