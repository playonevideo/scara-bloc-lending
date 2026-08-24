<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ResidentProfileController extends Controller
{
    public function show(User $resident): View
    {
        $resident->load('apartment.floor.staircase.building');

        $objects = $resident->objects()
            ->published()
            ->with(['category', 'images', 'owner'])
            ->latest()
            ->paginate(12);

        return view('residents.show', [
            'resident' => $resident,
            'objects' => $objects,
            'averageRating' => $resident->reviewsReceived()->avg('rating'),
            'reviewsCount' => $resident->reviewsReceived()->count(),
        ]);
    }
}
