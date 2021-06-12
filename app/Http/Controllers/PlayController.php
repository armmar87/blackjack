<?php


namespace App\Http\Controllers;


use App\Enums\CardsType;
use App\Models\Play;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\Types\Object_;

class PlayController
{
    const CARDS_URL = 'https://pickatale-backend-case.herokuapp.com/shuffle';

    public function play()
    {
        $playerOne = User::whereHas('play', function ($query) {
            $query->where('player', 1);
        } )->first();
        $playerTwo = User::whereHas('play', function ($query) {
            $query->where('player', 2);
        } )->first();

        $response = Http::get(self::CARDS_URL);
        $cards = json_decode($response->body());
        if ($playerOne->play->attempt === 0) {
            for ($i = 0; $i < 2; $i++) {
                $data = $this->getCard($cards);
                array_push($playerOne->play->cards, $this->getCardsAbbreviation($data));
                $playerOne->play->increment('points', $data->value);
                $playerOne->play->cards = $playerOne->play->cards;
                $playerOne->play->save();
            }
            $playerOne->play->increment('attempt');

            dd($data, $cards);
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
}
