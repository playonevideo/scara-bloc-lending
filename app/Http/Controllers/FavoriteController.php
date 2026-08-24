<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()
            ->favorites()
            ->with('object.category')
            ->latest()
            ->get()
            ->pluck('object');

        return view('favorites.index', ['objects' => $favorites]);
    }
}
