<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\LaravelVersionHelper;

class CheckVersionCompatibility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:check {--verbose : Show detailed information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Laravel version compatibility and display version information';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Laravel Version Compatibility Checker ===');
        $this->newLine();

        // Get version info
        $versionInfo = LaravelVersionHelper::getVersionInfo();
        $majorVersion = LaravelVersionHelper::getMajorVersion();

        // Display version
        $this->info('Current Version Information:');
        $this->line('  Laravel Version: ' . $this->formatVersion($versionInfo['version']));
        $this->line('  Major Version: ' . $versionInfo['major']);
        $this->line('  PHP Version: ' . $versionInfo['php_version']);
        $this->line('  Is LTS: ' . ($versionInfo['is_lts'] ? 'Yes ✓' : 'No'));
        if ($versionInfo['release_date']) {
            $this->line('  Release Date: ' . $versionInfo['release_date']);
        }
        $this->newLine();

        // Display compatibility status
        $this->info('Compatibility Status:');
        $this->checkVersionCompatibility($majorVersion);
        $this->newLine();

        // Display supported features
        $this->info('Supported Features:');
        $this->displaySupportedFeatures($majorVersion);
        $this->newLine();

        // Display deprecated features (if any)
        $deprecatedFeatures = config('version.deprecated_features', []);
        if (!empty($deprecatedFeatures)) {
            $this->warn('Deprecated Features:');
            foreach ($deprecatedFeatures as $feature) {
                $this->line('  ⚠ ' . $feature);
            }
            $this->newLine();
        }

        // Display new features (if applicable)
        $newFeatures = config("version.new_features.v{$majorVersion}", []);
        if (!empty($newFeatures)) {
            $this->info('New Features in this Version:');
            foreach ($newFeatures as $feature) {
                $this->line('  ✨ ' . $feature);
            }
            $this->newLine();
        }

        // Display next steps
        $this->info('Recommendations:');
        $this->displayRecommendations($majorVersion);
        $this->newLine();

        // Verbose output
        if ($this->option('verbose')) {
            $this->displayVerboseInfo($versionInfo);
        }

        $this->info('Check complete! Application is running on Laravel ' . $majorVersion . '.x');

        return self::SUCCESS;
    }

    /**
     * Check and display version compatibility
     */
    private function checkVersionCompatibility(int $majorVersion): void
    {
        $compatibilityStatuses = [
            10 => ['status' => 'Supported', 'until' => 'January 2025'],
            11 => ['status' => 'Supported (LTS)', 'until' => 'January 2027'],
            12 => ['status' => 'Supported', 'until' => 'Q3 2025'],
            13 => ['status' => 'Supported', 'until' => 'Q3 2026'],
        ];

        foreach ($compatibilityStatuses as $version => $info) {
            $marker = $version === $majorVersion ? '✓' : '•';
            $status = $info['status'];
            $until = $info['until'];

            if ($version === $majorVersion) {
                $this->line("  $marker <fg=green>Laravel {$version}.x: {$status}</> (Until {$until})");
            } else {
                $this->line("  $marker Laravel {$version}.x: {$status} (Until {$until})");
            }
        }
    }

    /**
     * Display supported features
     */
    private function displaySupportedFeatures(int $majorVersion): void
    {
        $features = [
            'Service Providers' => true,
            'Middleware System' => true,
            'Eloquent ORM' => true,
            'API Routing' => true,
            'Authorization & Policies' => true,
            'Database Migrations' => true,
            'Sanctum Authentication' => true,
            'Validation' => true,
            'Exception Handling' => true,
            'Model Factories & Seeders' => true,
        ];

        foreach ($features as $feature => $supported) {
            $marker = $supported ? '✓' : '✗';
            $this->line("  $marker {$feature}");
        }
    }

    /**
     * Display recommendations
     */
    private function displayRecommendations(int $majorVersion): void
    {
        if ($majorVersion < 11) {
            $this->line('  → Consider upgrading to Laravel 11 (LTS) for long-term support');
        }

        if ($majorVersion === 10) {
            $this->line('  → Security support until January 2025');
        }

        $this->line('  → Run `php artisan test` to verify compatibility');
        $this->line('  → Check LARAVEL_COMPATIBILITY.md for detailed information');
        $this->line('  → Review VERSION_MIGRATION_GUIDE.md for upgrade procedures');
    }

    /**
     * Display verbose information
     */
    private function displayVerboseInfo(array $versionInfo): void
    {
        $this->newLine();
        $this->info('Verbose Information:');

        // Version string details
        $version = $versionInfo['version'];
        $parts = explode('.', $version);
        $this->line('  Version Details:');
        $this->line('    Major: ' . ($parts[0] ?? 'unknown'));
        $this->line('    Minor: ' . ($parts[1] ?? 'unknown'));
        $this->line('    Patch: ' . ($parts[2] ?? 'unknown'));

        // Application info
        $this->line('  Application:');
        $this->line('    Name: ' . config('app.name'));
        $this->line('    Environment: ' . config('app.env'));
        $this->line('    Debug: ' . (config('app.debug') ? 'Enabled' : 'Disabled'));

        // Database info
        $this->line('  Database:');
        $this->line('    Connection: ' . config('database.default'));
        $this->line('    Database: ' . config('database.connections.' . config('database.default') . '.database'));

        // Cache info
        $this->line('  Cache:');
        $this->line('    Driver: ' . config('cache.default'));

        // Queue info
        $this->line('  Queue:');
        $this->line('    Driver: ' . config('queue.default'));
    }

    /**
     * Format version string for display
     */
    private function formatVersion(string $version): string
    {
        return "<fg=cyan>{$version}</>";
    }
}
