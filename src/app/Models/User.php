<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;

use App\Models\Traits\Uuid;
use App\Models\Encryption\Asymmetric;
use App\Models\Session;
use App\Models\SecuredData;

use App\Http\Requests\Auth\RegisterRequest;

use App\Events\UserRegistered;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, Uuid;
    
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'verified', 'verification_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'verification_code', 
        'public_key',
        'created_at',
        'updated_at',
        'id'
    ];

    public function session() {
        return $this->hasOne(Session::class);
    }

    public function data() {
        return $this->hasMany(SecuredData::class);
    }

    public function asymmetricChallenge(Asymmetric $keyPair) {
        return $this->public_key == $keyPair->exportPublicKey();
    }

    public static function create(RegisterRequest $request, Asymmetric $keyPair) {
        $instance = new static;
        $instance->email = $request->email;
        $instance->password = bcrypt($request->password);
        $instance->public_key = $keyPair->exportPublicKey();
        $instance->save();

        event(new UserRegistered($instance));

        return $instance;
    }

    public static function exists($email) {
        $instance = self::where('email', $email)->first();

        return $instance;
    }
}
