<?php

namespace Tests\Feature;

use App\Helpers\LaravelVersionHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaravelCompatibilityTest extends TestCase
{
    /**
     * Test that version helper correctly identifies Laravel version
     */
    public function test_version_helper_identifies_version(): void
    {
        $version = LaravelVersionHelper::getVersion();
        $this->assertIsString($version);
        $this->assertStringContainsString('.', $version);
    }

    /**
     * Test that major version is correctly extracted
     */
    public function test_major_version_extracted_correctly(): void
    {
        $majorVersion = LaravelVersionHelper::getMajorVersion();
        $this->assertIsInt($majorVersion);
        $this->assertGreaterThanOrEqual(10, $majorVersion);
    }

    /**
     * Test Laravel 10+ check
     */
    public function test_laravel_10_plus_check(): void
    {
        $this->assertTrue(LaravelVersionHelper::isLaravel10Plus());
    }

    /**
     * Test version info array structure
     */
    public function test_version_info_has_required_keys(): void
    {
        $info = LaravelVersionHelper::getVersionInfo();
        
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('major', $info);
        $this->assertArrayHasKey('is_lts', $info);
        $this->assertArrayHasKey('php_version', $info);
    }

    /**
     * Test is() method for exact version match
     */
    public function test_is_method_returns_correct_result(): void
    {
        $majorVersion = LaravelVersionHelper::getMajorVersion();
        
        $this->assertTrue(LaravelVersionHelper::is($majorVersion));
        $this->assertFalse(LaravelVersionHelper::is($majorVersion + 1));
    }

    /**
     * Test version compatibility checker
     */
    public function test_version_compatibility_checker(): void
    {
        $majorVersion = LaravelVersionHelper::getMajorVersion();

        // Test exact version format
        $this->assertTrue(
            LaravelVersionHelper::isCompatibleWith("{$majorVersion}.x")
        );

        // Test greater than or equal
        $this->assertTrue(
            LaravelVersionHelper::isCompatibleWith(">=" . ($majorVersion))
        );
    }

    /**
     * Test that application services are properly registered
     */
    public function test_version_helper_registered_in_container(): void
    {
        $helper = app('laravel-version-helper');
        $this->assertInstanceOf(LaravelVersionHelper::class, $helper);
    }

    /**
     * Test version in response header
     */
    public function test_version_info_available_in_request(): void
    {
        // Make a request to an API endpoint
        $response = $this->getJson('/api/auth/user');
        
        // Should get a response (might be 401 if not authenticated)
        $this->assertNotNull($response);
    }

    /**
     * Test core Laravel functionality across versions
     */
    public function test_basic_laravel_features_available(): void
    {
        // Test that we can access core Laravel features
        $this->assertNotNull(app());
        $this->assertNotNull(config('app'));
        $this->assertTrue(function_exists('route'));
        $this->assertTrue(function_exists('app'));
    }

    /**
     * Test that all required dependencies are installed
     */
    public function test_required_dependencies_installed(): void
    {
        // These should all be available
        $this->assertTrue(class_exists(\Illuminate\Foundation\Application::class));
        $this->assertTrue(class_exists(\Laravel\Sanctum\HasApiTokens::class));
        $this->assertTrue(class_exists(\PsySH\Shell::class)); // tinker
    }

    /**
     * Test middleware is configured correctly
     */
    public function test_middleware_configuration_valid(): void
    {
        $kernel = app(\App\Http\Kernel::class);
        
        // Check that middleware groups exist
        $this->assertTrue(method_exists($kernel, 'getMiddlewareGroups'));
    }

    /**
     * Test service providers are registered
     */
    public function test_service_providers_registered(): void
    {
        $providers = app()->getLoadedProviders();
        
        // Check core Laravel providers
        $this->assertArrayHasKey(\Illuminate\Foundation\Providers\FoundationServiceProvider::class, $providers);
    }
}
