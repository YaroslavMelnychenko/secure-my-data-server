<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests\User\Data\StoreRequest;
use App\Http\Requests\User\Data\UpdateRequest;
use App\Http\Response;

use App\Models\SecuredData;

use App\Helpers;

class SecuredDataController extends Controller
{
    protected $user;

    protected function checkBelongsToUser($data) {
        if(!$data->belongsToUser($this->user)) throw new ModelNotFoundException();
    }

    protected function storeAttachment(StoreRequest $request) {
        $securedData = SecuredData::storeAttachment($this->user, $request);

        return Response::send([
            'error' => false,
            'message' => $securedData
        ], 'SUCCESS');
    }

    protected function storePlainData(StoreRequest $request) {
        $securedData = SecuredData::storePlainData($this->user, $request);

        return Response::send([
            'error' => false,
            'message' => $securedData
        ], 'SUCCESS');
    }

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user(); 
            return $next($request);
        });
    }

    public function index() {
        return Response::send([
            'error' => false,
            'message' => $this->user->data
        ], 'SUCCESS');
    }

    public function store(StoreRequest $request) {
        if($request->file('attachment') !== null) {
            return $this->storeAttachment($request);
        } else {
            return $this->storePlainData($request);
        }
    }

    public function show(SecuredData $data) {
        $this->checkBelongsToUser($data);

        if($data->mime_type === null) {

            return Response::send([
                'error' => false,
                'message' => $data->retrieve()
            ], 'SUCCESS');
 
        } else {

            return response()->streamDownload(function () use ($data) {
                echo $data->retrieve();
            }, $data->getFullName(), [
                'Content-Type' => $data->mime_type
            ]);

        }
    }

    public function update(UpdateRequest $request, SecuredData $data) {
        $this->checkBelongsToUser($data);

        $data->name = $request->name;
        $data->save();

        return Response::send([
            'error' => false,
            'message' => $data
        ], 'SUCCESS');
    }

    public function destroy(SecuredData $data) {
        $this->checkBelongsToUser($data);

        $data->remove();

        return Response::send([
            'error' => false,
            'message' => $data
        ], 'SUCCESS');
    }
}
