<?php

namespace App\Livewire\GseTypes;

use App\Models\GseType;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?int $gseTypeId = null;
    public string $type_name = '';
    public string $description = '';
    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            return;
        }

        $gseType = GseType::query()->findOrFail($id);

        $this->isEdit = true;
        $this->gseTypeId = $gseType->getKey();
        $this->type_name = (string) $gseType->type_name;
        $this->description = (string) $gseType->description;
    }

    protected function rules(): array
    {
        return [
            'type_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('gse_types', 'type_name')->ignore($this->gseTypeId),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function saveChanges()
    {
        $payload = $this->validate();
        $payload['description'] = $payload['description'] ?: null;

        GseType::query()->updateOrCreate(
            ['id' => $this->gseTypeId],
            $payload
        );

        session()->flash('notify', [
            'content' => 'Tipe GSE berhasil disimpan!',
            'type' => 'success',
        ]);

        return $this->redirectRoute('gsetype', navigate: true);
    }

    public function delete()
    {
        $row = GseType::query()->find($this->gseTypeId);
        $name = $row?->type_name ?? 'Unknown';

        if (! $row) {
            session()->flash('notify', [
                'content' => 'Tipe GSE tidak ditemukan',
                'type' => 'error',
            ]);

            return;
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => 'Tipe GSE ' . $name . ' berhasil dihapus!',
                'type' => 'success',
            ]);

            return $this->redirectRoute('gsetype', navigate: true);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Tipe GSE ini masih dipakai dan tidak dapat dihapus.',
                    'type' => 'warning',
                ]);

                return;
            }

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.gse-types.create');
    }
}
