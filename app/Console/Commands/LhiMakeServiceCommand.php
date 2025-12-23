<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LhiMakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lhi:make-service {module : The module name} {name : The service name (without Service suffix)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Service class in the specified module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $name = $this->argument('name');

        // Add Service suffix if not present
        $serviceName = Str::endsWith($name, 'Service') ? $name : $name . 'Service';

        $modulePath = base_path("modules/{$module}");

        if (!is_dir($modulePath)) {
            $this->error("Module [{$module}] does not exist!");
            $this->line("Run: php artisan lhi:make-module {$module}");
            return self::FAILURE;
        }

        $servicesPath = "{$modulePath}/Services";

        if (!is_dir($servicesPath)) {
            mkdir($servicesPath, 0755, true);
        }

        $filePath = "{$servicesPath}/{$serviceName}.php";

        if (file_exists($filePath)) {
            $this->error("Service [{$serviceName}] already exists in module [{$module}]!");
            return self::FAILURE;
        }

        $stub = $this->getStub();
        $content = str_replace(
            ['{{MODULE}}', '{{SERVICE_NAME}}'],
            [$module, $serviceName],
            $stub
        );

        file_put_contents($filePath, $content);

        $this->components->info("Service [{$serviceName}] created successfully in module [{$module}]!");
        $this->line("📄 File: modules/{$module}/Services/{$serviceName}.php");

        return self::SUCCESS;
    }

    /**
     * Get the stub file for the generator.
     */
    private function getStub(): string
    {
        $stubPath = base_path('stubs/lhi/service.stub');

        if (file_exists($stubPath)) {
            return file_get_contents($stubPath);
        }

        // Default stub
        return <<<'STUB'
<?php

namespace Modules\{{MODULE}}\Services;

/**
 * {{SERVICE_NAME}}
 * 
 * Service class for business logic in {{MODULE}} module.
 */
class {{SERVICE_NAME}}
{
    /**
     * Example method.
     */
    public function exampleMethod(): void
    {
        // TODO: Implement business logic
    }
}

STUB;
    }
}
