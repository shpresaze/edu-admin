<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Course Name')
                    ->required(),

                Forms\Components\Select::make('teacher_id')
                    ->label('Teacher')
                    ->options(function () {
                        return Teacher::all()->pluck('full_name', 'id');
                    })
                    ->required(),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'waiting_to_start' => 'Waiting to Start',
                        'ongoing' => 'Ongoing',
                        'done' => 'Done',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('schedule')
                    ->label('Schedule')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                 Tables\Columns\TextColumn::make('name')
                     ->sortable()
                     ->searchable(),

                 Tables\Columns\TextColumn::make('teacher.full_name')
                     ->sortable(),

                 Tables\Columns\TextColumn::make('start_date')
                     ->sortable()
                     ->date(),

                 Tables\Columns\TextColumn::make('status')
                     ->sortable()
                     ->searchable(),

                 Tables\Columns\TextColumn::make('schedule')
                     ->sortable(),
             ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->recordSelectSearchColumns(['name'])
                    ->preloadRecordSelect(),
                ])
                ->actions([
                    Tables\Actions\DetachAction::make(),
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
