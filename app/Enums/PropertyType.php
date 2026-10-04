<?php

namespace App\Enums;

enum PropertyType: string
{
    case TwoRoomApartment = '2-izbove-byty';
    case ThreeRoomApartment = '3-izbove-byty';
    case FourRoomApartment = '4-izbove-byty';

    public function label(): string
    {
        return match ($this) {
            self::TwoRoomApartment => '2-room apartment',
            self::ThreeRoomApartment => '3-room apartment',
            self::FourRoomApartment => '4-room apartment',
        };
    }
}
