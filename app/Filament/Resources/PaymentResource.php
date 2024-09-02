<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'name') // Adjust based on your actual relation
                    ->required(),

                Forms\Components\Select::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'first_name') // Adjust based on your actual relation
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->nullable(),

                Forms\Components\TextInput::make('paid_amount')
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('due_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.name') // Adjust based on actual relation
                ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.first_name') // Adjust based on actual relation
                ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('discount')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('due_date')
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                // Add any filters here if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayments::route('/'),
        ];
    }
}
