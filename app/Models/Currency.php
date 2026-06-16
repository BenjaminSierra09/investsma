<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /** @use HasFactory<\Database\Factories\CurrencyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_base',
    ];

    public static function rateFor(?string $currencyCode): float
    {
        $fallbackRates = [
            'MXN' => 1.0,
            'USD' => 17.0,
            'CAD' => 12.5,
            'EUR' => 18.5,
        ];

        $code = strtoupper(trim((string) $currencyCode));

        if ($code === '') {
            $code = 'USD';
        }

        return (float) (static::query()
            ->where('code', $code)
            ->value('exchange_rate') ?? $fallbackRates[$code] ?? $fallbackRates['USD']);
    }

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:6',
            'is_base' => 'boolean',
        ];
    }
}
