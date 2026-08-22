<?php

namespace App\Http\Controllers;

use DomainException;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

abstract class Controller
{
    protected function izvrsiServisnuOperaciju(
        callable $operacija,
        string $poruka,
        ?string $odredisnaRuta = null
    ): RedirectResponse {
        try {
            $operacija();
        } catch (DomainException|InvalidArgumentException $exception) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['operacija' => $exception->getMessage()]);
        }

        $redirect = $odredisnaRuta === null
            ? redirect()->back()
            : redirect()->route($odredisnaRuta);

        return $redirect->with('success', $poruka);
    }
}
