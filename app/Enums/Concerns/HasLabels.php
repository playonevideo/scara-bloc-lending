<?php

namespace App\Enums\Concerns;

trait HasLabels
{
    /**
     * The Romanian display labels for each case.
     */
    abstract public static function labels(): array;

    public function label(): string
    {
        return static::labels()[$this->value] ?? $this->value;
    }

    /**
     * Options suitable for a select input: [value => label].
     */
    public static function options(): array
    {
        return static::labels();
    }

    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
