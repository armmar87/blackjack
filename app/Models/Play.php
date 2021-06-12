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
}
