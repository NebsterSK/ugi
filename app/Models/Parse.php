<?php

namespace App\Models;

use Database\Factories\ParseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $url
 * @property string $content
 * @property Carbon $created_at
 *
 * @method static \Database\Factories\ParseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Parse extends Model
{
    /** @use HasFactory<ParseFactory> */
    use HasFactory;

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;
}
