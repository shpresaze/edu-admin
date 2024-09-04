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
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return !Auth::user()->isTeacher();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'name')
                    ->required(),

                Forms\Components\Select::make('student_id')
                    ->label('Student')
                    ->relationship('student', 'name')
                    ->required(),

                Forms\Components\Select::make('currency_of_payments')
                    ->label('Currency Of Payments')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'MKD' => 'MKD',
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

                Forms\Components\Select::make('status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.name')
                ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('currency_of_payments')
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

                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->searchable(),
            ])
             ->filters([
                Tables\Filters\Filter::make('paid')
                     ->label('Paid')
                     ->query(function (Builder $query) {
                         return $query->where('status', 'paid');
                     }),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(function (Builder $query) {
                        return $query->whereDate('due_date', '<', now());
                    }),
                Tables\Filters\Filter::make('unpaid')
                    ->label('Unpaid')
                    ->query(function (Builder $query) {
                        return $query->where('status', 'unpaid');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('sendEmails')
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $emailContent = $data['content'];

                                Mail::raw($emailContent, function ($message) use ($record, $data) {
                                    $message->subject($data['subject']);
                                    $message->from('team@eduadmin.com', 'EduAdmin');
                                    $message->to($record->student->email);
                                });
                            }
                        })
                        ->form([
                            Forms\Components\Textarea::make('subject')
                                ->label('Subject')
                                ->required()
                                ->default('New Message | EduAdmin'),
                            Forms\Components\Textarea::make('content')
                                ->label('Content')
                                ->required(),
                        ])
                        ->label('Send Emails')
                        ->icon('heroicon-o-at-symbol'),
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
