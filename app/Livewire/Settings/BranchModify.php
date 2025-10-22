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

            $this->branchId  = $row->getKey();
            $this->name       = (string) $row->name;
            $this->status     = $row->status ?: 'ACTIVE';
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'status'   => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    public function saveChanges()
    {
        $data = $this->validate();

        // Stamp who did it
        $userId = Auth::id();
        if ($this->isEdit) {
            // editing existing row → only updated_by
            $data['updated_by'] = $userId;
        } else {
            // creating new row → set both created_by & updated_by
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;
        }

        $branch = Branch::updateOrCreate(
            ['id' => $this->branchId],
            $data
        );

        $this->branchId = $branch->id;

        session()->flash('notify', [
            'content' => 'Branch saved successfully!',
            'type' => 'success'
        ]);

        return $this->redirectRoute('settings.branch', navigate: true);
    }

    public function delete()
    {
        $row = Branch::find($this->branchId);
        $name = $row?->name ?? 'Unknown';

        if (!$row) {
            session()->flash('notify', [
                'content' => 'Branch not found',
                'type' => 'error'
            ]);
        }

        try {
            $row->delete();

            session()->flash('notify', [
                'content' => $name . ' deleted successfully!',
                'type' => 'success'
            ]);
            $this->redirectRoute('settings.branch', navigate: true);
        } catch (QueryException $e) {
            // 23000 => integrity constraint violation (FK in use, etc.)
            if ($e->getCode() === '23000') {
                session()->flash('notify', [
                    'content' => 'Branch ini ada di catatan penerbangan dan tidak dapat dihapus.',
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
