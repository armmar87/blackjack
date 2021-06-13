<?php

namespace App\Models;

use App\Helpers\CardsService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function play()
    {
        return $this->hasOne(Play::class)->orderBy('round', 'DESC');
    }

    public static function getLastRoundPlayers(): array
    {
        return DB::select('SELECT `users`.`name`, `p`.`points`, `p`.`cards`
            FROM `users`
            LEFT JOIN (SELECT *
            FROM `plays`
            WHERE `id` IN (
            SELECT MAX(`id`) FROM `plays` GROUP BY user_id
            )) AS p ON users.id = p.user_id');
    }

    public function saveCardsWithPoint(CardsService $cardsService): User
    {
        $this->play->increment('points', $cardsService->points);
        $this->play->cards = $cardsService->cards;
        $this->play->save();

        return $this;
    }
}
