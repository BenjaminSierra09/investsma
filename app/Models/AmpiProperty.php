<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AmpiProperty extends Model
{
    /** @use HasFactory<\Database\Factories\AmpiPropertyFactory> */
    use HasFactory;

    protected $fillable = [
        'mls_id',
        'external_id',
        'name',
        'slug',
        'status',
        'category',
        'office_id',
        'city',
        'neighborhood',
        'price',
        'currency',
        'normalized_price',
        'bedrooms',
        'bathrooms',
        'floors',
        'construction_meters',
        'lot_meters',
        'furnished',
        'parking_type',
        'with_yard',
        'pool',
        'casita',
        'gated_comm',
        'latitude',
        'longitude',
        'featured_image',
        'photos',
        'raw_payload',
        'api_created_at',
        'api_updated_at',
        'last_synced_at',
        'is_active',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function toAmpiArray(): array
    {
        $payload = is_array($this->raw_payload) ? $this->raw_payload : [];

        return array_merge($payload, [
            'id' => $this->external_id ?? $payload['id'] ?? $this->id,
            'mls_id' => $this->mls_id,
            'name' => $this->name,
            'slug' => $this->slug ?: Str::slug((string) $this->name),
            'status' => $this->status,
            'category' => $this->category,
            'office_id' => $this->office_id,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'price' => $this->price !== null ? (float) $this->price : null,
            'currency' => $this->currency,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms !== null ? (float) $this->bathrooms : null,
            'floors' => $this->floors,
            'construction_meters' => $this->construction_meters !== null ? (float) $this->construction_meters : null,
            'lot_meters' => $this->lot_meters !== null ? (float) $this->lot_meters : null,
            'furnished' => $this->furnished,
            'parking_type' => $this->parking_type,
            'with_yard' => $this->yesNoValue($this->with_yard),
            'pool' => $this->yesNoValue($this->pool),
            'casita' => $this->yesNoValue($this->casita),
            'gated_comm' => $this->yesNoValue($this->gated_comm),
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'featured_image' => $this->featured_image,
            'photos' => $this->photos ?? [],
        ]);
    }

    protected static function booted(): void
    {
        static::saving(function (AmpiProperty $property): void {
            if (blank($property->slug) && filled($property->name)) {
                $property->slug = Str::slug($property->name);
            }

            if ($property->price !== null) {
                $property->normalized_price = (float) $property->price * Currency::rateFor($property->currency);
            }
        });
    }

    private function yesNoValue(?bool $value): ?string
    {
        return match ($value) {
            true => 'Yes',
            false => 'No',
            default => null,
        };
    }

    protected function casts(): array
    {
        return [
            'external_id' => 'integer',
            'office_id' => 'integer',
            'price' => 'decimal:2',
            'normalized_price' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'decimal:1',
            'floors' => 'integer',
            'construction_meters' => 'decimal:2',
            'lot_meters' => 'decimal:2',
            'with_yard' => 'boolean',
            'pool' => 'boolean',
            'casita' => 'boolean',
            'gated_comm' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'photos' => 'array',
            'raw_payload' => 'array',
            'api_created_at' => 'datetime',
            'api_updated_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
