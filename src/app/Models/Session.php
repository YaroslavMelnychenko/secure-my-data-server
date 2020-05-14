<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Traits\Uuid;
use App\Models\User;
use App\Models\Encryption\Asymmetric;

use Carbon\Carbon;

class Session extends Model
{
    use Uuid;

    protected $dates = [
        'expires_at',
    ];

    protected $hidden = [
        'updated_at',
        'id',
        'user_id',
        'refresh_stamp'
    ];

    private function tempDisk() {
        return Storage::disk('public');
    }

    private function sessionDisk() {
        return Storage::disk('local');
    }

    private function createTempFile($name, $content) {
        $this->tempDisk()->put('temp/'.$name, $content, 'public');

        return $this->tempDisk()->url('temp/'.$name);
    }

    private function deleteTempFile($name) {
        $this->tempDisk()->delete('temp/'.$name);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function drop() {
        $this->removeKeyPair();
        $this->delete();
    }

    public function storeKeyPair(Asymmetric $keyPair) {
        $this->sessionDisk()->put(
            'session/keys/'.$this->id, 
            encrypt($keyPair->exportPrivateKey([]))
        );
    }

    public function removeKeyPair() {
        $this->sessionDisk()->delete('session/keys/'.$this->id);
    }

    public function obtaineKeyPair() {
        $keyMaterial = decrypt($this->sessionDisk()->get('session/keys/'.$this->id));

        $tempName = Str::random(32);
        $tempFile = $this->createTempFile($tempName, $keyMaterial);

        $keyPair = Asymmetric::restoreKeyPair($tempFile);

        $this->deleteTempFile($tempName);

        return $keyPair;
    }

    public function prolongate() {
        $session_lifetime = (int) config('passport.session_lifetime');

        $this->expires_at = Carbon::now()->addMinutes(2 * $session_lifetime);
    }

    public function setRefreshStamp($refreshToken) {
        $this->refresh_stamp = md5($refreshToken);
    }

    public static function dropAll() {
        $instances = self::all();

        foreach($instances as $instance) $instance->drop();

        $staticInstance = new static();

        $staticInstance->tempDisk()->deleteDirectory('temp');
        $staticInstance->sessionDisk()->deleteDirectory('session');
        $staticInstance->delete();
    }

    public static function purgeExpired() {
        $instances = self::where('expires_at', '<', Carbon::now())->get();

        foreach($instances as $instance) 
            $instance->drop();
    }

    public static function findByUser(User $user) {
        return self::where('user_id', $user->id)->first();
    } 

    public static function findByRefreshToken($refreshToken) {
        return self::where('refresh_stamp', md5($refreshToken))->first();
    }

    public static function createInstance(User $user, $refreshToken) {
        if(!($instance = self::findByUser($user))) {
            $instance = new static();
            $instance->user_id = $user->id;
        }

        $instance->setRefreshStamp($refreshToken);
        $instance->prolongate();
        $instance->save();
        return $instance;
    }

    public static function refreshInstance($oldRefreshToken, $newRefreshToken) {
        $instance = self::findByRefreshToken($oldRefreshToken);

        $instance->setRefreshStamp($newRefreshToken);
        $instance->prolongate();
        $instance->save();
        return $instance;
    }
}
