<?php

namespace Modules\Core\Filament\Resources\SystemSettingResource\Pages;

use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Resources\SystemSettingResource;
use Modules\Core\Services\SettingService;

class ManageSystemSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = SystemSettingResource::class;

    protected string $view = 'core::filament.pages.manage-system-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settingService = app(SettingService::class);

        $this->form->fill([
            'app_name' => $settingService->get('app_name', 'SIT LHI Admin'),
            'panel_color' => $settingService->get('panel_color', '#f59e0b'), // Amber default
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Application Settings')
                    ->description('Configure global application settings')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Application Name')
                            ->helperText('The name displayed in the navigation bar')
                            ->required()
                            ->maxLength(255)
                            ->default('SIT LHI Admin'),

                        Forms\Components\ColorPicker::make('panel_color')
                            ->label('Primary Color')
                            ->helperText('The primary color used throughout the admin panel')
                            ->required()
                            ->default('#f59e0b'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settingService = app(SettingService::class);

        $settingService->set('app_name', $data['app_name']);
        $settingService->set('panel_color', $data['panel_color']);

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->body('System settings have been updated successfully.')
            ->send();

        // Redirect to refresh the panel with new settings
        redirect()->to(static::getUrl());
    }
}
