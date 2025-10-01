<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemNotificationResource\Pages;
use App\Models\SystemNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemNotificationResource extends Resource
{
    protected static ?string $model = SystemNotification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'ATCS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('type')->required(),
            Forms\Components\Textarea::make('message')->rows(4)->required(),
            Forms\Components\DateTimePicker::make('read_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('type')->searchable(),
                Tables\Columns\TextColumn::make('message')->limit(60),
                Tables\Columns\TextColumn::make('read_at')->dateTime(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemNotifications::route('/'),
            'create' => Pages\CreateSystemNotification::route('/create'),
            'edit' => Pages\EditSystemNotification::route('/{record}/edit'),
        ];
    }
}

