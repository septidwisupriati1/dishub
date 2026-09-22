<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 🚀 KIR Antrean - Multi-Version Laravel Application

This application is designed to work seamlessly with **Laravel 10, 11, 12, and 13**.

### Supported Laravel Versions

| Version | PHP | Status | Support Until |
|---------|-----|--------|---|
| **Laravel 10** | 8.1+ | ✅ Supported | January 2025 |
| **Laravel 11** | 8.2+ | ✅ Supported (LTS) | January 2027 |
| **Laravel 12** | 8.3+ | ✅ Supported | Q3 2025 |
| **Laravel 13** | 8.3+ | ✅ Supported | Q3 2026 |

### Quick Start

```bash
# Check your Laravel version
php artisan version:check

# Get detailed version information
php artisan version:check --verbose

# View version info in code
php artisan tinker
App\Helpers\LaravelVersionHelper::getVersion()
```

### Key Features

- ✅ **Single codebase** works with Laravel 10, 11, 12, and 13
- ✅ **Version detection** with `LaravelVersionHelper` utility
- ✅ **Version-specific traits** for models (`VersionCompatible`)
- ✅ **Configuration system** for version-specific settings
- ✅ **Comprehensive testing** for version compatibility
- ✅ **Easy upgrades** with clear migration guides

### Documentation

- **[MULTI_VERSION_SETUP_GUIDE.md](MULTI_VERSION_SETUP_GUIDE.md)** - Setup instructions for multi-version support
- **[LARAVEL_COMPATIBILITY.md](LARAVEL_COMPATIBILITY.md)** - Detailed compatibility information
- **[VERSION_MIGRATION_GUIDE.md](VERSION_MIGRATION_GUIDE.md)** - Step-by-step upgrade guide
- **[DEVELOPER_QUICK_REFERENCE.md](DEVELOPER_QUICK_REFERENCE.md)** - Quick reference for developers

### Using Version-Specific Features

```php
use App\Helpers\LaravelVersionHelper;

// Get version
$version = LaravelVersionHelper::getVersion();

// Check version
if (LaravelVersionHelper::isLaravel13Plus()) {
    // Use Laravel 13+ features
}

// In models with VersionCompatible trait
$result = $model->ifLaravel12Plus(function() {
    return $this->useNewFeature();
});
```

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
