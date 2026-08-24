<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ObjectImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ObjectImageSeeder extends Seeder
{
    private const PALETTE = [
        ['#14b8a6', '#0f766e'],
        ['#6366f1', '#4f46e5'],
        ['#f59e0b', '#d97706'],
        ['#f43f5e', '#e11d48'],
        ['#10b981', '#059669'],
        ['#0ea5e9', '#0284c7'],
        ['#8b5cf6', '#7c3aed'],
        ['#f97316', '#ea580c'],
    ];

    public function run(): void
    {
        $this->clearExisting();

        $items = Item::query()->get();

        foreach ($items as $index => $item) {
            $icon = $item->category?->icon ?? '📦';
            [$from, $to] = self::PALETTE[$index % count(self::PALETTE)];

            $path = $this->downloadPhoto($item)
                ?? $this->writeFallback($item, $icon, $from, $to);

            $item->images()->create([
                'path' => $path,
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Remove previously generated demo images so re-seeding refreshes them.
     */
    private function clearExisting(): void
    {
        ObjectImage::query()->delete();
        Storage::disk('public')->deleteDirectory('objects');
    }

    /**
     * Download a real photo from Lorem Picsum (Unsplash-sourced) seeded by the object slug.
     */
    private function downloadPhoto(Item $item): ?string
    {
        $url = 'https://picsum.photos/seed/'.urlencode($item->slug).'/800/600';

        try {
            // withoutVerifying() is used because the local PHP build may lack a CA bundle;
            // the placeholder images are public and non-sensitive.
            $response = Http::withoutVerifying()->timeout(20)->get($url);

            if ($response->successful()) {
                $path = 'objects/'.$item->slug.'.jpg';
                Storage::disk('public')->put($path, $response->body());

                return $path;
            }
        } catch (\Throwable) {
            // Fall through to the generated placeholder.
        }

        return null;
    }

    private function writeFallback(Item $item, string $icon, string $from, string $to): string
    {
        $path = 'objects/'.$item->slug.'.svg';
        Storage::disk('public')->put($path, $this->renderSvg($item->title, $icon, $from, $to));

        return $path;
    }

    private function renderSvg(string $title, string $icon, string $from, string $to): string
    {
        $title = e($title);

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
            <defs>
                <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="{$from}"/>
                    <stop offset="100%" stop-color="{$to}"/>
                </linearGradient>
            </defs>
            <rect width="800" height="600" fill="url(#bg)"/>
            <circle cx="400" cy="280" r="150" fill="#ffffff" fill-opacity="0.15"/>
            <text x="400" y="280" font-size="170" text-anchor="middle" dominant-baseline="central">{$icon}</text>
            <text x="400" y="540" font-size="36" text-anchor="middle" fill="#ffffff" font-family="Arial, sans-serif" font-weight="700">{$title}</text>
        </svg>
        SVG;
    }
}
