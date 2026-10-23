<?php

namespace Mehuli\FilamentShadcn\Tests\Feature;

use Mehuli\FilamentShadcn\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class InstallCommandTest extends TestCase
{
    public static function invalidPanels(): array
    {
        return [["x');system('id');//"], ['../../etc'], ['admin/evil'], ['admin"']];
    }

    #[DataProvider('invalidPanels')]
    public function test_id_panel_tidak_valid_ditolak(string $panel): void
    {
        $this->artisan('shadcn:install', ['--panel' => $panel, '--no-interaction' => true])
            ->expectsOutputToContain('ID panel tidak valid')
            ->assertFailed();
    }

    public function test_provider_harus_file_php(): void
    {
        $this->artisan('shadcn:install', ['--panel' => 'admin', '--provider' => '/etc/passwd', '--no-interaction' => true])
            ->assertFailed();
    }

    public function test_memasang_ke_panel_provider(): void
    {
        $dir = app_path('Providers/Filament');
        @mkdir($dir, 0777, true);
        $provider = $dir . '/AdminPanelProvider.php';
        file_put_contents($provider, <<<'PHP'
            <?php

            namespace App\Providers\Filament;

            use Filament\Panel;
            use Filament\Support\Colors\Color;

            class AdminPanelProvider
            {
                public function panel(Panel $panel): Panel
                {
                    return $panel
                        ->id('admin')
                        ->path('admin')
                        ->login()
                        ->profile()
                        ->colors([
                            'primary' => Color::Amber,
                        ]);
                }
            }
            PHP);

        try {
            $this->artisan('shadcn:install', ['--panel' => 'admin', '--no-interaction' => true])->assertSuccessful();

            $contents = file_get_contents($provider);
            $this->assertStringContainsString('use Mehuli\FilamentShadcn\ShadcnPlugin;', $contents);
            $this->assertStringContainsString("->viteTheme('resources/css/filament/admin/theme.css')", $contents);
            $this->assertStringContainsString('->plugin(ShadcnPlugin::make())', $contents);
            $this->assertStringContainsString('->login(\Mehuli\FilamentShadcn\Pages\Auth\Login::class)', $contents);
            $this->assertStringContainsString('->profile(\Mehuli\FilamentShadcn\Pages\Auth\EditProfile::class, isSimple: false)', $contents);
            $this->assertStringNotContainsString('Color::Amber', $contents);

            // Aman dijalankan ulang.
            $this->artisan('shadcn:install', ['--panel' => 'admin', '--no-interaction' => true])->assertSuccessful();
            $this->assertSame($contents, file_get_contents($provider));
        } finally {
            @unlink($provider);
            @unlink(resource_path('css/filament/admin/theme.css'));
        }
    }
}
