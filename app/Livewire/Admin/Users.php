<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Users extends TableComponent
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with([
                        'role:id,label',
                        'branch:id,name',
                    ])
            )
            ->defaultSort('name')
            ->searchPlaceholder('Search username or name')
            ->paginated([5, 10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->recordUrl(fn (User $record): string => route('admin.users.edit', ['userid' => $record->id]))
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role.label')
                    ->label('Role')
                    ->sortable()
                    ->placeholder('Empty'),
                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->sortable()
                    ->placeholder('Empty'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'label'),
                SelectFilter::make('branch')
                    ->relationship('branch', 'name'),
            ], layout: FiltersLayout::BeforeContent)
            ->filtersApplyAction(fn (Action $action): Action => $action->color('gray'))
            ->emptyStateHeading('No user found.')
            ->persistFiltersInSession();
    }

    public function render()
    {
        return view('livewire.admin.users');
    }
}
