<?php


namespace App\Http\Controllers;

use App\Helpers\CardsService;
use App\Models\User;

class PlayController
{
    public function play()
    {
        $playerOne = User::with('play')->find(1);
        $playerTwo = User::with('play')->find(2);

        if (!$playerOne->play->win && !$playerTwo->play->win) {
            $cardsService = new CardsService();

            for ($i = 0; $i < 2; $i++) {

                $playerOneData = $cardsService->getPlayerCardsWithPoint($playerOne->play->cards);
                $playerOne = $playerOne->saveCardsWithPoint($playerOneData);

                $playerTwoData = $cardsService->getPlayerCardsWithPoint($playerTwo->play->cards);
                $playerTwo = $playerTwo->saveCardsWithPoint($playerTwoData);

                if ($playerOne->play->points == 21) {
                    $playerOne->play->win = 1;
                    $playerOne->play->save();
                    $players = User::getLastRoundPlayers();

                    $playerOne->play()->create(['round' => $playerOne->play->round + 1]);
                    $playerTwo->play()->create(['round' => $playerTwo->play->round + 1]);

                    $results = [
                        'winner' => $playerOne->name,
                        'players' => $players
                    ];
                    return response()->json($results,200);
                }

                if ($playerTwo->play->points == 21) {
                    $playerTwo->play->win = 1;
                    $playerTwo->play->save();
                    $players = User::getLastRoundPlayers();

                    $playerTwo->play()->create(['round' => $playerTwo->play->round + 1]);
                    $playerOne->play()->create(['round' => $playerOne->play->round + 1]);

                    $results = [
                        'winner' => $playerTwo->name,
                        'players' => $players
                    ];
                    return response()->json($results,200);
                }
            }
        }
    }
}
