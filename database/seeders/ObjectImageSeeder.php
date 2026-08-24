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

    private const KEYWORDS = [
        'Bormașină Bosch' => 'power drill',
        'Scară telescopică' => 'ladder',
        'Aspirator profesional' => 'vacuum cleaner',
        'Mașină de găurit' => 'electric drill',
        'Set de șurubelnițe' => 'screwdriver',
        'Cărucior pentru copii' => 'baby stroller',
        'Masă pliabilă' => 'folding table',
        'Set de scaune' => 'chair',
        'Fierăstrău electric' => 'circular saw',
        'Aparat de sudură' => 'welding machine',
        'Grătar portabil' => 'barbecue grill',
        'Trambulină fitness' => 'trampoline',
        'Colecție de cărți SF' => 'stack of books',
        'Proiector video' => 'projector',
        'Boxă portabilă' => 'bluetooth speaker',
        'Aerotermă' => 'space heater',
        'Pompa de bicicletă' => 'bike pump',
        'Set de chei' => 'wrench tools',
        'Flex' => 'angle grinder',
        'Nivelă cu laser' => 'laser level',
        'Cort de camping' => 'camping tent',
        'Sanie' => 'sled winter',
        'Bicicletă de oraș' => 'city bicycle',
        'Mixer de bucătărie' => 'stand mixer',
        'Mașină de cusut' => 'sewing machine',
    ];

    public function run(): void
    {
        $this->clearExisting();

        $items = Item::query()->get();

        foreach ($items as $index => $item) {
            $path = $this->downloadFromOpenverse($item)
                ?? $this->downloadFromPicsum($item)
                ?? $this->writeFallback($item, $index);

            $item->images()->create([
                'path' => $path,
                'sort_order' => 0,
            ]);
        }
    }

    private function clearExisting(): void
    {
        ObjectImage::query()->delete();
        Storage::disk('public')->deleteDirectory('objects');
    }

    private function downloadFromOpenverse(Item $item): ?string
    {
        $keyword = self::KEYWORDS[$item->title] ?? $item->category?->slug ?? 'tools';

        try {
            $search = Http::withoutVerifying()->timeout(20)->get('https://api.openverse.org/v1/images/', [
                'q' => $keyword,
                'page_size' => 1,
            ]);

            if (! $search->successful()) {
                return null;
            }

            $results = $search->json('results') ?? [];
            $imageUrl = $results[0]['thumbnail'] ?? $results[0]['url'] ?? null;

            if (! $imageUrl) {
                return null;
            }

            $image = Http::withoutVerifying()->timeout(20)->get($imageUrl);

            if (! $image->successful()) {
                return null;
            }

            $path = 'objects/'.$item->slug.$this->extensionFor($image->header('Content-Type'));
            Storage::disk('public')->put($path, $image->body());

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }

    private function downloadFromPicsum(Item $item): ?string
    {
        $url = 'https://picsum.photos/seed/'.urlencode($item->slug).'/800/600';

        try {
            $response = Http::withoutVerifying()->timeout(20)->get($url);

            if ($response->successful()) {
                $path = 'objects/'.$item->slug.'.jpg';
                Storage::disk('public')->put($path, $response->body());

                return $path;
            }
        } catch (\Throwable) {
            // Fall through.
        }

        return null;
    }

    private function writeFallback(Item $item, int $index): string
    {
        $icon = $item->category?->icon ?? '📦';
        [$from, $to] = self::PALETTE[$index % count(self::PALETTE)];

        $path = 'objects/'.$item->slug.'.svg';
        Storage::disk('public')->put($path, $this->renderSvg($item->title, $icon, $from, $to));

        return $path;
    }

    private function extensionFor(string $contentType): string
    {
        return match (strtolower(explode(';', $contentType)[0])) {
            'image/png' => '.png',
            'image/webp' => '.webp',
            'image/gif' => '.gif',
            default => '.jpg',
        };
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
