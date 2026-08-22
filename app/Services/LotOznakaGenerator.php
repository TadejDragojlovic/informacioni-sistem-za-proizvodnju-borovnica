<?php

namespace App\Services;

use App\Models\Lot;
use Carbon\CarbonInterface;

class LotOznakaGenerator
{
    /** Generiše sledeću jedinstvenu oznaku lota u formatu BL-GODINA-RB za godinu berbe. */
    public function generisi(CarbonInterface $datumBerbe): string
    {
        $godina = $datumBerbe->year;
        $prefiks = "BL-{$godina}-";

        $poslednjiRedniBroj = Lot::query()
            ->where('oznaka', 'like', $prefiks.'%')
            ->lockForUpdate()
            ->pluck('oznaka')
            ->map(function (string $oznaka) use ($godina): int {
                return preg_match("/^BL-{$godina}-(\\d+)$/", $oznaka, $podudaranje)
                    ? (int) $podudaranje[1]
                    : 0;
            })
            ->max() ?? 0;

        return $prefiks.sprintf('%03d', $poslednjiRedniBroj + 1);
    }
}
