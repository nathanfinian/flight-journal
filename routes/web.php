<?php

use App\Livewire\Admin;
use App\Livewire\Settings;
use App\Livewire\Dashboard;
use App\Actions\Auth\Logout;
use App\Http\Controllers\CharterDepositController;
use App\Livewire\Admin\Role;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Users;
use App\Livewire\FlightHistory;
use App\Livewire\FlightJournal;
use App\Livewire\FlightSchedule;
use App\Livewire\Settings\Branch;
use App\Livewire\Admin\RoleModify;
use App\Livewire\Settings\Account;
use App\Livewire\Settings\Airline;
use App\Livewire\Settings\Airport;
use App\Livewire\Settings\Aircraft;
use App\Livewire\Admin\UsersControl;
use App\Livewire\Settings\Equipment;
use App\Livewire\FlightJournalActual;
use App\Livewire\FlightJournalModify;
use App\Livewire\Settings\FlightType;
use Illuminate\Support\Facades\Route;
use App\Livewire\FlightScheduleModify;
use App\Livewire\Settings\AirlineRates;
use App\Livewire\Settings\AirportRoute;
use App\Livewire\Settings\BranchModify;
use App\Livewire\Settings\AirlineModify;
use App\Livewire\Settings\AirportModify;
use App\Livewire\Settings\AircraftModify;
use App\Livewire\Settings\EquipmentModify;
use App\Livewire\Settings\FlightTypeModify;
use App\Livewire\Settings\AirlineRatesModify;
use App\Livewire\Settings\AirportRouteModify;

use App\Livewire\Invoice\Index as IndexInvoice;
use App\Livewire\Invoice\Create as CreateInvoice;

use App\Livewire\GseRecap\Index as IndexGSERecap;
use App\Livewire\GseRecap\Create as CreateGSERecap;

use App\Livewire\GseRates\Index as IndexGSERate;
use App\Livewire\GseRates\Create as CreateGSERate;

use App\Livewire\GseTypes\Index as IndexGSEType;
use App\Livewire\GseTypes\Create as CreateGSEType;

use App\Livewire\GseEquipment\Index as IndexGSEEquipment;
use App\Livewire\GseEquipment\Create as CreateGSEEquipment;

use App\Livewire\GseInventoryCategories\Index as IndexGSEInventoryCategories;
use App\Livewire\GseInventoryCategories\CategoryForm as GSEInventoryCategoryForm;

use App\Livewire\GseInventoryItems\Index as IndexGSEInventoryItems;
use App\Livewire\GseInventoryItems\ItemForm as GSEInventoryItemForm;

use App\Livewire\GseInventoryUnits\Index as IndexGSEInventoryUnits;
use App\Livewire\GseInventoryUnits\UnitForm as GSEInventoryUnitForm;

use App\Livewire\GseInventoryTransactions\Index as IndexGSEInventoryTransactions;
use App\Livewire\GseInventoryTransactions\TransactionForm as GSEInventoryTransactionForm;

use App\Livewire\GseInvoices\Index as IndexGSEInvoice;
use App\Livewire\GseInvoices\Create as CreateGSEInvoice;

use App\Livewire\Deposit\Index as IndexDeposit;
use App\Livewire\Deposit\Create as CreateDeposit;

use App\Http\Controllers\Export\FlightExportController;
use App\Http\Controllers\Export\GseInvoiceRecapExportController;
use App\Http\Controllers\GseInvoicePrintController;
use App\Http\Controllers\InvoiceController;

Route::get('/', Login::class)->name('login');
// Route::get('/', Livewire\Home::class)->name('home');

/** AUTH ROUTES */
// Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {

    /* =======================
     | Dashboard
     ======================= */
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    /* =======================
     | Invoice - Regular Data page
     ======================= */
    Route::get('/invoice', IndexInvoice::class)->name('invoice');
    Route::get('/invoice/create', CreateInvoice::class)
        ->name('invoice.create');
    Route::get('/invoice/{id:id}/edit', CreateInvoice::class)
        ->whereNumber('id') // optional safety
        ->name('invoice.edit');  // <-- {airline} matches the type-hint
    Route::get('/invoice/print/{invoice}', [InvoiceController::class, 'print'])
        ->name('invoice.print');

    /* =======================
     | GSE Invoice - GSE Invoice Page
     ======================= */
    Route::get('/invoice/gse', IndexGSEInvoice::class)->name('invoicegse');
    Route::get('/invoice/gse/create', CreateGSEInvoice::class)
        ->name('invoicegse.create');
    Route::get('/invoice/gse/{id:id}/edit', CreateGSEInvoice::class)
        ->whereNumber('id') // optional safety
        ->name('invoicegse.edit');  // <-- {airline} matches the type-hint
    Route::get('/invoice/gse/{invoice}/export-recap', [GseInvoiceRecapExportController::class, 'export'])
        ->whereNumber('invoice')
        ->name('invoicegse.export-recap');
    Route::get('/invoice/gse/{invoice}/print', [GseInvoicePrintController::class, 'print'])
        ->whereNumber('invoice')
        ->name('invoicegse.print');

    /* =======================
     | GSE Rekap - GPU and ATT Invoices
     ======================= */
    Route::get('/gse/rekapgse', IndexGSERecap::class)->name('rekapgse');
    Route::get('/gse/rekapgse/create', CreateGSERecap::class)
        ->name('rekapgse.create');
    Route::get('/gse/rekapgse/{id:id}/edit', CreateGSERecap::class)
        ->whereNumber('id') // optional safety
        ->name('rekapgse.edit');  // <-- {airline} matches the type-hint
    //Route::get('/gse/rekap/print/{rekap}', [InvoiceController::class, 'print']) // print page
      //  ->name('rekapgse.print');

    /* =======================
     | GSE Rates - Rates data based on GSE type and time period
     ======================= */
    Route::get('/gse/rategse', IndexGSERate::class)->name('rategse');
    Route::get('/gse/rategse/create', CreateGSERate::class)
        ->name('rategse.create');
    Route::get('/gse/rategse/{id:id}/edit', CreateGSERate::class)
        ->whereNumber('id') // optional safety
        ->name('rategse.edit');  // <-- {airline} matches the type-hint
    //Route::get('/gse/rekap/print/{rekap}', [InvoiceController::class, 'print']) // print page
      //  ->name('rekapgse.print');

    /* =======================
     | GSE Types
     ======================= */
    Route::get('/gse/types', IndexGSEType::class)->name('gsetype');
    Route::get('/gse/types/create', CreateGSEType::class)
        ->name('gsetype.create');
    Route::get('/gse/types/{id:id}/edit', CreateGSEType::class)
        ->whereNumber('id')
        ->name('gsetype.edit');

    /* =======================
     | GSE Equipment
     ======================= */
    Route::get('/gse/equipment', IndexGSEEquipment::class)->name('gseequipment');
    Route::get('/gse/equipment/create', CreateGSEEquipment::class)
        ->name('gseequipment.create');
    Route::get('/gse/equipment/{id:id}/edit', CreateGSEEquipment::class)
        ->whereNumber('id')
        ->name('gseequipment.edit');

    /* =======================
     | GSE Inventory Categories
     ======================= */
    Route::get('/gse/inventory/categories', IndexGSEInventoryCategories::class)->name('gsecategories');
    Route::get('/gse/inventory/categories/create', GSEInventoryCategoryForm::class)
        ->name('gsecategories.create');
    Route::get('/gse/inventory/categories/{id:id}/edit', GSEInventoryCategoryForm::class)
        ->whereNumber('id')
        ->name('gsecategories.edit');

    /* =======================
     | GSE Inventory Items
     ======================= */
    Route::get('/gse/inventory/items', IndexGSEInventoryItems::class)->name('gseitems');
    Route::get('/gse/inventory/items/create', GSEInventoryItemForm::class)
        ->name('gseitems.create');
    Route::get('/gse/inventory/items/{id:id}/edit', GSEInventoryItemForm::class)
        ->whereNumber('id')
        ->name('gseitems.edit');

    /* =======================
     | GSE Inventory Units
     ======================= */
    Route::get('/gse/inventory/units', IndexGSEInventoryUnits::class)->name('gseunits');
    Route::get('/gse/inventory/units/create', GSEInventoryUnitForm::class)
        ->name('gseunits.create');
    Route::get('/gse/inventory/units/{id:id}/edit', GSEInventoryUnitForm::class)
        ->whereNumber('id')
        ->name('gseunits.edit');

    /* =======================
     | GSE Inventory Transactions
     ======================= */
    Route::get('/gse/inventory/transactions', IndexGSEInventoryTransactions::class)->name('gsetransactions');
    Route::get('/gse/inventory/transactions/create', GSEInventoryTransactionForm::class)
        ->name('gsetransactions.create');
    Route::get('/gse/inventory/transactions/{id:id}/edit', GSEInventoryTransactionForm::class)
        ->whereNumber('id')
        ->name('gsetransactions.edit');

    /* =======================
     | Invoice - Talangan Data page
     ======================= */
    Route::get('/invoice/deposit', IndexDeposit::class)->name('deposit');
    Route::get('/invoice/deposit/create', CreateDeposit::class)
        ->name('deposit.create');
    Route::get('/invoice/deposit/{id:id}/edit', CreateDeposit::class)
        ->whereNumber('id') // optional safety
        ->name('deposit.edit');  // <-- {airline} matches the type-hint
    Route::get('/deposit/print/{deposit}', [CharterDepositController::class, 'print'])
        ->name('deposit.print');

    /* =======================
     | Flight Journal Scheduled, create and edit from scheduled to actual
     ======================= */
    Route::get('/flight-journal', FlightJournal::class)->name('flight-journal');
    Route::get('/flight-journal/create', FlightJournalModify::class)
        ->name('flight-journal.create');
    Route::get('/flight-journal/{id:id}/edit', FlightJournalModify::class)
        ->whereNumber('id') // optional safety
        ->name('flight-journal.edit');  // <-- {airline} matches the type-hint

    /* =======================
     | Flight Journal Actual (CRUD)
     ======================= */
    Route::get('/flight-journal/actual', FlightJournalActual::class)
        ->name('flight-journal.actual');

    /* =======================
     | Flight Scheduling (CRUD)
     ======================= */
    Route::get('/flight-schedule', FlightSchedule::class)->name('flight-schedule');
    Route::get('/flight-schedule/create', FlightScheduleModify::class)
        ->name('flight-schedule.create');
    Route::get('/flight-schedule/{scheduled:id}/edit', FlightScheduleModify::class)
        ->whereNumber('scheduled') // optional safety
        ->name('flight-schedule.edit');  // <-- {airline} matches the type-hint

    /* =======================
     | Flight History Exports
     ======================= */
    Route::get('/flight-history', FlightHistory::class)->name('flight-history');
    Route::get('/exportfh', [FlightExportController::class, 'export'])->name('export-flight-history');
    Route::get('/exportfhpdf', [FlightExportController::class, 'exportPdf'])->name('export-flight-pdf');
    Route::get('/printfh', [FlightExportController::class, 'print'])->name('export-flight-print');

    /* =======================
     | Settings Pages
     ======================= */
    Route::get('/settings', Settings::class)->name('settings.index');
    Route::get('/settings/account', Account::class)->name('settings.account');

    /* =======================
     | Settings Pages - Airlines
     ======================= */
    Route::get('/settings/airline', Airline::class)->name('settings.airline');
    Route::get('/settings/airline/create', AirlineModify::class)
        ->name('settings.airline.create');
    Route::get('/settings/airline/{airline}/edit', AirlineModify::class)
        ->whereNumber('airline') // optional safety
        ->name('settings.airline.edit');  // <-- {airline} matches the type-hint

    /* =======================
     | Settings Pages - Airline Rates
     ======================= */
    Route::get('/settings/airlineRates', AirlineRates::class)->name('settings.airlineRates');
    Route::get('/settings/airlineRates/create', AirlineRatesModify::class)
        ->name('settings.airlineRates.create');
    Route::get('/settings/airlineRates/{airlineRate}/edit', AirlineRatesModify::class)
        ->whereNumber('airlineRates') // optional safety
        ->name('settings.airlineRates.edit');  // <-- {airline} matches the type-hint

    /* =======================
     | Settings Pages - Flight Type
     ======================= */
    Route::get('/settings/flight-type', FlightType::class)->name('settings.flight-type');
    Route::get('/settings/flight-type/create', FlightTypeModify::class)
        ->name('settings.flight-type.create');
    Route::get('/settings/flight-type/{flightType}/edit', FlightTypeModify::class)
        ->whereNumber('typeId') // optional safety
        ->name('settings.flight-type.edit');  // <-- {airline} matches the type-hint

    /* =======================
     | Settings Pages - Aircraft
     ======================= */
    Route::get('/settings/aircraft', Aircraft::class)->name('settings.aircraft');
    Route::get('/settings/aircraft/create', AircraftModify::class)
        ->name('settings.aircraft.create');
    Route::get('/settings/aircraft/{aircraft:id}/edit', AircraftModify::class)
        ->whereNumber('aircraft') // optional safety
        ->name('settings.aircraft.edit');  // <-- {aircraft} matches the type-hint

    /* =======================
     | Settings Pages - Equipment
     ======================= */
    Route::get('/settings/equipment', Equipment::class)->name('settings.equipment');
    Route::get('/settings/equipment/create', EquipmentModify::class)
        ->name('settings.equipment.create');
    Route::get('/settings/equipment/{equipment:id}/edit', EquipmentModify::class)
        ->whereNumber('equipment') // optional safety
        ->name('settings.equipment.edit');  // <-- {equipment} matches the type-hint

    /* =======================
     | Settings Pages - Airport List
     ======================= */
    Route::get('/settings/airport', Airport::class)->name('settings.airport');
    Route::get('/settings/airport/create', AirportModify::class)
        ->name('settings.airport.create');
    Route::get('/settings/airport/{airport:id}/edit', AirportModify::class)
        ->whereNumber('airport') // optional safety
        ->name('settings.airport.edit');  // <-- {airport} matches the type-hint

    /* =======================
     | Settings Pages - Flight Routes
     ======================= */
    Route::get('/settings/airport-route', AirportRoute::class)->name('settings.airport-route');
    Route::get('/settings/airport-route/create', AirportRouteModify::class)
        ->name('settings.airport-route.create');
    Route::get('/settings/airport-route/{route:id}/edit', AirportRouteModify::class)
        ->whereNumber('route') // optional safety
        ->name('settings.airport-route.edit');  // <-- {airport-route} matches the type-hint

    /* =======================
     | Settings Pages End
     ======================= */
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

    /* =======================
     | Admin - Branches
     ======================= */
    Route::get('/admin/branch', Branch::class)->name('admin.branch');
    Route::get('/admin/branch/create', BranchModify::class)
        ->name('admin.branch.create');
    Route::get('/admin/branch/{branch}/edit', BranchModify::class)
        ->whereNumber('branch') // optional safety
        ->name('admin.branch.edit');  // <-- {branch} matches the type-hint
});
