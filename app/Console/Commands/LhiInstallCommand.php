<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\School;

class LhiInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lhi:install 
                            {--fresh : Run fresh migrations (WARNING: drops all tables)}
                            {--seed : Run seeders after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and setup SIT LHI Boilerplate with Filament, Shield, and Multi-tenancy';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('🚀 SIT LHI Boilerplate Installation');
        $this->newLine();

        // Step 1: Publish Configs
        $this->step1PublishConfigs();

        // Step 2: Run Migrations
        $this->step2RunMigrations();

        // Step 3: Setup Shield
        $this->step3SetupShield();

        // Step 4: Create Default School
        $school = $this->step4CreateDefaultSchool();

        // Step 5: Create Super Admin
        $this->step5CreateSuperAdmin($school);

        // Step 6: Clear Cache
        $this->step6ClearCache();

        $this->newLine();
        $this->components->info('✅ Installation completed successfully!');
        $this->newLine();
        $this->displayNextSteps();

        return self::SUCCESS;
    }

    /**
     * Step 1: Publish configuration files.
     */
    private function step1PublishConfigs(): void
    {
        $this->components->task('Publishing Filament configs', function () {
            $this->callSilently('vendor:publish', [
                '--tag' => 'filament-config',
                '--force' => true,
            ]);
        });

        $this->components->task('Publishing Shield configs', function () {
            $this->callSilently('vendor:publish', [
                '--tag' => 'filament-shield-config',
                '--force' => true,
            ]);
        });
    }

    /**
     * Step 2: Run database migrations.
     */
    private function step2RunMigrations(): void
    {
        if ($this->option('fresh')) {
            $this->components->warn('Running fresh migrations (this will drop all tables)...');

            if (!$this->confirm('Are you sure you want to drop all tables?', false)) {
                $this->components->error('Installation cancelled.');
                exit(1);
            }

            $this->components->task('Running fresh migrations', function () {
                $this->call('migrate:fresh', ['--force' => true]);
            });
        } else {
            $this->components->task('Running migrations', function () {
                $this->call('migrate', ['--force' => true]);
            });
        }

        if ($this->option('seed')) {
            $this->components->task('Running seeders', function () {
                $this->call('db:seed', ['--force' => true]);
            });
        }
    }

    /**
     * Step 3: Setup Shield permissions and roles.
     */
    private function step3SetupShield(): void
    {
        $this->components->task('Setting up Shield permissions', function () {
            // Shield setup will create basic roles and permissions
            $this->callSilently('shield:generate', [
                '--all' => true,
            ]);
        });
    }

    /**
     * Step 4: Create default school.
     */
    private function step4CreateDefaultSchool(): School
    {
        $this->newLine();
        $this->components->info('📚 Creating Default School');

        $schoolName = $this->ask('School name', 'SIT LHI Demo');
        $schoolCode = $this->ask('School code', 'DEMO001');
        $npsn = $this->ask('NPSN (optional)', '');

        $school = null;
        $this->components->task("Creating school: {$schoolName}", function () use (&$school, $schoolName, $schoolCode, $npsn) {
            $school = School::create([
                'name' => $schoolName,
                'code' => $schoolCode,
                'npsn' => $npsn ?: null,
                'address' => 'Default Address',
                'phone' => '021-12345678',
                'email' => 'contact@' . strtolower($schoolCode) . '.sch.id',
                'is_active' => true,
            ]);
        });

        return $school;
    }

    /**
     * Step 5: Create Super Admin user.
     */
    private function step5CreateSuperAdmin(School $school): void
    {
        $this->newLine();
        $this->components->info('👤 Creating Super Admin User');

        $name = $this->ask('Admin name', 'Super Admin');
        $email = $this->ask('Admin email', 'admin@lhi.sch.id');
        $password = $this->secret('Admin password (min 8 characters)');

        if (strlen($password) < 8) {
            $this->components->error('Password must be at least 8 characters!');
            exit(1);
        }

        $user = null;
        $this->components->task("Creating user: {$email}", function () use (&$user, $name, $email, $password, $school) {
            // Check if user already exists
            $existing = User::where('email', $email)->first();

            if ($existing) {
                $this->components->warn("User {$email} already exists, updating...");
                $user = $existing;
                $user->update([
                    'name' => $name,
                    'password' => Hash::make($password),
                    'school_id' => $school->id,
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'school_id' => $school->id,
                ]);
            }
        });

        $this->components->task('Assigning super_admin role', function () use ($user) {
            // Assign super_admin role from Shield
            $user->assignRole('super_admin');
        });

        $this->newLine();
        $this->components->info("✅ Super Admin created and assigned to school: {$school->name}");
    }

    /**
     * Step 6: Clear all caches.
     */
    private function step6ClearCache(): void
    {
        $this->components->task('Clearing application cache', function () {
            $this->call('optimize:clear');
        });
    }

    /**
     * Display next steps for the user.
     */
    private function displayNextSteps(): void
    {
        $this->line('📝 Next Steps:');
        $this->line('');
        $this->line('  1. Access admin panel at: ' . config('app.url') . '/admin');
        $this->line('  2. Login with the credentials you just created');
        $this->line('  3. Create your first domain module:');
        $this->line('     <fg=green>php artisan lhi:make-module SIAKAD</>');
        $this->line('');
        $this->line('  4. Generate services and actions:');
        $this->line('     <fg=green>php artisan lhi:make-service SIAKAD Student</>');
        $this->line('     <fg=green>php artisan lhi:make-action SIAKAD EnrollStudent</>');
        $this->line('');
        $this->line('  📚 Documentation: ARCHITECTURE.md');
        $this->line('');
    }
}
