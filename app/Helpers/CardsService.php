<?php
namespace App\Helpers;

use App\Enums\CardsType;
use Illuminate\Support\Facades\Http;

class CardsService
{
    public $deckCards;
    public $point;
    const CARDS_URL = 'https://pickatale-backend-case.herokuapp.com/shuffle';

    function __construct()
    {
        $this->deckCards = json_decode(Http::get(self::CARDS_URL)->body());
    }

    public function getPlayerCardsWithPoint(array $cards): array
    {
        $data = $this->getCard();
        array_push($cards, $this->getCardsAbbreviation($data));
        $point = $this->getPoint($data->value);

        return compact('cards', 'point');
    }

    private function getCard(): \stdClass
    {
        $count = count($this->deckCards);
        $randomNumber = rand(0, $count - 1);
        $card = $this->deckCards[$randomNumber];
        unset($this->deckCards[$randomNumber]);

        return $card;
    }

    private function getCardsAbbreviation(\stdClass $data): string
    {
        return CardsType::CARDS[$data->suit] . $data->value;
    }

    private function getPoint(string $value): int
    {
        if (in_array($value,CardsType::CARDS_PICTURES)) {
            return CardsType::POINTS[$value];
        }
        return (int) $value;
    }
}
