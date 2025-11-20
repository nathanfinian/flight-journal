<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

trait TimeValidation
{
    public function checkTimeFormat(?string $time): void
    {
        if ($time === null || $time === '') {
            return;
        }

        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            throw ValidationException::withMessages([
                'time_format' => 'Invalid format waktu: harus 24-jam HH:MM (00:00–23:59)',
            ]);
        }
    }

    public function formatTime(?string $time): ?string
    {
        if (!$time) return null;

        try {
            return Carbon::createFromFormat('H:i', $time)->format('H:i:s');
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'time_format' => 'Invalid format waktu: harus 24-jam HH:MM (00:00–23:59)',
            ]);
        }
    }
}
