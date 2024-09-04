<?php

namespace App\Filament\Widgets;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
            ->icon('heroicon-o-user')
            ->chart([1,3,5,10,20,40]),
            Stat::make('Students', Student::count())
            ->icon('heroicon-o-academic-cap')
            ->chart([1,3,5,10,20,40]),
            Stat::make('Courses', Course::count())
            ->icon('heroicon-o-book-open')
             ->chart([1,3,5,10,20,40]),
        ];
    }
}
