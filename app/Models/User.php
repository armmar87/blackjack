<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    public static function getLastRoundPlayers(): Collection
    {
        return self::select(['users.name', 'p.points', 'p.cards'])
            ->leftJoin('plays as p', 'users.id', '=', 'p.user_id')
            ->orderBy('p.round', 'DESC')
            ->groupBy('users.id')
            ->get();
    }

    public function saveCardsWithPoint(array $data): User
    {
        $this->play->increment('points', $data['point']);
        $this->play->cards = $data['cards'];
        $this->play->save();

        return $this;
    }
}
