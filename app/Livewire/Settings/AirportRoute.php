<?php

namespace App\Livewire\Settings;

use App\Models\Airport;
use App\Models\Airline;
use App\Models\AirportRoute as AirportRouteModel;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AirportRoute extends TableComponent
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                AirportRouteModel::query()
                    ->with([
                        'origin:id,iata,city',
                        'destination:id,iata,city',
                        'airlines:id,name,icao_code',
                    ])
                    ->join('airports as o', 'airport_routes.origin_id', '=', 'o.id')
                    ->select('airport_routes.*')
            )
            ->defaultSort('o.iata')
            ->searchPlaceholder('Search route or airline')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->recordUrl(fn (AirportRouteModel $record): string => route('settings.airport-route.edit', ['route' => $record->id]))
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('code_pair')
                    ->label('Route (IATA)')
                    ->state(fn (AirportRouteModel $record): string => $record->code_pair)
                    ->searchable([
                        'origin.iata',
                        'destination.iata',
                    ]),
                TextColumn::make('airlines.name')
                    ->label('Airlines')
                    ->badge()
                    ->state(function (AirportRouteModel $record): array {
                        return $record->airlines
                            ->map(fn ($airline): string => $airline->name)
                            ->all();
                    })
                    ->color('purple')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('airlines', function (Builder $subQuery) use ($search): void {
                            $subQuery->where('name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'ACTIVE' => 'success',
                        'INACTIVE' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('airport')
                    ->options(
                        Airport::query()
                            ->orderBy('iata')
                            ->get(['id', 'city', 'iata'])
                            ->mapWithKeys(fn (Airport $airport) => [
                                $airport->id => "{$airport->city} - {$airport->iata}",
                            ])
                            ->all()
                    )
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        $airportId = $data['value'] ?? null;

                        if (blank($airportId)) {
                            return $query;
                        }

                        return $query->where(function (Builder $subQuery) use ($airportId): void {
                            $subQuery
                                ->where('airport_routes.origin_id', $airportId)
                                ->orWhere('airport_routes.destination_id', $airportId);
                        });
                    }),
                SelectFilter::make('airline')
                    ->options(
                        Airline::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all()
                    )
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        $airlineId = $data['value'] ?? null;

                        if (blank($airlineId)) {
                            return $query;
                        }

                        return $query->whereHas('airlines', function (Builder $subQuery) use ($airlineId): void {
                            $subQuery->where('airlines.id', $airlineId);
                        });
                    }),
                SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'ACTIVE',
                        'INACTIVE' => 'INACTIVE',
                    ]),
            ], layout: FiltersLayout::BeforeContentCollapsible)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter') // Add your label here
                    ->icon('heroicon-m-funnel')
            )
            ->filtersApplyAction(fn (Action $action): Action => $action->color('gray'))
            ->emptyStateHeading('No route found.')
            ->persistFiltersInSession();
    }

    public function render()
    {
        return view('livewire.settings.airport-route');
    }
}
