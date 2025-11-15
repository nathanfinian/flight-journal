<?php

use App\Livewire\Admin;
use App\Livewire\Settings;
use App\Livewire\Dashboard;
use App\Actions\Auth\Logout;
use App\Livewire\Admin\Role;
use App\Livewire\Admin\RoleModify;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Users;
use App\Livewire\FlightHistory;
use App\Livewire\FlightJournal;
use App\Livewire\FlightSchedule;
use App\Livewire\Settings\Branch;
use App\Livewire\Settings\Account;
use App\Livewire\Settings\Airline;
use App\Livewire\Settings\Airport;
use App\Livewire\Settings\Aircraft;
use App\Livewire\Admin\UsersControl;
use App\Livewire\Settings\Equipment;
use App\Livewire\FlightJournalActual;
use App\Livewire\FlightJournalModify;
use Illuminate\Support\Facades\Route;
use App\Livewire\FlightScheduleModify;
use App\Livewire\Settings\AirportRoute;
use App\Livewire\Settings\BranchModify;
use App\Livewire\Settings\AirlineModify;
use App\Livewire\Settings\AirportModify;
use App\Livewire\Settings\AircraftModify;
use App\Livewire\Settings\EquipmentModify;
use App\Livewire\Settings\AirportRouteModify;

Route::get('/', Login::class)->name('login');
// Route::get('/', Livewire\Home::class)->name('home');

/** AUTH ROUTES */
// Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    //Flight Journal Scheduled, create and edit from scheduled to actual
    Route::get('/flight-journal', FlightJournal::class)->name('flight-journal');
    Route::get('/flight-journal/create', FlightJournalModify::class)
        ->name('flight-journal.create');
    Route::get('/flight-journal/{id:id}/edit', FlightJournalModify::class)
        ->whereNumber('id') // optional safety
        ->name('flight-journal.edit');  // <-- {airline} matches the type-hint

    //Flight Journal Actual and its modify page
    Route::get('/flight-journal/actual', FlightJournalActual::class)
        ->name('flight-journal.actual');

    //Flight scheduling pages CRUD
    Route::get('/flight-schedule', FlightSchedule::class)->name('flight-schedule');
    Route::get('/flight-schedule/create', FlightScheduleModify::class)
        ->name('flight-schedule.create');
    Route::get('/flight-schedule/{scheduled:id}/edit', FlightScheduleModify::class)
        ->whereNumber('scheduled') // optional safety
        ->name('flight-schedule.edit');  // <-- {airline} matches the type-hint

    //Flight History
    Route::get('/flight-history', FlightHistory::class)->name('flight-history');

    //Settings pages start
    Route::get('/settings', Settings::class)->name('settings.index');
    Route::get('/settings/account', Account::class)->name('settings.account');
    
    Route::get('/settings/airline', Airline::class)->name('settings.airline');
    Route::get('/settings/airline/create', AirlineModify::class)
        ->name('settings.airline.create');
    Route::get('/settings/airline/{airline:id}/edit', AirlineModify::class)
        ->whereNumber('airline') // optional safety
        ->name('settings.airline.edit');  // <-- {airline} matches the type-hint

    Route::get('/settings/aircraft', Aircraft::class)->name('settings.aircraft');
    Route::get('/settings/aircraft/create', AircraftModify::class)
        ->name('settings.aircraft.create');
    Route::get('/settings/aircraft/{aircraft:id}/edit', AircraftModify::class)
        ->whereNumber('aircraft') // optional safety
        ->name('settings.aircraft.edit');  // <-- {aircraft} matches the type-hint

    Route::get('/settings/equipment', Equipment::class)->name('settings.equipment');
    Route::get('/settings/equipment/create', EquipmentModify::class)
        ->name('settings.equipment.create');
    Route::get('/settings/equipment/{equipment:id}/edit', EquipmentModify::class)
        ->whereNumber('equipment') // optional safety
        ->name('settings.equipment.edit');  // <-- {equipment} matches the type-hint

    Route::get('/settings/airport', Airport::class)->name('settings.airport');
    Route::get('/settings/airport/create', AirportModify::class)
        ->name('settings.airport.create');
    Route::get('/settings/airport/{airport:id}/edit', AirportModify::class)
        ->whereNumber('airport') // optional safety
        ->name('settings.airport.edit');  // <-- {airport} matches the type-hint

    Route::get('/settings/airport-route', AirportRoute::class)->name('settings.airport-route');
    Route::get('/settings/airport-route/create', AirportRouteModify::class)
        ->name('settings.airport-route.create');
    Route::get('/settings/airport-route/{route:id}/edit', AirportRouteModify::class)
        ->whereNumber('route') // optional safety
        ->name('settings.airport-route.edit');  // <-- {airport-route} matches the type-hint

    Route::get('/settings/branch', Branch::class)->name('settings.branch');
    Route::get('/settings/branch/create', BranchModify::class)
        ->name('settings.branch.create');
    Route::get('/settings/branch/{branch}/edit', BranchModify::class)
        ->whereNumber('branch') // optional safety
        ->name('settings.branch.edit');  // <-- {branch} matches the type-hint
    //Settings pages end
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', Logout::class)
        ->name('app.auth.logout');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', Admin::class)->name('admin.index');

    Route::get('/admin/users', Users::class)->name('admin.users');
    Route::get('/admin/users/create', UsersControl::class)
        ->name('admin.users.create');
    Route::get('/admin/users/{userid:id}/edit', UsersControl::class)
        ->whereNumber('userid') // optional safety
        ->name('admin.users.edit');

    Route::get('/admin/roles', Role::class)->name('admin.roles');
    Route::get('/admin/roles/create', RoleModify::class)
        ->name('admin.roles.create');
    Route::get('/admin/roles/{roleid:id}/edit', RoleModify::class)
        ->whereNumber('roleid') // optional safety
        ->name('admin.roles.edit');
});
