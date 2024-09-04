<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('first_name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('last_name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('embg')
                ->required()
                ->maxLength(255),

            Forms\Components\DatePicker::make('birthday')
                ->required(),

            Forms\Components\TextInput::make('email')
                ->required()
                ->email()
                ->maxLength(255),

            Forms\Components\TextInput::make('phone_number')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('address')
                ->required()
                ->maxLength(65535),

            Forms\Components\TextInput::make('points')
                ->label('Points')
                ->required()
                ->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn (Student $record): string => "{$record->first_name} {$record->last_name} {$record->embg}")
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->limit(50),

                Tables\Columns\TextColumn::make('pivot.points')
                    ->label('Points')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
               Tables\Actions\CreateAction::make(),
               Tables\Actions\AttachAction::make()
                   ->recordSelectSearchColumns(['first_name', 'last_name', 'embg'])
                   ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
