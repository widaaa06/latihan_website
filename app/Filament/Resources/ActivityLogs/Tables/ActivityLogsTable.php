<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pelaku')
                    ->placeholder('Sistem')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Aktivitas')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'login' => 'Masuk',
                        'logout' => 'Keluar',
                        'created' => 'Ditambahkan',
                        'updated' => 'Diperbarui',
                        'deleted' => 'Dihapus',
                        default => $state,
                    }),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('properties')
                    ->label('Detail')
                    ->formatStateUsing(fn (mixed $state): string => collect(is_array($state) ? $state : [])
                        ->map(fn (mixed $value, string $key): string => $key.': '.(is_scalar($value) ? (string) $value : json_encode($value)))
                        ->implode(', ') ?: '—')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Aktivitas')
                    ->options([
                        'login' => 'Masuk',
                        'logout' => 'Keluar',
                        'created' => 'Ditambahkan',
                        'updated' => 'Diperbarui',
                        'deleted' => 'Dihapus',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }
}
