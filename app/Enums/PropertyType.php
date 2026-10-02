<?php

namespace App\Enums;

enum PropertyType: string
{
    case ThreeRoomApartment = '3-izbove-byty';
    case FourRoomApartment = '4-izbove-byty';

    public function label(): string
    {
        return match ($this) {
            self::ThreeRoomApartment => '3-room apartment',
            self::FourRoomApartment => '4-room apartment',
        };
    }
}
