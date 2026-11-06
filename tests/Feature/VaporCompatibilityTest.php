<?php

namespace Tests\Feature;

use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class VaporCompatibilityTest extends TestCase
{
    public function test_the_welcome_page_renders_when_built_assets_are_only_on_the_cdn(): void
    {
        // Vapor ships the Vite manifests in the Lambda bundle but moves the
        // asset files themselves to the CDN, so nothing may read them from disk.
        $public = sys_get_temp_dir().'/dvc-public-'.uniqid();
        File::ensureDirectoryExists($public.'/build');
        File::put($public.'/build/manifest.json', json_encode([
            'resources/css/app.css' => ['file' => 'assets/app-missing.css', 'src' => 'resources/css/app.css', 'isEntry' => true],
            'resources/js/app.js' => ['file' => 'assets/app-missing.js', 'src' => 'resources/js/app.js', 'isEntry' => true],
        ]));
        File::put($public.'/build/fonts-manifest.json', json_encode([
            'version' => 1,
            'style' => ['file' => 'assets/fonts-missing.css', 'familyStyles' => []],
        ]));

        try {
            $this->app->usePublicPath($public);

            $this->get('/')->assertOk()->assertSee('assets/app-missing.css');
        } finally {
            File::deleteDirectory($public);
        }
    }

    public function test_trusted_hosts_cover_the_custom_domain_and_vanity_url_only(): void
    {
        config(['app.url' => 'https://api.deltavcreative.com']);
        $patterns = $this->app->make(TrustHosts::class)->hosts();

        $trusted = fn (string $host): bool => collect($patterns)
            ->contains(fn (string $pattern) => preg_match('{'.$pattern.'}i', $host) === 1);

        $this->assertTrue($trusted('api.deltavcreative.com'));
        $this->assertTrue($trusted('misty-garden-8w5lxmxdhmao.vapor-farm-c1.com'));
        $this->assertFalse($trusted('someone-else.vapor-farm-c1.com'));
        $this->assertFalse($trusted('evil.example'));
    }
}
