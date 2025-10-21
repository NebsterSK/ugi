<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $content
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Database\Factories\ParseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parse whereId($value)
 * @mixin \Eloquent
 */
class Parse extends Model
{
    /** @use HasFactory<\Database\Factories\ParseFactory> */
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
}
