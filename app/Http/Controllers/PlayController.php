<?php


namespace App\Http\Controllers;


use App\Enums\CardsType;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class PlayController
{
    const CARDS_URL = 'https://pickatale-backend-case.herokuapp.com/shuffle';

    public function play()
    {
        $playerOne = User::getPlayer(1);
        $playerTwo = User::getPlayer(2);

        $response = Http::get(self::CARDS_URL);
        $cards = json_decode($response->body());
        if (!$playerOne->play->win && !$playerTwo->play->win) {
            for ($i = 0; $i < 2; $i++) {
                $data = $this->getCard($cards);
                $playCards = $playerOne->play->cards;
                array_push($playCards, $this->getCardsAbbreviation($data));
                $point = $this->getPoint($data->value);

                $playerOne->play->increment('points', $point);
                $playerOne->play->cards = $playCards;
                $playerOne->play->save();

                $data = $this->getCard($cards);
                $playCards = $playerTwo->play->cards;
                array_push($playCards, $this->getCardsAbbreviation($data));
                $point = $this->getPoint($data->value);

                $playerTwo->play->increment('points', $point);
                $playerTwo->play->cards = $playCards;
                $playerTwo->play->save();

            }
        }
    }

    private function getCard(array &$cards): \stdClass
    {
        $count = count($cards);
        $randomNumber = rand(0, $count - 1);
        $card = $cards[$randomNumber];
        unset($cards[$randomNumber]);

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
