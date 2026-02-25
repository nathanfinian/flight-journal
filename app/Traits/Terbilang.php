<?php

namespace App\Traits;

trait Terbilang
{
    public function terbilang($nilai): string
    {
        $nilai = abs((int) $nilai);

        $huruf = [
            "",
            "Satu",
            "Dua",
            "Tiga",
            "Empat",
            "Lima",
            "Enam",
            "Tujuh",
            "Delapan",
            "Sembilan",
            "Sepuluh",
            "Sebelas"
        ];

        if ($nilai < 12) {
            return $huruf[$nilai];
        }

        if ($nilai < 20) {
            return $this->terbilang($nilai - 10) . " Belas";
        }

        if ($nilai < 100) {
            return $this->terbilang(intval($nilai / 10)) . " Puluh " .
                $this->terbilang($nilai % 10);
        }

        if ($nilai < 200) {
            return "Seratus " . $this->terbilang($nilai - 100);
        }

        if ($nilai < 1000) {
            return $this->terbilang(intval($nilai / 100)) . " Ratus " .
                $this->terbilang($nilai % 100);
        }

        if ($nilai < 2000) {
            return "Seribu " . $this->terbilang($nilai - 1000);
        }

        if ($nilai < 1000000) {
            return $this->terbilang(intval($nilai / 1000)) . " Ribu " .
                $this->terbilang($nilai % 1000);
        }

        if ($nilai < 1000000000) {
            return $this->terbilang(intval($nilai / 1000000)) . " Juta " .
                $this->terbilang($nilai % 1000000);
        }

        if ($nilai < 1000000000000) {
            return $this->terbilang(intval($nilai / 1000000000)) . " Milyar " .
                $this->terbilang($nilai % 1000000000);
        }

        return "";
    }

    public function formatTerbilang($nilai): string
    {
        return trim(preg_replace(
            '/\s+/',
            ' ',
            $this->terbilang($nilai)
        ));
    }
}
