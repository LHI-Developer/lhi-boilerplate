<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LhiMakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lhi:make-module {name : The name of the module (PascalCase)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new SIT LHI module with standard folder structure';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');

        // Validate PascalCase
        if (!$this->isPascalCase($name)) {
            $this->error('Module name must be in PascalCase format (e.g., SIAKAD, Rapor, Keuangan)');
            return self::FAILURE;
        }

        $this->info("Creating module: {$name}");

        // Use nwidart's module:make command
        $exitCode = $this->call('module:make', [
            'name' => [$name],
        ]);

        if ($exitCode !== 0) {
            $this->error("Failed to create module {$name}");
            return self::FAILURE;
        }

        // Create additional folders if they don't exist
        $modulePath = base_path("modules/{$name}");

        $additionalFolders = [
            'Infrastructure',
            'Infrastructure/Traits',
        ];

        foreach ($additionalFolders as $folder) {
            $folderPath = "{$modulePath}/{$folder}";
            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0755, true);
                file_put_contents("{$folderPath}/.gitkeep", '');
                $this->info("Created: {$folder}/");
            }
        }

        $this->newLine();
        $this->components->info("Module [{$name}] created successfully!");
        $this->newLine();
        $this->line("📁 Module structure:");
        $this->line("  ✓ Actions/");
        $this->line("  ✓ Services/");
        $this->line("  ✓ Models/");
        $this->line("  ✓ Filament/");
        $this->line("  ✓ Livewire/");
        $this->line("  ✓ Database/");
        $this->line("  ✓ Routes/");
        $this->line("  ✓ Infrastructure/");
        $this->newLine();
        $this->components->info("Next steps:");
        $this->line("  • php artisan lhi:make-service {$name} {ServiceName}");
        $this->line("  • php artisan lhi:make-action {$name} {ActionName}");

        return self::SUCCESS;
    }

    /**
     * Check if string is in PascalCase format.
     */
    private function isPascalCase(string $value): bool
    {
        return preg_match('/^[A-Z][a-zA-Z0-9]*$/', $value) === 1;
    }
}
