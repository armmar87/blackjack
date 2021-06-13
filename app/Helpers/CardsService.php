<?php
namespace App\Helpers;

use App\Enums\CardsType;
use Illuminate\Support\Facades\Http;

class CardsService
{
    public $cards;
    public $points;
    public $deckCards;
    const CARDS_URL = 'https://pickatale-backend-case.herokuapp.com/shuffle';

    function __construct()
    {
        $this->deckCards = json_decode(Http::get(self::CARDS_URL)->body());
    }

    public function setCardsWithPoint(array $cards): void
    {
        $data = $this->getPlayerCards();
        array_push($cards, $this->getCardsAbbreviation($data));
        $points = $this->getPlayerPoints($data->value);

        $this->points = $points;
        $this->cards = $cards;
    }

    private function getPlayerCards(): \stdClass
    {
        $count = count($this->deckCards) - 1;
        $randomNumber = rand(0, $count);
        $card = $this->deckCards[$randomNumber];
        unset($this->deckCards[$randomNumber]);
        sort($this->deckCards);

        return $card;
    }

    private function getCardsAbbreviation(\stdClass $data): string
    {
        return CardsType::CARDS[$data->suit] . $data->value;
    }

    private function getPlayerPoints(string $value): int
    {
        if (in_array($value,CardsType::CARDS_PICTURES)) {
            return CardsType::POINTS[$value];
        }
        return (int) $value;
    }

    public function getCards()
    {
        return $this->cards;
    }

    public function getPoint()
    {
        return $this->points;
    }
}
