<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Widgets\ChartWidget;

class ObjectsByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Obiecte pe categorii';

    protected function getData(): array
    {
        $grouped = Item::query()
            ->with('category')
            ->get()
            ->groupBy(fn (Item $item) => $item->category?->name ?? 'Fără categorie')
            ->map->count();

        $palette = ['#14b8a6', '#0ea5e9', '#8b5cf6', '#f59e0b', '#ef4444', '#10b981', '#6366f1', '#f97316'];

        return [
            'datasets' => [
                [
                    'label' => 'Obiecte',
                    'data' => $grouped->values()->toArray(),
                    'backgroundColor' => array_slice(array_merge($palette, $palette), 0, $grouped->count()),
                ],
            ],
            'labels' => $grouped->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
