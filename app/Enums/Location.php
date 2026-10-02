<?php

namespace App\Enums;

enum Location: int
{
    case NoveMesto = 100012513;
    case Petrzalka = 100012524;
    case Raca = 100012514;
    case Ruzinov = 100012511;

    public function label(): string
    {
        return match ($this) {
            self::NoveMesto => 'Nové Mesto',
            self::Petrzalka => 'Petržalka',
            self::Raca => 'Rača',
            self::Ruzinov => 'Ružinov',
        };
    }
}
