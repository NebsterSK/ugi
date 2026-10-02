<?php

namespace App\Models;

use App\Enums\Location;
use App\Enums\PropertyType;
use Database\Factories\FilterFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property PropertyType $property_type
 * @property Collection<int, Location> $locations
 * @property int|null $price_from
 * @property int|null $price_to
 * @property int|null $area_from
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Filter extends Model
{
    /** @use HasFactory<FilterFactory> */
    use HasFactory;

    protected const string BASE_URL = 'https://www.nehnutelnosti.sk/vysledky/';

    protected const string TRANSACTION = 'predaj';

    protected $fillable = [
        'name',
        'property_type',
        'locations',
        'price_from',
        'price_to',
        'area_from',
        'is_active',
    ];

    protected $casts = [
        'property_type' => PropertyType::class,
        'locations' => AsEnumCollection::class.':'.Location::class,
        'price_from' => 'integer',
        'price_to' => 'integer',
        'area_from' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scopes

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // Methods

    /**
     * Search results URL on nehnutelnosti.sk for the given page of this filter.
     */
    public function url(int $page = 1): string
    {
        $parameters = $this->locations->map(fn (Location $location): string => 'locations='.$location->value);

        $optionalParameters = [
            'priceFrom' => $this->price_from,
            'priceTo' => $this->price_to,
            'areaFrom' => $this->area_from,
            'page' => $page > 1 ? $page : null,
        ];

        foreach ($optionalParameters as $name => $value) {
            if ($value !== null) {
                $parameters->push($name.'='.$value);
            }
        }

        return self::BASE_URL.$this->property_type->value.'/'.self::TRANSACTION.'?'.$parameters->implode('&');
    }
}
