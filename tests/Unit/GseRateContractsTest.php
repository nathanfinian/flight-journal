<?php

use App\Livewire\GseInvoices\Create as InvoiceCreate;
use App\Livewire\GseRates\Create as RateCreate;
use App\Models\GseRecap;
use App\Models\GseTypeRate;
use App\Models\Invoice_gse;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    // Exercise the contract migration on isolated SQLite; older migrations use MySQL-only SQL.
    config(['database.default' => 'gse_contract_test', 'database.connections.gse_contract_test' => [
        'driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true,
    ]]);
    DB::purge('gse_contract_test');

    foreach (['airlines', 'branches', 'gse_types'] as $name) {
        Schema::create($name, function (Blueprint $table) use ($name) {
            $table->id();
            $table->string($name === 'gse_types' ? 'type_name' : 'name');
            $table->string('status')->default('ACTIVE');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }
    Schema::create('equipments', function (Blueprint $table) {
        $table->id();
        $table->string('registration');
    });
    Schema::create('gse_recaps', function (Blueprint $table) {
        $table->id();
        foreach (['gse_type_id', 'airline_id', 'branch_id', 'equipment_id'] as $field) {
            $table->unsignedBigInteger($field);
        }
        $table->date('service_date');
        $table->string('er_number');
        $table->string('flight_number');
        $table->string('operator_name');
        $table->timestamps();
        $table->softDeletes();
    });
    Schema::create('gse_gpu_details', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('gse_recap_id');
        $table->time('start_time');
        $table->time('end_time');
        $table->timestamps();
    });
    Schema::create('gse_pushback_details', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('gse_recap_id');
        $table->string('start_ps');
        $table->string('end_ps');
    });
    Schema::create('gse_invoices', function (Blueprint $table) {
        $table->id();
        foreach (['gse_type_id', 'airline_id', 'branch_id'] as $field) {
            $table->unsignedBigInteger($field);
        }
        foreach (['invoice_number', 'toCompany', 'toWhom', 'toTitle', 'signer_name'] as $field) {
            $table->string($field)->nullable();
        }
        $table->date('dateFrom');
        $table->date('dateTo');
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    (require database_path('migrations/2026_03_12_090000_create_gse_type_rates_table.php'))->up();
    (require database_path('migrations/2026_10_26_000000_add_airline_and_branch_to_gse_type_rates_table.php'))->up();
    (require database_path('migrations/2026_03_12_090100_create_gse_invoice_recap_table.php'))->up();

    DB::table('airlines')->insert([['id' => 1, 'name' => 'Airline A'], ['id' => 2, 'name' => 'Airline B']]);
    DB::table('branches')->insert([['id' => 1, 'name' => 'Branch A'], ['id' => 2, 'name' => 'Branch B']]);
    DB::table('gse_types')->insert([['id' => 1, 'type_name' => 'GPU'], ['id' => 2, 'type_name' => 'ATT']]);
    DB::table('equipments')->insert(['id' => 1, 'registration' => 'PK-TEST']);

    $this->contract = [
        'gse_type_id' => 1, 'airline_id' => 1, 'branch_id' => 1,
        'effective_from' => '2026-08-01', 'effective_to' => '2026-08-31',
        'charge_type' => 'HOURLY', 'service_rate' => 100,
    ];
    $this->recap = GseRecap::create([
        'gse_type_id' => 1, 'airline_id' => 1, 'branch_id' => 1, 'equipment_id' => 1,
        'service_date' => '2026-08-15', 'er_number' => 'ER-001',
        'flight_number' => 'GA100', 'operator_name' => 'Operator A',
    ]);
    $this->recap->gpuDetail()->create(['start_time' => '23:00', 'end_time' => '01:00']);
});

test('contract migration retains legacy rates without guessing their airline or branch', function () {
    $migration = require database_path('migrations/2026_10_26_000000_add_airline_and_branch_to_gse_type_rates_table.php');
    $migration->down();
    $legacy = $this->contract;
    unset($legacy['airline_id'], $legacy['branch_id']);
    $rate = GseTypeRate::create($legacy);
    $migration->up();

    expect($rate->fresh()->airline_id)->toBeNull()
        ->and($rate->fresh()->branch_id)->toBeNull()
        ->and($rate->fresh()->service_rate)->toBe('100.00')
        ->and(GseTypeRate::forRecap($this->recap)->exists())->toBeFalse();
});

test('rates match the exact airline branch GSE type and inclusive service date', function () {
    $matching = GseTypeRate::create($this->contract);
    foreach ([['airline_id' => 2], ['branch_id' => 2], ['gse_type_id' => 2],
        ['effective_from' => '2026-09-01', 'effective_to' => null], ['airline_id' => null, 'branch_id' => null]] as $override) {
        GseTypeRate::create(array_replace($this->contract, $override));
    }
    foreach (['2026-08-01', '2026-08-15', '2026-08-31'] as $date) {
        $this->recap->service_date = $date;
        expect(GseTypeRate::forRecap($this->recap)->pluck('id')->all())->toBe([$matching->id]);
    }
});

test('overlap detection includes open contracts and shared boundary dates', function () {
    GseTypeRate::create($this->contract);
    foreach ([
        ['effective_from' => '2026-08-31', 'effective_to' => null, 'overlap' => true],
        ['effective_from' => '2026-07-01', 'effective_to' => '2026-08-01', 'overlap' => true],
        ['effective_from' => '2026-08-15', 'effective_to' => '2026-08-20', 'overlap' => true],
        ['effective_from' => '2026-09-01', 'effective_to' => null, 'overlap' => false],
        ['effective_from' => '2026-07-01', 'effective_to' => '2026-07-31', 'overlap' => false],
    ] as $case) {
        $expected = $case['overlap'];
        unset($case['overlap']);
        expect(GseTypeRate::overlappingContract(array_replace($this->contract, $case))->exists())->toBe($expected);
    }
    GseTypeRate::query()->update(['effective_to' => null]);
    expect(GseTypeRate::overlappingContract(array_replace($this->contract, ['effective_from' => '2027-01-01', 'effective_to' => null]))->exists())->toBeTrue();
});

test('rate form rejects overlap but accepts a separate airline contract', function () {
    GseTypeRate::create($this->contract);
    $form = Livewire::test(RateCreate::class)
        ->set('gse_type_id', '1')->set('airline_id', '1')->set('branch_id', '1')
        ->set('effective_from', '2026-08-15')->set('effective_to', '2026-09-30')
        ->set('charge_type', 'HOURLY')->set('service_rate', '250,50');
    $form->call('saveChanges')->assertHasErrors(['effective_from']);
    $form->set('airline_id', '2')->assertHasNoErrors(['effective_from'])
        ->call('saveChanges')->assertHasNoErrors()->assertRedirect(route('rategse'));
    expect(GseTypeRate::where('airline_id', 2)->first()->service_rate)->toBe('250.50');
});

test('editing a rate excludes itself from overlap validation and preserves cents', function () {
    $rate = GseTypeRate::create(array_replace($this->contract, ['service_rate' => 100.25]));
    Livewire::test(RateCreate::class, ['id' => $rate->id])
        ->assertSet('service_rate', '100,25')
        ->set('effective_to', '2026-08-30')->call('saveChanges')->assertHasNoErrors();
    expect($rate->fresh()->service_rate)->toBe('100.25');
});

test('invoice selection shows unpriced recaps and excludes them from automatic selection', function () {
    GseTypeRate::create(array_replace($this->contract, ['airline_id' => 2]));
    Livewire::test(InvoiceCreate::class)
        ->set('form.gse_type_id', '1')->set('form.branch_id', '1')->set('form.airline_id', '1')
        ->set('form.dateFrom', '2026-08-01')->set('form.dateTo', '2026-08-31')
        ->call('loadRecaps')->assertHasNoErrors()
        ->assertCount('availableRecaps', 1)->assertSet('availableRecaps.0.has_rate', false)
        ->assertSet('selectedRecapIds', [])->assertSee('Rate belum tersedia');
});

test('invoice selection refuses ambiguous overlapping contracts', function () {
    GseTypeRate::create($this->contract);
    GseTypeRate::create(array_replace($this->contract, ['service_rate' => 200]));
    Livewire::test(InvoiceCreate::class)
        ->set('form.gse_type_id', '1')->set('form.branch_id', '1')->set('form.airline_id', '1')
        ->set('form.dateFrom', '2026-08-01')->set('form.dateTo', '2026-08-31')
        ->call('loadRecaps')->assertSet('availableRecaps.0.has_rate', false)
        ->assertSet('selectedRecapIds', []);
});

test('new invoices use the matching contract and calculate overnight usage on the server', function () {
    $rate = GseTypeRate::create($this->contract);
    GseTypeRate::create(array_replace($this->contract, ['airline_id' => 2, 'service_rate' => 999]));
    $id = $this->recap->id;
    Livewire::test(InvoiceCreate::class)
        ->set('form.gse_type_id', '1')->set('form.branch_id', '1')->set('form.airline_id', '1')
        ->set('form.dateFrom', '2026-08-01')->set('form.dateTo', '2026-08-31')
        ->set('form.toWhom', 'Finance')->set('form.toTitle', 'Manager')
        ->set('form.toCompany', 'Airline A')->set('form.signer_name', 'Director')
        ->call('loadRecaps')->assertSet("pivotRows.{$id}.quantity", 2.0)
        ->set("pivotRows.{$id}.amount", 0.01)
        ->call('saveChanges')->assertHasNoErrors();

    $line = DB::table('gse_invoice_recap')->first();
    expect((int) $line->gse_type_rate_id)->toBe($rate->id)
        ->and((float) $line->service_rate)->toBe(100.0)
        ->and((float) $line->quantity)->toBe(2.0)
        ->and((float) $line->amount)->toBe(200.0);
});

test('combined GPU and ATT invoices resolve each service contract separately', function () {
    GseTypeRate::create($this->contract);
    GseTypeRate::create(array_replace($this->contract, [
        'gse_type_id' => 2, 'charge_type' => 'PER_HANDLING', 'service_rate' => 50,
    ]));
    $att = GseRecap::create(array_replace($this->recap->only([
        'airline_id', 'branch_id', 'equipment_id', 'service_date', 'flight_number', 'operator_name',
    ]), ['gse_type_id' => 2, 'er_number' => 'ER-002']));
    $gpuId = $this->recap->id;
    Livewire::test(InvoiceCreate::class)
        ->set('form.gse_type_id', 'gpu_att')->set('form.branch_id', '1')->set('form.airline_id', '1')
        ->set('form.dateFrom', '2026-08-01')->set('form.dateTo', '2026-08-31')
        ->call('loadRecaps')->assertHasNoErrors()->assertCount('selectedRecapIds', 2)
        ->assertSet("pivotRows.{$gpuId}.amount", 200.0)
        ->assertSet("pivotRows.{$att->id}.amount", 50.0);
});

test('saved invoice snapshots remain editable without an assigned legacy rate', function () {
    $rate = GseTypeRate::create(array_replace($this->contract, ['airline_id' => null, 'branch_id' => null, 'service_rate' => 999]));
    $invoice = Invoice_gse::create([
        'gse_type_id' => 1, 'branch_id' => 1, 'airline_id' => 1,
        'invoice_number' => 'INV-HISTORY', 'dateFrom' => '2026-08-01', 'dateTo' => '2026-08-31',
        'toWhom' => 'Finance', 'toTitle' => 'Manager', 'toCompany' => 'Airline A', 'signer_name' => 'Director',
    ]);
    $invoice->recaps()->attach($this->recap->id, [
        'gse_type_rate_id' => $rate->id, 'charge_type' => 'HOURLY',
        'service_rate' => 75, 'quantity' => 1, 'amount' => 75,
    ]);
    $id = $this->recap->id;
    Livewire::test(InvoiceCreate::class, ['id' => $invoice->id])
        ->call('loadRecaps')->assertSet("pivotRows.{$id}.service_rate", 75.0)
        ->assertSet('availableRecaps.0.has_rate', true)
        ->call('saveChanges')->assertHasNoErrors();

    expect((float) $invoice->fresh()->recaps->first()->pivot->amount)->toBe(75.0);
    $rateForm = Livewire::test(RateCreate::class, ['id' => $rate->id])
        ->set('airline_id', '1')->set('branch_id', '1')->set('service_rate', '500,00')
        ->call('saveChanges')->assertHasNoErrors();

    expect($rate->fresh()->service_rate)->toBe('500.00');
    $rateForm->set('airline_id', '2')->call('saveChanges')->assertHasErrors(['airline_id']);
    $rateForm->set('airline_id', '1')->assertHasNoErrors(['airline_id'])
        ->set('service_rate', '600,00')->call('saveChanges')->assertHasNoErrors();

    Livewire::test(InvoiceCreate::class, ['id' => $invoice->id])
        ->call('loadRecaps')->assertSet("pivotRows.{$id}.service_rate", 75.0)
        ->call('saveChanges')->assertHasNoErrors();
    expect((float) $invoice->fresh()->recaps->first()->pivot->amount)->toBe(75.0);

    Livewire::test(InvoiceCreate::class)
        ->set('form.gse_type_id', '1')->set('form.branch_id', '1')->set('form.airline_id', '1')
        ->set('form.dateFrom', '2026-08-01')->set('form.dateTo', '2026-08-31')
        ->call('loadRecaps')->assertSet("pivotRows.{$id}.service_rate", 600.0);
});

test('a changed contract must be reviewed again before a new invoice can be saved', function () {
    $rate = GseTypeRate::create($this->contract);
    $form = Livewire::test(InvoiceCreate::class)
        ->set('form.gse_type_id', '1')->set('form.branch_id', '1')->set('form.airline_id', '1')
        ->set('form.dateFrom', '2026-08-01')->set('form.dateTo', '2026-08-31')
        ->set('form.toWhom', 'Finance')->set('form.toTitle', 'Manager')
        ->set('form.toCompany', 'Airline A')->set('form.signer_name', 'Director')
        ->call('loadRecaps');

    $rate->update(['service_rate' => 200]);
    $form->call('saveChanges')->assertHasErrors(['selectedRecapIds']);
    expect(Invoice_gse::count())->toBe(0);
});
