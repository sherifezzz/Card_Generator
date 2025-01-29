<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $primaryKey = 'card_id';

    protected $fillable = [
        'user_id',
        'card_name',
        'full_name',
        'job_title',
        'email',
        'phone',
        'address',
        'qr_url',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
