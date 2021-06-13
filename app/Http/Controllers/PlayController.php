<?php


namespace App\Http\Controllers;

use App\Helpers\CardsService;
use App\Models\Play;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class PlayController
{
    protected $cardsService;

    function __construct(CardsService $cardsService)
    {
        $this->cardsService = $cardsService;
    }

    public function playBlackjack()
    {
        $playerOne = User::with('play')->find(1);
        $playerTwo = User::with('play')->find(2);

        return $this->recursionPlayers($playerOne, $playerTwo);
    }

    protected function recursionPlayers($playerOne, $playerTwo)
    {
        for ($i = 0; $i < 2; $i++) {
            $this->cardsService->setCardsWithPoint($playerOne->play->cards);
            $playerOne = $playerOne->saveCardsWithPoint($this->cardsService);
            if ($winner = $this->returnWinnerPlayer($playerOne, $playerTwo)) {

                return $winner;
            }

            $this->cardsService->setCardsWithPoint($playerTwo->play->cards);
            $playerTwo = $playerTwo->saveCardsWithPoint($this->cardsService);
            if ($winner = $this->returnWinnerPlayer($playerTwo, $playerOne)) {

                return $winner;
            }
        }

        if ($playerOne->play->points < 17 || $playerOne->play->points > $playerTwo->play->points) {
            return $this->recursionPlayers($playerOne, $playerTwo);
        }

        return Response::json(['message' => 'No winner, play again', 'players' => User::getLastRoundPlayers()],200);
    }

    protected function returnWinnerPlayer($playerOne, $playerTwo)
    {
        if ($playerOne->play->points == 21) {

            return $this->returnResponse($playerOne, $playerTwo);
        }
        if ($playerOne->play->points > 21) {

            return $this->returnResponse($playerTwo, $playerOne);
        }

        return false;
    }

    protected function returnResponse($playerOne, $playerTwo)
    {
        return Response::json([
            'winner' => $playerOne->name,
            'players' => Play::saveWinner($playerOne, $playerTwo)
        ],200);
    }
}
