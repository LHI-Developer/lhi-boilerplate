# Arsitektur SIT LHI: Laravel 12 Modular Monolith

## 1. Visi & Identitas Sistem
*   **Tujuan Utama**: Menjadi template standar yang konsisten, scalable, dan minim technical debt untuk seluruh ekosistem digital SIT LHI.
*   **Ekosistem Terintegrasi**: SIAKAD, Rapor Digital, Sistem Keuangan, Manajemen Notifikasi, dan LMS.
*   **Tech Stack**: Laravel 12, Filament 4, Livewire.
*   **Module Management**: 
    *   `nwidart/laravel-modules`: Manajemen struktur module di `modules/`.
    *   `coolsam/modules`: Bridge untuk integrasi otomatis module dengan Filament 4.

## 2. Prinsip Arsitektur (Modular Monolith)
*   **Domain-Driven**: Struktur folder mengikuti domain bisnis di dalam folder `modules/`.
*   **Separation of Concerns**:
    *   **Model**: Hanya representasi data dan relasi.
    *   **Service**: Logika bisnis utama dan aturan domain.
    *   **Action**: Proses spesifik yang dapat dipanggil ulang (e.g., `PublishRaporAction`).
    *   **UI**: Filament (Admin) atau Livewire (Dashboard) untuk presentasi.

## 3. Filosofi: Convention Enforcement via Scaffolding
*   **Boilerplate sebagai Konstitusi**: Mendefinisikan standar yang tidak dapat dilanggar.
*   **Generator sebagai Penegak Aturan**: Membekukan knowledge engineering untuk menjamin konsistensi tanpa bergantung pada disiplin manusia.
*   **Plain Laravel Code**: Hasil generasi adalah kode Laravel/Filament standar yang dapat diedit manual dan tidak memiliki runtime dependency pada generator.
*   **Disposable Generator**: Proyek tetap berjalan normal meskipun generator dihapus.

### Safe Zone (Wajib di-scaffold)
*   Struktur module dasar (`Actions/`, `Services/`, `Models/`, dll)
*   Model & Migration awal
*   Filament Resource basic (Form & Table)
*   Policy stub & Permission CRUD

### Danger Zone (Manual Only)
*   Business logic kompleks di Service/Action
*   Workflow & sistem approval
*   Reporting & query kompleks

## 4. Custom Generator Commands
*   `php artisan lhi:make-module {name}` - Buat skeleton module lengkap
*   `php artisan lhi:make-service {module} {name}` - Buat Service class dengan suffix `Service`
*   `php artisan lhi:make-action {module} {name}` - Buat Action class untuk proses spesifik

## 5. Standar Struktur Folder Module
Setiap module dalam `modules/` wajib memiliki:
*   `Actions/` & `Services/`: Logika bisnis.
*   `Models/`: Definisi data.
*   `Filament/` & `Livewire/`: Komponen UI.
*   `Database/`: Migrasi dan seeder.
*   `Routes/`: Definisi routing.

**Penamaan Konvensi**:
*   Module: PascalCase (contoh: `Rapor`, `Keuangan`).
*   Service: Akhiran `Service` (contoh: `RaporService`, `PaymentService`).

**Konfigurasi**: File `config/modules.php` dikonfigurasi untuk otomatis generate struktur folder standar saat membuat module baru via `php artisan module:make`.

## 6. Aturan Interaksi & Isolasi Data
*   **Tight Coupling Dilarang**: Komunikasi antar module harus melalui Service atau Event/Listener.
*   **Akses Data Lintas Module**:
    *   **Read-only**: Boleh akses Model module lain.
    *   **Write/Update**: Dilarang keras. Harus melalui Service module pemilik.
*   **Keputusan Bisnis**: Harus di Service/Action, bukan di UI.

## 7. Module Core (Fondasi Teknis)
Fondasi global tanpa logika bisnis domain sekolah.
*   **Auth**: Otentikasi, Role, Permission.
*   **Tenant (Multi-school)**: Implementasi `HasSchoolScope`.
*   **Infrastructure**: Base Model, helper global, utilitas teknis.

## 8. Standar Pengembangan & QA
*   **Pemisahan UI & Logika**: UI dilarang melakukan query kompleks.
*   **Testing**: Wajib unit/feature test untuk Action & Service kompleks.
*   **Konvensi Penamaan**: PascalCase untuk module, akhiran `Service` untuk kelas Service.
