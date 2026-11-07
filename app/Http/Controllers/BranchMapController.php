<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Contracts\View\View;

class BranchMapController extends Controller
{
    public function index(): View
    {
        $branches = Branch::select(
            'id', 'nama', 'alamat', 'telepon', 'latitude', 'longitude'
        )->get();

        return view('peta', compact('branches'));
    }
}
