<?php

namespace App\Filament\Resources;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Admins & Teachers';

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

               Forms\Components\TextInput::make('email')
                   ->required()
                   ->email()
                   ->maxLength(255),

               Forms\Components\TextInput::make('password')
                   ->password()
                   ->required()
                   ->minLength(8),

               Forms\Components\Select::make('role')
                   ->options([
                       'teacher' => 'Teacher',
                       'admin' => 'Admin'
                   ])
                   ->required(),

               Forms\Components\TextInput::make('embg')
                   ->required()
                   ->maxLength(255),

               Forms\Components\DatePicker::make('birthday')
                   ->required(),

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

            Tables\Columns\TextColumn::make('role')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('phone_number')
                ->sortable(),

            Tables\Columns\TextColumn::make('courses_count')
                ->label('Courses Count')
                ->numeric(),

            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'teacher' => 'Teacher',
                    ])
                    ->label('Role'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
