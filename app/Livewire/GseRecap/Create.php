<?php

namespace App\Livewire\GseRecap;

use App\Livewire\Forms\GseRecapForm;
use App\Models\Airline;
use App\Models\Branch;
use App\Models\Equipment;
use App\Models\GseRecap;
use App\Models\GseType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public GseRecapForm $form;

    public $branches;
    public $airlines;
    public $equipments;
    public $gseTypes;

    public ?string $selectedGSEType = '';
    public ?string $currentDetailType = null;
    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        $this->branches = Branch::query()->orderBy('name')->get(['id', 'name']);
        $this->airlines = Airline::query()->orderBy('name')->get(['id', 'name']);
        $this->equipments = Equipment::query()->orderBy('registration')->get(['id', 'registration', 'airline_id']);
        $this->gseTypes = GseType::query()->orderBy('service_name')->get(['id', 'service_name']);
        $this->form->service_date = now('Asia/Jakarta')->toDateString();

        if ($id !== null) {
            $this->isEdit = true;
            $recap = GseRecap::with(['gpuDetail', 'pushbackDetail'])->findOrFail($id);
            $this->form->setRecap($recap);
            $this->selectedGSEType = $this->form->gse_type_id;
            $this->currentDetailType = $this->form->detailType();
            return;
        }

        $this->form->branch_id = (string) (Auth::user()?->branch_id ?? '');
        $this->currentDetailType = null;
    }

    public function updated($field): void
    {
        $this->form->validateOnly($field, $this->form->rules());
    }

    public function updatedFormGseTypeId($value): void
    {
        $this->updatedGSEType($value);
    }

    public function updatedGSEType($value): void
    {
        $this->selectedGSEType = (string) $value;
        $this->form->gse_type_id = (string) $value;
        $this->form->resetDetailFields();
        $this->currentDetailType = $this->form->detailType();
    }

    public function updatedFormEquipmentId($value): void
    {
        if (! $value) {
            $this->form->airline_id = '';
            return;
        }

        $equipment = Equipment::query()->find($value);
        $this->form->airline_id = $equipment ? (string) $equipment->airline_id : '';
    }

    public function saveChanges()
    {
        $recap = $this->form->record
            ? $this->form->update()
            : $this->form->store();

        session()->flash('notify', [
            'content' => $recap->er_number . ' berhasil disave',
            'type' => 'success',
        ]);

        return $this->redirectRoute('rekapgse', navigate: true);
    }

    public function delete()
    {
        $this->form->delete();

        session()->flash('notify', [
            'content' => 'GSE recap berhasil dihapus!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('rekapgse', navigate: true);
    }

    public function render()
    {
        return view('livewire.gse-recap.create');
    }
}
