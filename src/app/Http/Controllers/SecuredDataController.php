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
        $file = $request->file('attachment');

        return Response::send([
            'error' => false,
            'message' => "Create secured data for user {$this->user->email}",
            'request' => [
                'size' => Helpers::formatBytes($file->getSize()),
                'name' => $file->getClientOriginalName()
            ]
        ], 'SUCCESS');
    }

    protected function storePlainData(StoreRequest $request) {
        $plainName = $request->input('plain_name');
        $plainData = $request->input('plain_data');

        return Response::send([
            'error' => false,
            'message' => "Create secured data for user {$this->user->email}",
            'request' => [
                'plain_name' => $plainName,
                'plain_data' => $plainData
            ]
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

        return Response::send([
            'error' => false,
            'message' => $data
        ], 'SUCCESS');
    }

    public function update(UpdateRequest $request, SecuredData $data) {
        $this->checkBelongsToUser($data);

        return Response::send([
            'error' => false,
            'message' => "Edit data with new information",
            'request' => $request->all()
        ], 'SUCCESS');
    }

    public function destroy(SecuredData $data) {
        $this->checkBelongsToUser($data);

        return Response::send([
            'error' => false,
            'message' => "Delete data"
        ], 'SUCCESS');
    }
}
