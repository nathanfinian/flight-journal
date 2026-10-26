<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;

final class GseDuration
{
    public static function seconds(?string $start, ?string $finish): ?int
    {
        $start = self::parseTime($start);
        $finish = self::parseTime($finish);
        if ($start === null || $finish === null) {
            return null;
        }

        if ($finish < $start) {
            $finish = $finish->modify('+1 day');
        }

        return $finish->getTimestamp() - $start->getTimestamp();
    }

    public static function billableHours(?string $start, ?string $finish): float
    {
        return max(1.0, (float) ceil((self::seconds($start, $finish) ?? 0) / 3600));
    }

    public static function formatTime(?string $value): ?string
    {
        return self::parseTime($value)?->format('H:i');
    }

    private static function parseTime(?string $value): ?DateTimeImmutable
    {
        $value = str_replace('.', ':', trim($value ?? ''));
        if (! preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $value)) {
            return null;
        }

        $format = substr_count($value, ':') === 2 ? '!G:i:s' : '!G:i';
        $time = DateTimeImmutable::createFromFormat($format, $value, new DateTimeZone('Asia/Jakarta'));
        $errors = DateTimeImmutable::getLastErrors();

        return $time !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
            ? $time : null;
    }
}
