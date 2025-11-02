<?php

namespace App\Livewire\Traits;

use Illuminate\Database\QueryException;

trait HandlesSafeActions
{
    protected function safeAction(callable $callback, string $successMessage): void
    {
        try {
            $callback();

            session()->flash('notify', [
                'content' => $successMessage,
                'type' => 'success',
            ]);
        } catch (QueryException $e) {
            $message = $e->getCode() === '23000'
                ? 'Gagal melakukan aksi ini karena terhubung dengan record lain'
                : 'Database error: ' . $e->getMessage();

            session()->flash('notify', [
                'content' => $message,
                'type' => 'error',
            ]);
        } catch (\Throwable $e) {
            session()->flash('notify', [
                'content' => 'Unexpected error: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }
}
