<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
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

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return !Auth::user()->isTeacher();
    }

    public static function form(Form $form): Form
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('enrolled_courses_count')
                    ->label('Enrolled Courses')
                    ->numeric(),

                Tables\Columns\TextColumn::make('completed_courses_count')
                    ->label('Completed Courses')
                    ->numeric(),
            ])
            ->filters([
                // Add any filters here if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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

    public static function getRelations(): array
    {
        if(Auth::check() && Auth::user()->isTeacher()){
            return [
                RelationManagers\CoursesRelationManager::class,
            ];
        }
        else {
            return [
                RelationManagers\CoursesRelationManager::class,
                RelationManagers\PaymentsRelationManager::class,
            ];
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }
}
