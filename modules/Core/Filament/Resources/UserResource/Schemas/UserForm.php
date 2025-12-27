<?php

namespace Modules\Core\Filament\Resources\UserResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->minLength(8)
                            ->maxLength(255)
                            ->revealable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('School & Roles')
                    ->schema([
                        Forms\Components\Select::make('school_id')
                            ->relationship('school', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('code')
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Roles'),
                    ])
                    ->columns(2),
            ]);
    }
}
