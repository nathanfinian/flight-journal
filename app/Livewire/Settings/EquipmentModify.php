<?php

namespace App\Livewire\Settings;

use App\Models\Airline;
use Livewire\Component;
use App\Models\Aircraft;
use App\Models\Equipment;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EquipmentModify extends Component
{
    public ?Equipment $record = null;
    public $aircrafts;
    public $airlines;

    public ?int $equipmentId = null;
    public string $airline_id='';
    public string $aircraft_id='';
    public string $registration = '';
    public string $status = 'ACTIVE';

    public bool $isEdit = false;

    public function mount(?int $equipment = null)
    {
        $this->equipmentId = $equipment;
        $this->aircrafts = Aircraft::orderBy('type_name')->get();
        $this->airlines = Airline::orderBy('name')->get();

        if ($equipment) {
            $this->record = Equipment::findOrFail($equipment);
            $this->isEdit = true;

            $this->aircraft_id = $this->record->aircraft_id;
            $this->airline_id = $this->record->airline_id;
            $this->registration = $this->record->registration;
            $this->status = $this->record->status;
        }
    }

    public function updatedRegis($value): void
    {
        $this->registration = $value ? strtoupper(trim($value)) : null;
    }

    public function saveChanges()
    {
        $validated = $this->validate([
            'aircraft_id' => ['required', 'exists:aircrafts,id'],
            'airline_id' => ['required', 'exists:airlines,id'],
            'registration' => ['required', 'max:10', Rule::unique('equipments', 'registration')->ignore($this->record?->id)],
            'status' => ['required', 'in:ACTIVE,RETIRED'],
        ]);

        $userId = Auth::id();
        if ($this->equipmentId) {
            $validated['updated_by'] = $userId;
        } else {
            $validated['created_by'] = $userId;
            $validated['updated_by'] = $userId;
        }

        Equipment::updateOrCreate(
            ['id' => $this->record?->id],
            $validated
        );

        session()->flash('notify', [
            'content' => 'Equipment data saved successfully',
            'type' => 'success'
        ]);
        return redirect()->route('settings.equipment');
    }

    public function delete()
    {
        if ($this->record) {
            $this->record->delete();

            session()->flash('notify', [
                'content' => 'Equipment data deleted successfully',
                'type' => 'success'
            ]);
            return redirect()->route('settings.equipment');
        }
    }

    public function render()
    {
        return view('livewire.settings.equipment-modify');
    }
}
