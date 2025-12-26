<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\School;
use Modules\Core\Models\User;

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
    protected $description = 'Install and setup SIT LHI Boilerplate (Non-Interactive Mode)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🚀 SIT LHI Boilerplate - Automated Installation');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        try {
            // Step 1: Publish Configs
            $this->step1PublishConfigs();

            // Step 2: Run Migrations
            $this->step2RunMigrations();

            // Step 3: Seed Default School (BEFORE Shield & User)
            $school = $this->step3SeedDefaultSchool();

            // Step 4: Create Super Admin User (BEFORE Shield)
            $user = $this->step4CreateSuperAdmin($school);

            // Step 5: Setup Shield (Non-Interactive)
            $this->step5SetupShield();

            // Step 6: Assign Super Admin Role
            $this->step6AssignSuperAdminRole($user);

            // Step 7: Clear Cache
            $this->step7ClearCache();

            $this->newLine();
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('✅ Installation completed successfully!');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->newLine();
            $this->displayCredentials($user);
            $this->displayNextSteps();

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Installation failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return self::FAILURE;
        }
    }

    /**
     * Step 1: Publish configuration files.
     */
    private function step1PublishConfigs(): void
    {
        $this->info('📦 Step 1: Publishing configuration files...');

        $this->callSilently('vendor:publish', [
            '--tag' => 'filament-config',
            '--force' => true,
        ]);
        $this->line('   ✓ Filament configs published');

        $this->callSilently('vendor:publish', [
            '--tag' => 'filament-shield-config',
            '--force' => true,
        ]);
        $this->line('   ✓ Shield configs published');

        $this->newLine();
    }

    /**
     * Step 2: Run database migrations.
     */
    private function step2RunMigrations(): void
    {
        $this->info('🗄️  Step 2: Running database migrations...');

        if ($this->option('fresh')) {
            $this->warn('   ⚠️  Running FRESH migrations (dropping all tables)...');

            $this->call('migrate:fresh', [
                '--force' => true,
            ]);
        } else {
            $this->call('migrate', [
                '--force' => true,
            ]);
        }

        $this->line('   ✓ Database migrations completed');

        if ($this->option('seed')) {
            $this->call('db:seed', ['--force' => true]);
            $this->line('   ✓ Database seeded');
        }

        $this->newLine();
    }

    /**
     * Step 3: Seed default school.
     */
    private function step3SeedDefaultSchool(): School
    {
        $this->info('🏫 Step 3: Creating default school...');

        // Check if school already exists
        $existingSchool = School::where('code', 'LHI001')->first();

        if ($existingSchool) {
            $this->line('   ℹ️  Default school already exists, using existing...');
            $school = $existingSchool;
        } else {
            $school = School::create([
                'name' => env('DEFAULT_SCHOOL_NAME', 'SIT LHI Demo'),
                'code' => env('DEFAULT_SCHOOL_CODE', 'LHI001'),
                'npsn' => env('DEFAULT_SCHOOL_NPSN', ''),
                'address' => env('DEFAULT_SCHOOL_ADDRESS', 'Jakarta, Indonesia'),
                'phone' => env('DEFAULT_SCHOOL_PHONE', '021-12345678'),
                'email' => env('DEFAULT_SCHOOL_EMAIL', 'contact@lhi.sch.id'),
                'is_active' => true,
            ]);
            $this->line('   ✓ Default school created: ' . $school->name);
        }

        $this->line('   📌 School ID: ' . $school->id);
        $this->line('   📌 School Code: ' . $school->code);
        $this->newLine();

        return $school;
    }

    /**
     * Step 4: Create super admin user using Eloquent.
     */
    private function step4CreateSuperAdmin(School $school): User
    {
        $this->info('👤 Step 4: Creating super admin user...');

        $name = env('ADMIN_NAME', 'Super Admin');
        $email = env('ADMIN_EMAIL', 'admin@lhi.sch.id');
        $password = env('ADMIN_PASSWORD', 'password');

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $this->line('   ℹ️  User already exists, updating...');
            $existingUser->update([
                'name' => $name,
                'password' => Hash::make($password),
                'school_id' => $school->id,
            ]);
            $user = $existingUser;
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'school_id' => $school->id,
            ]);
            $this->line('   ✓ Super admin user created');
        }

        $this->line('   📌 Name: ' . $user->name);
        $this->line('   📌 Email: ' . $user->email);
        $this->line('   📌 School: ' . $school->name);
        $this->newLine();

        return $user;
    }

    /**
     * Step 5: Setup Shield (Non-Interactive).
     */
    private function step5SetupShield(): void
    {
        $this->info('🛡️  Step 5: Setting up Shield permissions...');

        // Install Shield for admin panel (use call for debugging visibility)
        $this->call('shield:install', [
            'panel' => 'admin',
            '--no-interaction' => true,
        ]);
        $this->line('   ✓ Shield installed for admin panel');

        // Generate all permissions for resources with explicit panel parameter
        $this->call('shield:generate', [
            '--panel' => 'admin',
            '--all' => true,
        ]);
        $this->line('   ✓ Permissions generated for all resources');

        $this->newLine();
    }

    /**
     * Step 6: Assign super_admin role programmatically.
     */
    private function step6AssignSuperAdminRole(User $user): void
    {
        $this->info('👑 Step 6: Assigning super_admin role...');

        // Check if user already has the role
        if ($user->hasRole('super_admin')) {
            $this->line('   ℹ️  User already has super_admin role');
        } else {
            $user->assignRole('super_admin');
            $this->line('   ✓ super_admin role assigned to ' . $user->email);
        }

        $this->newLine();
    }

    /**
     * Step 7: Clear all caches.
     */
    private function step7ClearCache(): void
    {
        $this->info('🧹 Step 7: Clearing application cache...');

        $this->call('optimize:clear');
        $this->line('   ✓ All caches cleared');

        $this->newLine();
    }

    /**
     * Display login credentials.
     */
    private function displayCredentials(User $user): void
    {
        $this->info('🔑 Login Credentials:');
        $this->table(
            ['Field', 'Value'],
            [
                ['URL', config('app.url') . '/admin'],
                ['Email', $user->email],
                ['Password', env('ADMIN_PASSWORD', 'password')],
            ]
        );
        $this->newLine();
    }

    /**
     * Display next steps for the user.
     */
    private function displayNextSteps(): void
    {
        $this->info('📝 Next Steps:');
        $this->line('');
        $this->line('  1. Access admin panel: ' . config('app.url') . '/admin');
        $this->line('  2. Login with credentials above');
        $this->line('  3. Create your first domain module:');
        $this->line('     <fg=green>php artisan lhi:make-module SIAKAD</>');
        $this->line('');
        $this->line('  4. Generate services and actions:');
        $this->line('     <fg=green>php artisan lhi:make-service SIAKAD Student</>');
        $this->line('     <fg=green>php artisan lhi:make-action SIAKAD EnrollStudent</>');
        $this->line('');
        $this->line('📚 Documentation: README.md & ARCHITECTURE.md');
        $this->line('');
    }
}
