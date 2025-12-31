<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinansijeController extends Controller
{
    public function index()
    {
        return "[ADMIN] Stranica za finansijski pregled";
    }

    public function generate()
    {
        return "[ADMIN] Generisanje izvestaja...";
    }
}
