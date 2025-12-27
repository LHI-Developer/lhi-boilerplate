<?php

namespace Modules\Core\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\UserResource\Pages;
use Modules\Core\Filament\Resources\UserResource\Schemas;
use Modules\Core\Filament\Resources\UserResource\Tables;
use Modules\Core\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return Schemas\UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return Tables\UserTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return Schemas\UserInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
