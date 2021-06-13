<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    protected $table = 'plays';
    public $timestamps = false;
    protected $casts = [
        'cards' =>'array'
    ];
    protected $fillable = ['round'];


    public  static function saveWinner($playerOne, $playerTwo): array
    {
        $playerOne->play->win = 1;
        $playerOne->play->save();
        $players = User::getLastRoundPlayers();

        $playerOne->play()->create(['round' => $playerOne->play->round + 1]);
        $playerTwo->play()->create(['round' => $playerTwo->play->round + 1]);

        return $players;
    }
}
