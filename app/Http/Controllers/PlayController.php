<?php


namespace App\Http\Controllers;

use App\Helpers\CardsService;
use App\Models\User;

class PlayController
{
    private CardsService $cardsService;

    function __construct(CardsService $cardsService)
    {
        $this->cardsService = $cardsService;
    }

    public function playBlackjack()
    {
        $playerOne = User::with('play')->find(1);
        $playerTwo = User::with('play')->find(2);

        return $this->recursionForPlayers($playerOne, $playerTwo);
    }

    private function recursionForPlayers($playerOne, $playerTwo)
    {
        for ($i = 0; $i < 2; $i++) {
            $this->cardsService->setCardsWithPoint($playerOne->play->cards);
            $playerOne = $playerOne->saveCardsWithPoint($this->cardsService);

            if ($playerOne->play->points == 21) {
                $playerOne->play->win = 1;
                $playerOne->play->save();
                $players = User::getLastRoundPlayers();

                $playerOne->play()->create(['round' => $playerOne->play->round + 1]);
                $playerTwo->play()->create(['round' => $playerTwo->play->round + 1]);

                return response()->json(['winner' => $playerOne->name, 'players' => $players],200);
            }
            if ($playerOne->play->points > 21) {
                $playerTwo->play->win = 1;
                $playerTwo->play->save();
                $players = User::getLastRoundPlayers();

                $playerTwo->play()->create(['round' => $playerTwo->play->round + 1]);
                $playerOne->play()->create(['round' => $playerOne->play->round + 1]);

                return response()->json(['winner' => $playerTwo->name, 'players' => $players],200);
            }
        }

        for ($i = 0; $i < 2; $i++) {
            $this->cardsService->setCardsWithPoint($playerTwo->play->cards);
            $playerTwo = $playerTwo->saveCardsWithPoint($this->cardsService);
            if ($playerTwo->play->points == 21) {
                $playerTwo->play->win = 1;
                $playerTwo->play->save();
                $players = User::getLastRoundPlayers();

                $playerTwo->play()->create(['round' => $playerTwo->play->round + 1]);
                $playerOne->play()->create(['round' => $playerOne->play->round + 1]);

                return response()->json(['winner' => $playerTwo->name, 'players' => $players],200);
            }
            if ($playerTwo->play->points > 21) {
                $playerOne->play->win = 1;
                $playerOne->play->save();
                $players = User::getLastRoundPlayers();

                $playerOne->play()->create(['round' => $playerOne->play->round + 1]);
                $playerTwo->play()->create(['round' => $playerTwo->play->round + 1]);

                return response()->json(['winner' => $playerOne->name, 'players' => $players],200);
            }
        }

        if ($playerOne->play->points < 17 || $playerOne->play->points > $playerTwo->play->points) {
            return $this->recursionForPlayers($playerOne, $playerTwo);
        }
        return response()->json(['message' => 'No winner, play again', 'players' => User::getLastRoundPlayers()],200);
    }
}
