<?php

namespace App\Livewire\Settings;

use App\Models\Branch;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class BranchModify extends Component
{
    // Route param (e.g. /settings/branch/{branch}/edit)
    public ?int $branchId = null;

    // Form fields
    public string  $name       = '';
    public string  $address    = '';
    public string  $phone_number   = '';
    public string  $email      = '';
    public string  $status     = 'ACTIVE'; // ACTIVE | INACTIVE these selects are automatically selected when passed with livewire

    public function mount(?int $branch = null): void
    {
        if ($branch) {
            $row = Branch::find($branch);
            if (!$row) {
                session()->flash('notify', [
                    'content' => 'Branch tidak ditemukan!',
                    'type' => 'error'
                ]);
                return;
            }

            $this->branchId   = $row->getKey();
            $this->name       = (string) $row->name;
            $this->address    = (string) $row->address;
            $this->phone_number   = (string) $row->phone_number;
            $this->email      = (string) $row->email;
            $this->status     = $row->status ?: 'ACTIVE';
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'address'      => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email'        => ['required', 'email:rfc,dns', 'max:120'],
            'status'   => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    public function saveChanges()
    {
        $data = $this->validate();

        $branch = Branch::updateOrCreate(
            ['id' => $this->branchId],
            $data
        );

        $this->branchId = $branch->id;

        session()->flash('notify', [
            'content' => 'Cabang berhasil disimpan!',
            'type' => 'success'
        ]);

        return $this->redirectRoute('settings.branch', navigate: true);
    }

    public function delete()
    {
        $row = Branch::find($this->branchId);
        $name = 'Cabang ' . $row?->name ?? 'Unknown';

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Cabang tidak ditemukan',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' berhasil dihapus!',
                'type' => 'success'
            ]);
            $this->redirectRoute('settings.branch', navigate: true);
        } catch (QueryException $e) {
            // 23000 => integrity constraint violation (FK in use, etc.)
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Cabang ini dipakai catatan penerbangan dan tidak dapat dihapus.',
                    'type' => 'warning'
                ]);
                return;
            }
            // Re-throw unexpected errors for visibility in logs
            throw $e;
        }
    }

    /** For headings/buttons in the view */
    public function getIsEditProperty(): bool
    {
        return (bool) $this->branchId;
    }
    
    public function render()
    {
        return view('livewire.settings.branch-modify');
    }
}
