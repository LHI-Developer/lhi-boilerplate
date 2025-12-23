# SIT LHI Laravel 12 Modular Boilerplate

> **Enterprise-grade Laravel boilerplate with modular architecture, multi-tenancy, and scaffolding generators for SIT LHI ecosystem.**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4.x-F59E0B?style=flat)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)

---

## 🎯 Overview

This boilerplate provides a **production-ready foundation** for building modular, multi-tenant educational management systems for the SIT LHI (Sekolah Islam Terpadu Lukman Hakim Indonesia) ecosystem. It enforces architectural conventions through automated generators and provides essential infrastructure for rapid development.

### Built For

- 🏫 **SIAKAD** - Academic Information System
- 📊 **Rapor Digital** - Digital Report Cards
- 💰 **Payment System** - Tuition & Fee Management
- 📢 **Notification System** - School Communication
- 📚 **LMS** - Learning Management System

---

## ✨ Key Features

### 🏗️ Modular Architecture
- **Domain-Driven Design** - Modules organized by business domain
- **Laravel Modules** - Full PSR-4 autoloading with `nwidart/laravel-modules`
- **Filament Integration** - Auto-discovery via `coolsam/modules`
- **Separation of Concerns** - Clear boundaries: Models, Services, Actions, UI

### 🏢 Multi-Tenancy
- **School-based isolation** - Automatic data scoping by school
- **HasSchoolScope trait** - Auto-filters all queries
- **User-School association** - Built-in tenant context
- **Cross-school admin access** - Configurable permissions

### 🛡️ Security & Permissions
- **Filament Shield** - Role-based access control
- **Spatie Permissions** - Flexible permission system
- **Super Admin** - Pre-configured admin role
- **Policy-driven** - Resource-level authorization

### 🎨 Admin Panel
- **Filament 4** - Modern, beautiful admin interface
- **Shield Integration** - Permission management UI
- **Multi-panel support** - Separate admin/teacher/student panels
- **Resource scaffolding** - Quick CRUD generation

### 🚀 Code Generators
- **`lhi:install`** - One-command setup with interactive wizard
- **`lhi:make-module`** - Create modules with standard structure
- **`lhi:make-service`** - Generate service classes
- **`lhi:make-action`** - Generate single-purpose actions
- **Custom stubs** - Pre-configured templates with best practices

---

## 📋 Requirements

- **PHP** 8.2 or higher
- **Composer** 2.x
- **Node.js** 18.x or higher
- **NPM** or **Yarn**
- **MySQL** 8.0+ or **PostgreSQL** 13+
- **Laravel Herd**, **Valet**, or **Homestead** (recommended)

---

## 🚀 Quick Start

### 1️⃣ Installation

```bash
# Clone repository
git clone <your-repository-url> lhi-project
cd lhi-project

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate
```

### 2️⃣ Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lhi_boilerplate
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3️⃣ Run Automated Setup

```bash
php artisan lhi:install
```

**Follow interactive prompts** to:
- ✅ Publish configurations
- ✅ Run database migrations
- ✅ Setup permissions & roles
- ✅ Create your first school
- ✅ Create super admin account

### 4️⃣ Build Assets & Start Server

```bash
# Compile assets
npm run build

# Start development server
php artisan serve
```

### 5️⃣ Access Admin Panel

Navigate to: **http://localhost:8000/admin**

Login with credentials created during installation.

---

## 📁 Project Structure

```
lhi-boilerplate/
├── app/
│   ├── Console/Commands/          # Custom Artisan commands
│   │   ├── LhiInstallCommand.php  # Setup automation
│   │   ├── LhiMakeModuleCommand.php
│   │   ├── LhiMakeServiceCommand.php
│   │   └── LhiMakeActionCommand.php
│   └── Models/
│       └── User.php                # With HasRoles & school_id
│
├── modules/                        # Domain modules
│   ├── Core/                       # Infrastructure module
│   │   ├── Infrastructure/
│   │   │   ├── BaseModel.php      # Base for all models
│   │   │   └── Traits/
│   │   │       └── HasSchoolScope.php
│   │   ├── Models/
│   │   │   └── School.php
│   │   └── Services/
│   │       └── TenantService.php
│   │
│   └── [YourModule]/              # Your domain modules
│       ├── Actions/                # Single-purpose operations
│       ├── Services/               # Business logic layer
│       ├── Models/                 # Data models
│       ├── Filament/              # Admin resources
│       ├── Livewire/              # Interactive components
│       ├── Database/              # Migrations & seeders
│       └── Routes/                # Web & API routes
│
├── stubs/lhi/                     # Generator templates
│   ├── service.stub
│   └── action.stub
│
├── bootstrap/
│   └── providers.php              # Core module registered here
│
├── config/
│   └── modules.php                # Module configuration
│
├── ARCHITECTURE.md                # Architecture documentation
└── README.md                      # You are here
```

---

## 🎯 Usage Guide

### Creating a New Module

```bash
# Create module with standard structure
php artisan lhi:make-module SIAKAD

# Generated structure:
# modules/SIAKAD/
# ├── Actions/
# ├── Services/
# ├── Models/
# ├── Filament/
# ├── Livewire/
# ├── Database/
# ├── Routes/
# └── Infrastructure/
```

### Adding Business Logic

```bash
# Create a Service
php artisan lhi:make-service SIAKAD Student

# Create an Action
php artisan lhi:make-action SIAKAD EnrollStudent

# Create a Model
php artisan module:make-model Student SIAKAD
```

### Multi-Tenancy Implementation

**Apply to any model for automatic school scoping:**

```php
use Modules\Core\Infrastructure\BaseModel;
use Modules\Core\Infrastructure\Traits\HasSchoolScope;

class Student extends BaseModel
{
    use HasSchoolScope;

    protected $fillable = ['name', 'nis', 'class'];
}
```

**What it does:**
- ✅ Auto-assigns `school_id` on creation
- ✅ Filters all queries by current user's school
- ✅ Prevents cross-school data leaks

**Query examples:**

```php
// Automatically scoped to current school
$students = Student::all();

// Override for admin/superadmin
$allStudents = Student::withoutSchoolScope()->get();

// Specific school
$students = Student::forSchool(2)->get();
```

---

## 🛠️ Available Commands

### Installation & Setup

```bash
# Fresh installation
php artisan lhi:install

# Fresh install with database reset
php artisan lhi:install --fresh

# With seeders
php artisan lhi:install --seed
```

### Module Generators

```bash
# Create module (PascalCase)
php artisan lhi:make-module {ModuleName}

# Create service (auto-adds Service suffix)
php artisan lhi:make-service {Module} {ServiceName}

# Create action (auto-adds Action suffix)
php artisan lhi:make-action {Module} {ActionName}
```

### Module Management

```bash
# List all modules
php artisan module:list

# Enable/disable module
php artisan module:enable {ModuleName}
php artisan module:disable {ModuleName}

# Module migrations
php artisan module:migrate {ModuleName}
php artisan module:migrate-reset {ModuleName}
```

### Shield & Permissions

```bash
# Generate permissions for all resources
php artisan shield:generate --all

# Create super admin
php artisan shield:super-admin

# Publish Shield resources
php artisan shield:publish
```

---

## 📚 Architecture Principles

### 1. **Convention Enforcement**
- Generators ensure consistent folder structure
- Naming conventions validated automatically
- Plain Laravel code output (no runtime dependencies)

### 2. **Separation of Concerns**

| Layer | Purpose | Location |
|-------|---------|----------|
| **Models** | Data representation & relationships | `Models/` |
| **Services** | Business logic & domain rules | `Services/` |
| **Actions** | Single-purpose operations | `Actions/` |
| **UI** | Presentation (Filament/Livewire) | `Filament/`, `Livewire/` |

### 3. **Module Isolation**

**Safe Zone** (auto-generated):
- ✅ Module structure
- ✅ Basic models & migrations
- ✅ Filament resources
- ✅ Policy stubs

**Danger Zone** (manual only):
- ⚠️ Complex business logic
- ⚠️ Workflow & approval systems
- ⚠️ Advanced reporting

### 4. **Core Module**

The **Core** module is permanent infrastructure:
- Manually registered in `bootstrap/providers.php`
- Contains: BaseModel, HasSchoolScope, School, TenantService
- Cannot be disabled

**Other modules** use dynamic registration via `modules_statuses.json`.

---

## 🔐 Security Best Practices

### Multi-Tenancy
- Always use `HasSchoolScope` for tenant-specific models
- Never bypass scope without explicit admin check
- Test cross-tenant access scenarios

### Permissions
- Define policies for all Filament resources
- Use Shield's role-based access control
- Review permissions before production

### Data Validation
- Validate in Service layer, not controllers
- Use Form Requests for complex validation
- Sanitize user input

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run module tests
php artisan test modules/SIAKAD/Tests
```

**Write tests for:**
- ✅ Services (business logic)
- ✅ Actions (operations)
- ✅ Multi-tenancy scoping
- ✅ Permissions & authorization

---

## 🚢 Deployment

### Production Checklist

```bash
# 1. Optimize application
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Build production assets
npm run build

# 3. Run migrations
php artisan migrate --force

# 4. Setup permissions
php artisan shield:generate --all

# 5. Clear cache
php artisan optimize:clear
```

### Environment Variables

Key `.env` settings for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

---

## 📖 Documentation

- **[ARCHITECTURE.md](ARCHITECTURE.md)** - System architecture & principles
- **[Implementation Plan](docs/implementation_plan.md)** - Development phases
- **[Quick Start Guide](docs/quickstart.md)** - Getting started
- **[Walkthrough](docs/walkthrough.md)** - Implementation details

---

## 🤝 Contributing

This boilerplate follows strict architectural conventions. Before contributing:

1. Read `ARCHITECTURE.md` thoroughly
2. Follow existing code patterns
3. Use provided generators for new code
4. Write tests for new features
5. Update documentation

---

## 📝 License

This project is proprietary software for SIT LHI ecosystem.

---

## 👥 Credits

**Developed for:** Sekolah Islam Terpadu Lukman Hakim Indonesia (SIT LHI)

**Tech Stack:**
- Laravel 12
- Filament 4
- Laravel Modules
- Filament Shield
- Spatie Permissions

---

## 🆘 Support

For issues, questions, or feature requests:

1. Check existing documentation
2. Review `ARCHITECTURE.md` for design decisions
3. Consult module-specific README files
4. Check error logs in `storage/logs/`

---

**Happy Coding! 🚀**

Built with ❤️ for quality education management systems.
