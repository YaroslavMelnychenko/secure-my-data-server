<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Http\Requests\User\FaultReportRequest;

use App\Models\Traits\Uuid;
use App\Models\User;

class Fault extends Model
{
    use Uuid;

    protected $hidden = [
        'user_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function report(User $user, FaultReportRequest $request) {
        $instance = new static();
        $instance->user_id = $user->id;
        $instance->name = $request->name;
        $instance->desc = $request->desc;
        $instance->save();

        return $instance;
    }
}
