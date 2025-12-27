<?php

namespace Modules\Core\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Resources\SystemSettingResource\Pages;
use Modules\Core\Models\SystemSetting;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 99;

    protected static ?string $navigationLabel = 'System Settings';

    protected static ?string $modelLabel = 'System Setting';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Form will be defined in the page itself for Simple Resource
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSystemSettings::route('/'),
        ];
    }
}
