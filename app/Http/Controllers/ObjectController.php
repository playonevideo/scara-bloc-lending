<?php

namespace App\Http\Controllers;

use App\Enums\ObjectCondition;
use App\Enums\ObjectStatus;
use App\Http\Requests\StoreObjectRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\ObjectImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ObjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::query()->with(['category', 'images', 'owner.apartment.floor']);

        if ($request->boolean('mine')) {
            $query->where('owner_id', $request->user()->id);
        } else {
            $query->published();
        }

        if ($request->filled('owner')) {
            $query->where('owner_id', $request->integer('owner'));
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('floor')) {
            $query->whereHas('owner.apartment.floor', fn ($q) => $q->where('number', $request->integer('floor')));
        }

        $query = match ($request->string('sort', 'newest')) {
            'popular' => $query->withCount('loans')->orderByDesc('loans_count'),
            'rating' => $query->orderByRaw('(SELECT AVG(rating) FROM reviews WHERE reviews.reviewee_id = objects.owner_id) DESC'),
            default => $query->latest(),
        };

        $objects = $query->paginate(12)->withQueryString();

        return view('objects.index', [
            'objects' => $objects,
            'categories' => Category::orderBy('sort_order')->get(),
            'floors' => range(0, 10),
            'conditions' => ObjectCondition::options(),
            'filters' => $request->only(['q', 'category', 'status', 'floor', 'sort', 'mine', 'owner']),
        ]);
    }

    public function create(): View
    {
        return view('objects.create', [
            'categories' => Category::orderBy('sort_order')->get(),
            'conditions' => ObjectCondition::options(),
        ]);
    }

    public function store(StoreObjectRequest $request): RedirectResponse
    {
        $object = Item::create([
            ...$request->safe()->except('images'),
            'owner_id' => $request->user()->id,
            'slug' => $this->uniqueSlug($request->string('title')),
            'status' => ObjectStatus::Available,
            'requires_personal_handover' => $request->boolean('requires_personal_handover'),
            'can_leave_at_door' => $request->boolean('can_leave_at_door'),
        ]);

        $this->storeImages($request, $object);

        return redirect()->route('objects.show', $object)
            ->with('status', 'Obiectul a fost publicat cu succes.');
    }

    public function show(Item $object): View
    {
        $this->authorize('view', $object);

        $object->load(['category', 'images', 'owner.apartment.floor', 'loans' => fn ($q) => $q->active()]);

        return view('objects.show', [
            'object' => $object,
            'isFavorite' => $object->favorites()->where('user_id', auth()->id())->exists(),
            'canRequest' => $object->isAvailable() && $object->owner_id !== auth()->id(),
        ]);
    }

    public function edit(Item $object): View
    {
        $this->authorize('update', $object);

        return view('objects.edit', [
            'object' => $object,
            'categories' => Category::orderBy('sort_order')->get(),
            'conditions' => ObjectCondition::options(),
        ]);
    }

    public function update(StoreObjectRequest $request, Item $object): RedirectResponse
    {
        $this->authorize('update', $object);

        $object->update([
            ...$request->safe()->except('images'),
            'requires_personal_handover' => $request->boolean('requires_personal_handover'),
            'can_leave_at_door' => $request->boolean('can_leave_at_door'),
        ]);

        if ($request->filled('remove_images')) {
            foreach ($request->input('remove_images') as $imageId) {
                $image = $object->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        $this->storeImages($request, $object);

        return redirect()->route('objects.show', $object)
            ->with('status', 'Obiectul a fost actualizat.');
    }

    public function destroy(Item $object): RedirectResponse
    {
        $this->authorize('delete', $object);

        foreach ($object->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $object->delete();

        return redirect()->route('objects.index')->with('status', 'Obiectul a fost șters.');
    }

    public function toggleFavorite(Item $object): RedirectResponse
    {
        $user = auth()->user();

        $favorite = $object->favorites()->where('user_id', $user->id)->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            $object->favorites()->create(['user_id' => $user->id]);
        }

        return back();
    }

    private function storeImages(StoreObjectRequest $request, Item $object): void
    {
        foreach ($request->file('images', []) as $index => $file) {
            $path = $file->store('objects', 'public');

            ObjectImage::create([
                'object_id' => $object->id,
                'path' => $path,
                'sort_order' => $object->images()->count() + $index,
            ]);
        }
    }

    private function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 2;

        while (Item::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $original.'-'.$i++;
        }

        return $slug;
    }
}
