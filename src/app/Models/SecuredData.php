<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\Uuid;
use App\Models\User;

class SecuredData extends Model
{
    use Uuid;

    protected $hidden = [
        'user_id',
        'user'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function belongsToUser(User $user) {
        return $this->user->id === $user->id;
    }
}
