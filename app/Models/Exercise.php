<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Track;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'user_id', 'track_id'];

    protected $casts = [
        'content' => 'array', // Автоматически преобразует JSON в массив
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

}
