<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CctvResource\Pages;
use App\Models\Cctv;
use App\Models\Building;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class CctvResource extends Resource
{
    protected static ?string $model = Cctv::class;
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationGroup = 'ATCS';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('building_id')->label('Building')
                ->options(Building::query()->pluck('name','id'))->searchable()->required(),
            Forms\Components\Select::make('room_id')->label('Room')
                ->options(Room::query()->pluck('name','id'))->searchable(),
            Forms\Components\TextInput::make('ip_rtsp')->required(),
            Forms\Components\Select::make('status')->options([
                'online'=>'Online','offline'=>'Offline','maintenance'=>'Maintenance'
            ])->required(),
            Forms\Components\TextInput::make('lat')->numeric()->maxValue(90)->minValue(-90),
            Forms\Components\TextInput::make('lng')->numeric()->maxValue(180)->minValue(-180),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('building.name')->label('Building')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('room.name')->label('Room')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('ip_rtsp')->label('RTSP')->wrap(),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'success' => 'online',
                    'danger' => 'offline',
                    'warning' => 'maintenance',
                ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Action::make('export')
                    ->label('Export to Excel')
                    ->url(fn()=> route('export.excel', ['entity' => 'cctvs']))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCctvs::route('/'),
            'create' => Pages\CreateCctv::route('/create'),
            'edit' => Pages\EditCctv::route('/{record}/edit'),
        ];
    }
}

