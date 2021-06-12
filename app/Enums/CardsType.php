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

    const JACK = 'J';
    const QUEEN = 'Q';
    const KING = 'K';
    const ACE = 'A';

    const CARDS_PICTURES = [self::JACK, self::QUEEN, self::KING, self::ACE];

    const POINTS = [
        self::JACK => 10,
        self::QUEEN => 10,
        self::KING => 10,
        self::ACE => 11,
    ];
}
