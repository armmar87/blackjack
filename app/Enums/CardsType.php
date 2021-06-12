<?php
namespace App\Enums;
use BenSampo\Enum\Enum;

class CardsType extends Enum
{
    const SPADES = 'SPADES';
    const DIAMONDS = 'DIAMONDS';
    const HEARTS = 'HEARTS';
    const CLUBS = 'CLUBS';

    const CARDS = [
        self::SPADES => 'S',
        self::DIAMONDS => 'D',
        self::HEARTS => 'H',
        self::CLUBS => 'C',
    ];
}
