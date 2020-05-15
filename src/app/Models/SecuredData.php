<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use App\Models\Traits\Uuid;
use App\Models\User;
use App\Models\Encryption\Symmetric;

use App\Http\Requests\User\Data\StoreRequest;

class SecuredData extends Model
{
    use Uuid;

    protected $hidden = [
        'user_id',
        'user'
    ];

    protected function disk() {
        return Storage::disk(config('filesystems.cloud'));
    }

    protected function cryptor() {
        $asymmetricKeyPair = $this->user->session->obtaineKeyPair();
         
        return Symmetric::createFromAsymmetric($this->user->password, $asymmetricKeyPair);        
    }

    protected function fillInformation($array) {
        $this->user_id = $array['user_id'];
        $this->name = $array['name'];
        $this->ext = $array['ext'];
        $this->mime_type = $array['mime_type'];
    }

    protected function saveToCloud($content) {
        $this->disk()->put($this->id, $content);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function belongsToUser(User $user) {
        return $this->user->id === $user->id;
    }

    public function getFullName() {
        if($this->ext === null) {
            return $this->name;
        } else {
            return "{$this->name}.{$this->ext}";
        }
    }

    public function retrieve() {
        if($this->mime_type == null) {

            $encrypted = $this->disk()->get($this->id);
            $decrypted = $this->cryptor()->decrypt($encrypted);

            return $decrypted;

        } else {

            $encryptedFile = $this->disk()->get($this->id);

            $temp = tmpfile();
            fwrite($temp, $encryptedFile);

            $decryptedFile = $this->cryptor()->decryptFile(stream_get_meta_data($temp)['uri']);

            fclose($temp);

            return $decryptedFile;

        }
    }

    public static function storeAttachment(User $user, StoreRequest $request) {
        $file = $request->file('attachment');

        $fullFileName = $file->getClientOriginalName();

        $tmp = explode(".", $fullFileName);
        $fileExt = end($tmp);
        $fileName = str_replace('.'.$fileExt, '', $fullFileName);
        $fileMimeType = $file->getClientMimeType();

        $instance = new static();

        $instance->fillInformation([
            'user_id' => $user->id,
            'name' => $fileName,
            'ext' => $fileExt,
            'mime_type' => $fileMimeType
        ]);

        $instance->save();

        $encryptedFile = $instance->cryptor()->encryptFile($file->getRealPath());

        $instance->saveToCloud($encryptedFile);

        return $instance;
    }

    public static function storePlainData(User $user, StoreRequest $request) {
        $name = $request->plain_name;
        $data = $request->plain_data;

        $instance = new static();

        $instance->fillInformation([
            'user_id' => $user->id,
            'name' => $name,
            'ext' => null,
            'mime_type' => null
        ]);

        $instance->save();

        $encryptedData = $instance->cryptor()->encrypt($data);

        $instance->saveToCloud($encryptedData);

        return $instance;
    }
}
