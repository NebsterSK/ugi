<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $internal_id
 * @property string $url
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $seen_at
 * @property \Illuminate\Support\Carbon|null $favorited_at
 * @property bool $is_ignored
 * @property int $rooms
 * @property string $street
 * @property string $district
 * @property int $area
 * @property int $price
 * @property int $price_per_sqm
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_favorite
 * @method static \Database\Factories\EntryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Entry favorite()
 * @method static Builder<static>|Entry ignored()
 * @method static Builder<static>|Entry newModelQuery()
 * @method static Builder<static>|Entry newQuery()
 * @method static Builder<static>|Entry newState()
 * @method static Builder<static>|Entry notIgnored()
 * @method static Builder<static>|Entry query()
 * @method static Builder<static>|Entry seen()
 * @method static Builder<static>|Entry whereArea($value)
 * @method static Builder<static>|Entry whereComment($value)
 * @method static Builder<static>|Entry whereCreatedAt($value)
 * @method static Builder<static>|Entry whereDistrict($value)
 * @method static Builder<static>|Entry whereFavoritedAt($value)
 * @method static Builder<static>|Entry whereId($value)
 * @method static Builder<static>|Entry whereInternalId($value)
 * @method static Builder<static>|Entry whereIsIgnored($value)
 * @method static Builder<static>|Entry wherePrice($value)
 * @method static Builder<static>|Entry wherePricePerSqm($value)
 * @method static Builder<static>|Entry whereRooms($value)
 * @method static Builder<static>|Entry whereSeenAt($value)
 * @method static Builder<static>|Entry whereStreet($value)
 * @method static Builder<static>|Entry whereTitle($value)
 * @method static Builder<static>|Entry whereUpdatedAt($value)
 * @method static Builder<static>|Entry whereUrl($value)
 * @mixin \Eloquent
 */
class Entry extends Model
{
    /** @use HasFactory<\Database\Factories\EntryFactory> */
    use HasFactory;

    protected $casts = [
        'seen_at' => 'datetime:Y-m-d H:i:s',
        'favorited_at' => 'datetime:Y-m-d H:i:s',
        'is_ignored' => 'boolean',
    ];

    // Scopes

    #[Scope]
    protected function newState(Builder $query): void
    {
        $query->whereNull('seen_at')->whereNull('favorited_at')->where('is_ignored', false);
    }

    #[Scope]
    protected function seen(Builder $query): void
    {
        $query->whereNotNull('seen_at')->whereNull('favorited_at')->where('is_ignored', false);
    }

    #[Scope]
    protected function favorite(Builder $query): void
    {
        $query->whereNotNull('seen_at')->whereNotNull('favorited_at')->where('is_ignored', false);
    }

    #[Scope]
    protected function ignored(Builder $query): void
    {
        $query->where('is_ignored', true);
    }

    #[Scope]
    protected function notIgnored(Builder $query): void
    {
        $query->where('is_ignored', false);
    }

    // Accessors & Mutators

    public function isFavorite(): Attribute
    {
        return Attribute::make(get: function(): bool {
            return (bool) $this->favorited_at;
        });
    }
}
