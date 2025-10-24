<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $internal_id
 * @property string $url
 * @property string $title
 * @property string $slug
 * @property int $is_seen
 * @property int $is_favorite
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\EntryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereInternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereIsFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereIsSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereUrl($value)
 * @mixin \Eloquent
 */
class Entry extends Model
{
    /** @use HasFactory<\Database\Factories\EntryFactory> */
    use HasFactory;

    protected $casts = [
        'is_seen' => 'boolean',
        'is_favorite' => 'boolean',
    ];
}
