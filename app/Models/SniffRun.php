<?php

namespace App\Models;

use App\Enums\SniffRunStatus;
use Database\Factories\SniffRunFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property SniffRunStatus $status
 * @property int $filters_count
 * @property int $saved_count
 * @property int $skipped_count
 * @property int $failed_count
 * @property int $new_entries_count
 * @property string|null $error
 * @property Carbon $started_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin \Eloquent
 */
class SniffRun extends Model
{
    /** @use HasFactory<SniffRunFactory> */
    use HasFactory;

    use MassPrunable;

    protected const int INTERRUPTED_AFTER_HOURS = 1;

    protected const int PRUNE_AFTER_MONTHS = 3;

    protected $fillable = [
        'status',
        'filters_count',
        'saved_count',
        'skipped_count',
        'failed_count',
        'new_entries_count',
        'error',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'status' => SniffRunStatus::class,
        'filters_count' => 'integer',
        'saved_count' => 'integer',
        'skipped_count' => 'integer',
        'failed_count' => 'integer',
        'new_entries_count' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Methods

    /**
     * Still marked as running long after it started, so the process most likely died without finishing the run.
     */
    public function isInterrupted(): bool
    {
        return $this->status === SniffRunStatus::Running
            && $this->started_at->lt(now()->subHours(self::INTERRUPTED_AFTER_HOURS));
    }

    /**
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()->where('started_at', '<', now()->subMonths(self::PRUNE_AFTER_MONTHS));
    }
}
