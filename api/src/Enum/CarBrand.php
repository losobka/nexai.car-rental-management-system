<?php

namespace App\Enum;

enum CarBrand: string
{
    case AUDI = 'Audi';
    case BMW ='BMW';
    case CHEVROLET = 'Chevrolet';
    case FERRARI = 'Ferrari';
    case FORD = 'Ford';
    case HONDA = 'Honda';
    case HYUNDAI = 'Hyundai';
    case JEEP = 'Jeep';
    case KEI = 'Kia';
    case LAMBORGHINI = 'Lamborghini';
    case LEXUS = 'Lexus';
    case MAZDA = 'Mazda';
    case MERCEDES_BENZ = 'Mercedes-Benz';
    case NISSAN = 'Nissan';
    case PORSCHE = 'Porsche';
    case SUBARU = 'Subaru';
    case TESLA = 'Tesla';
    case TOYOTA = 'Toyota';
    case VOLKSWAGEN = 'Volkswagen';
    case VOLVO ='Volvo';

    public static function values(): array
    {
        return self::cases();
        return array_column(self::cases(), 'value');
    }
}