<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

use App\Models\Traits\Uuid;
use App\Models\User;
use App\Models\Encryption\Symmetric;

class SecuredData extends Model
{
    use Uuid;

    protected $hidden = [
        'user_id',
        'user'
    ];

    protected function cryptor() {
        $asymmetricKeyPair = $this->user->session->obtaineKeyPair();
         
        return Symmetric::createFromAsymmetric($this->user->password, $asymmetricKeyPair);        
    }

    protected function fillInformation($array) {
        foreach($array as $key => $value)
            $this[$key] = $value;
    }

    protected function disk() {
        return Storage::disk(config('filesystems.cloud'));
    }

    protected function saveToCloud($path) {
        $this->disk()->putFileAs('secured', new File($path), $this->id);
    }

    protected function getFromCloud() {
        return $this->disk()->get('secured/'.$this->id);
    }

    protected function removeFromCloud() {
        $this->disk()->delete('secured/'.$this->id);
    }

    public function remove() {
        $this->removeFromCloud();
        $this->delete();
    }

    public function retrieve() {
        if($this->mime_type == null) {
            $encrypted = $this->getFromCloud();

            return $this->cryptor()->decrypt($encrypted);
        } else {
            $tmp = tmpfile();
            fwrite($tmp, $this->getFromCloud());
            $decryptedFile = $this->cryptor()->decryptFile(stream_get_meta_data($tmp)['uri']);

            return fread($decryptedFile, filesize(stream_get_meta_data($decryptedFile)['uri']));
        }
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

    public function storeAttachment(User $user, $attachment) {
        $fullFileName = $attachment->getClientOriginalName();

        $tmp = explode(".", $fullFileName);

        if(count($tmp) <= 1)
            $fileExt = null;
        else
            $fileExt = end($tmp);
            
        $fileName = str_replace('.'.$fileExt, '', $fullFileName);
        $fileMimeType = $attachment->getMimeType();
        $fileOriginalSize = $attachment->getSize();

        $this->fillInformation([
            'user_id' => $user->id,
            'name' => $fileName,
            'ext' => $fileExt,
            'mime_type' => $fileMimeType,
            'size' => $fileOriginalSize
        ]);

        $this->save();

        $encryptedFile = $this->cryptor()->encryptFile($attachment->getRealPath());

        $this->saveToCloud(stream_get_meta_data($encryptedFile)['uri']);
    }

    public function storePlainData(User $user, $plainName, $plainData) {
        $this->fillInformation([
            'user_id' => $user->id,
            'name' => $plainName,
            'ext' => null,
            'mime_type' => null,
            'size' => null
        ]);

        $this->save();
        
        $encryptedData = $this->cryptor()->encrypt($plainData);

        $tmpFile = tmpfile();
        fwrite($tmpFile, $encryptedData);

        $this->saveToCloud(stream_get_meta_data($tmpFile)['uri']);

        fclose($tmpFile);
    }
}
