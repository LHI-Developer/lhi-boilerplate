<?php

namespace Modules\Core\Filament\Resources\UserResource\Schemas;

use Filament\Infolists;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('email'),
                        Infolists\Components\TextEntry::make('school.name')
                            ->label('School'),
                        Infolists\Components\TextEntry::make('roles.name')
                            ->badge()
                            ->label('Roles'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Metadata')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2)
            ]);
    }
}
