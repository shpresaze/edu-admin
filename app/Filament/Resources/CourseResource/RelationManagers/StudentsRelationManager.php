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
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
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
            ->recordTitle(fn (Student $record): string => "{$record->name} {$record->embg}")
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->sortable()
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
                   ->recordSelectSearchColumns(['name', 'embg'])
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
                    BulkAction::make('sendEmails')
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $record) {
                                $emailContent = $data['content'];

                                Mail::raw($emailContent, function ($message) use ($record, $data) {
                                    $message->subject($data['subject']);
                                    $message->from('team@eduadmin.com', 'EduAdmin');
                                    $message->to($record->email);
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
}
