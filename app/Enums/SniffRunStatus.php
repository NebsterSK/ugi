<?php

namespace App\Enums;

enum SniffRunStatus: string
{
    case Running = 'running';
    case Succeeded = 'succeeded';
    case Warning = 'warning';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Running => 'Running',
            self::Succeeded => 'Succeeded',
            self::Warning => 'Warning',
            self::Failed => 'Failed',
        };
    }

    /**
     * Bootstrap contextual color used for the status badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Running => 'info',
            self::Succeeded => 'success',
            self::Warning => 'warning',
            self::Failed => 'danger',
        };
    }
}
