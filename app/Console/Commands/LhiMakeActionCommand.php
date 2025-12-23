<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LhiMakeActionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lhi:make-action {module : The module name} {name : The action name (without Action suffix)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Action class in the specified module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $name = $this->argument('name');

        // Add Action suffix if not present
        $actionName = Str::endsWith($name, 'Action') ? $name : $name . 'Action';

        $modulePath = base_path("modules/{$module}");

        if (!is_dir($modulePath)) {
            $this->error("Module [{$module}] does not exist!");
            $this->line("Run: php artisan lhi:make-module {$module}");
            return self::FAILURE;
        }

        $actionsPath = "{$modulePath}/Actions";

        if (!is_dir($actionsPath)) {
            mkdir($actionsPath, 0755, true);
        }

        $filePath = "{$actionsPath}/{$actionName}.php";

        if (file_exists($filePath)) {
            $this->error("Action [{$actionName}] already exists in module [{$module}]!");
            return self::FAILURE;
        }

        $stub = $this->getStub();
        $content = str_replace(
            ['{{MODULE}}', '{{ACTION_NAME}}'],
            [$module, $actionName],
            $stub
        );

        file_put_contents($filePath, $content);

        $this->components->info("Action [{$actionName}] created successfully in module [{$module}]!");
        $this->line("📄 File: modules/{$module}/Actions/{$actionName}.php");

        return self::SUCCESS;
    }

    /**
     * Get the stub file for the generator.
     */
    private function getStub(): string
    {
        $stubPath = base_path('stubs/lhi/action.stub');

        if (file_exists($stubPath)) {
            return file_get_contents($stubPath);
        }

        // Default stub
        return <<<'STUB'
<?php

namespace Modules\{{MODULE}}\Actions;

/**
 * {{ACTION_NAME}}
 * 
 * Single-purpose action for {{MODULE}} module.
 */
class {{ACTION_NAME}}
{
    /**
     * Execute the action.
     */
    public function execute(): void
    {
        // TODO: Implement action logic
    }

    /**
     * Handle the action (alias for execute).
     */
    public function handle(): void
    {
        $this->execute();
    }
}

STUB;
    }
}
