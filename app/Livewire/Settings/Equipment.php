<?php

namespace App\Livewire\Settings;

use App\Models\Equipment as EquipmentModel;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Equipment extends TableComponent
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                EquipmentModel::query()
                    ->with([
                        'aircraft:id,type_name',
                        'airline:id,name',
                    ])
                    ->join('airlines', 'equipments.airline_id', '=', 'airlines.id')
                    ->select('equipments.*')
            )
            ->defaultSort('airlines.name')
            ->searchPlaceholder('Cari registration')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->recordUrl(fn (EquipmentModel $record): string => route('settings.equipment.edit', ['equipment' => $record->id]))
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('airline.name')
                    ->label('Airline')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('registration')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('aircraft.type_name')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'ACTIVE' => 'success',
                        'RETIRED' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('airline')
                    ->relationship('airline', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'ACTIVE',
                        'RETIRED' => 'INACTIVE',
                    ]),
            ], layout: FiltersLayout::BeforeContent)
            ->filtersApplyAction(fn (Action $action): Action => $action->color('gray'))
            ->emptyStateHeading('No equipment found.')
            ->persistFiltersInSession();
    }

    public function render()
    {
        return view('livewire.settings.equipment');
    }
}
