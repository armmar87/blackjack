<?php

namespace Unit\App\Http\Controllers;

use App\Helpers\CardsService;
use App\Http\Controllers\PlayController;
use Mockery;
use Tests\Unit\BaseTestCase;

class PlayControllerTest extends BaseTestCase
{
    protected $cardsService;

    const PLAYERS = ['players'];
    const NAME = 'name';

    protected function mockeryTestSetUp()
    {
        parent::mockeryTestSetUp();
        $this->SUT = Mockery::mock(PlayController::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $this->cardsService = Mockery::mock(CardsService::class);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function GIVEN_WHEN_construct()
    {
        $this->constructMocks();

        $this->assertEquals(true, true);
    }

    private function constructMocks()
    {
        $this->SUT->__construct($this->cardsService);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function GIVEN_WHEN_playBlackjack_THEN_return_recursionPlayers()
    {
        // GIVEN
        $this->constructMocks();

        $userMock = Mockery::mock('alias:App\Models\User');
        $userMock->shouldReceive('with')
            ->twice()
            ->with(Mockery::any())
            ->andReturn($userMock)
            ->shouldReceive('find')
            ->twice()
            ->with(Mockery::any());

        $this->SUT->shouldReceive('recursionPlayers')
            ->with(Mockery::any(), Mockery::any())
            ->andReturn(1);

        $expected = 1;

        // WHEN
        $actual = $this->SUT->playBlackjack();

        // THEN
        $this->assertEquals($expected, $actual);
    }

    public function recursionPlayersProvider()
    {
        return [
            'GIVEN_players_WHEN_recursionPlayers_players_complete_points_THEN_return_not_winner' => [
                ['message' => 'No winner, play again', 'players' => self::PLAYERS],
                17,
                17,
                false,
            ],
            'GIVEN_players_WHEN_recursionPlayers_players_complete_less_points_THEN_get_recursion_function' => [
                ['message' => 'No winner, play again', 'players' => self::PLAYERS],
                15,
                17,
                false,
            ],
            'GIVEN_players_WHEN_recursionPlayers_players_complete_points_THEN_return_winner' => [
                true,
                21,
                20,
                true,
            ],
        ];
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider recursionPlayersProvider
     */
    public function recursionPlayers($expected, $playerOnePoint, $playerTwoPoint, $winner)
    {
        // GIVEN
        $this->constructMocks();

        $userMockOne = Mockery::mock('alias:App\Models\User');
        $userMockTwo = Mockery::mock('alias:App\Models\User');

        $this->cardsService->shouldReceive('setCardsWithPoint');

        $stdClassForPlayerOne = new \stdClass();
        $stdClassForPlayerOne->cards = [1];
        $stdClassForPlayerOne->points = $playerOnePoint;
        $userMockOne->play = $stdClassForPlayerOne;

        $stdClassForPlayerTwo = new \stdClass();
        $stdClassForPlayerTwo->cards = [1];
        $stdClassForPlayerTwo->points = $playerTwoPoint;
        $userMockTwo->play = $stdClassForPlayerTwo;

        if ($playerOnePoint < 17 || $playerOnePoint > $playerTwoPoint) {
            $this->SUT->shouldReceive('recursionPlayers')
                ->once()
                ->with(Mockery::any(), Mockery::any())
                ->andReturn($expected);
        } else {
            $userMockOne->shouldReceive('getLastRoundPlayers')
                ->once()
                ->andReturn(self::PLAYERS);

            $userMockOne->shouldReceive('saveCardsWithPoint')
                ->twice()
                ->with(Mockery::any())
                ->andReturn($userMockOne);

            $userMockTwo->shouldReceive('saveCardsWithPoint')
                ->twice()
                ->with(Mockery::any())
                ->andReturn($userMockTwo);

            if (!$winner) {
                $this->SUT->shouldReceive('returnWinnerPlayer')
                    ->times(4)
                    ->with(Mockery::any(), Mockery::any())
                    ->andReturn($winner);
            }

            Mockery::mock('alias:Illuminate\Support\Facades\Response')
                ->shouldReceive('json')
                ->andReturn($expected);
        }

        // WHEN
        $actual = $this->SUT->recursionPlayers($userMockOne, $userMockTwo);

        // THEN
        $this->assertEquals($expected, $actual);
    }

    public function returnWinnerPlayerProvider()
    {
        return [
            'GIVEN_players_WHEN_returnWinnerPlayer_player1_complete_less_points_THEN_no_winner' => [
                false,
                15,
                15,
            ],
            'GIVEN_players_WHEN_returnWinnerPlayer_playerOne_complete_21_points_THEN_return_winner' => [
                self::PLAYERS,
                21,
                15,
            ],
            'GIVEN_players_WHEN_returnWinnerPlayer_playerTwo_complete_21_points_THEN_return_winner' => [
                self::PLAYERS,
                15,
                21,
            ]
        ];
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider returnWinnerPlayerProvider
     */
    public function returnWinnerPlayer($expected, $playerOnePoint, $playerTwoPoint)
    {
        // GIVEN
        $this->constructMocks();

        $userMockOne = Mockery::mock('alias:App\Models\User');
        $userMockTwo = Mockery::mock('alias:App\Models\User');
        $userMockOne->name = self::NAME;
        $userMockTwo->name = self::NAME;

        $this->cardsService->shouldReceive('setCardsWithPoint');

        $stdClassForPlayerOne = new \stdClass();
        $stdClassForPlayerOne->cards = [1];
        $stdClassForPlayerOne->points = $playerOnePoint;
        $userMockOne->play = $stdClassForPlayerOne;

        $stdClassForPlayerTwo = new \stdClass();
        $stdClassForPlayerTwo->cards = [1];
        $stdClassForPlayerTwo->points = $playerTwoPoint;
        $userMockTwo->play = $stdClassForPlayerTwo;

        $actual = false;
        if ($playerOnePoint == 21 || $playerTwoPoint > 21) {
           $this->SUT->shouldReceive('returnResponse')
               ->once()
               ->with(Mockery::any(), Mockery::any())
               ->andReturn(self::PLAYERS);

            // WHEN
            $actual = $this->SUT->returnWinnerPlayer($userMockOne, $userMockTwo);
        }

        if ($playerTwoPoint == 21 || $playerOnePoint > 21) {
            $this->SUT->shouldReceive('returnResponse')
                ->once()
                ->with(Mockery::any(), Mockery::any())
                ->andReturn(self::PLAYERS);

            // WHEN
            $actual = $this->SUT->returnWinnerPlayer($userMockTwo, $userMockOne);
        }

        // THEN
        $this->assertEquals($expected, $actual);
    }
}

