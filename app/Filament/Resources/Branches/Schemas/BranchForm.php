<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')->required(),
                Textarea::make('alamat')->required()->columnSpanFull(),
                TextInput::make('telepon')->tel(),
                View::make('filament.forms.components.branch-location-picker')
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->minValue(-90)
                    ->maxValue(90)
                    ->readOnly()
                    ->helperText('Pilih lokasi dengan mengeklik peta.'),
                TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->minValue(-180)
                    ->maxValue(180)
                    ->readOnly()
                    ->helperText('Nilai koordinat diisi otomatis dari peta.'),
            ]);
    }
}
