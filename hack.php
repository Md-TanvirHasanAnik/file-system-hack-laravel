<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\File;



class StorageController extends Controller
{
    public function createParentFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|max:191",
        ]);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
        try {
            $response = Storage::makeDirectory('UserUpload/' . auth()->id() . '/' . $request->name);
            if ($response) {
                return response()->json(['message' => "Folder Created Successfully!"]);
            }

        } catch (\Exception $e) {
            return $e;
        }

    }

    public function createChildFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|max:191",
            "parent_directory" => "required|max:191"
        ]);
        if ($validator->fails()) {
            return $validator->errors()->getMessages();
        }
        try {
            $path = $request->parent_directory . '/' . $request->name;
            $response = Storage::makeDirectory('UserUpload/' . auth()->id() . '/' . $request->parent_directory . '/' . $request->name);
            if ($response) {
                return response()->json(['message' => "Folder Created Successfully!", 'path' => $path]);
            }
        } catch (\Exception $e) {
            return $e;
        }

    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "file" => "max:100000",
            "directory" => "required|max:191"
        ]);
        try
        {
            $path = auth()->id().'/'.$request->directory.'/'.$request->file('file')->getClientOriginalName();
            $result =  $request->file('file')->storeAs(auth()->id().'/'.$request->directory.'/', $request->file('file')->getClientOriginalName(), 'UserUpload');
            return public_path('user_uploads').$result;
        }
        catch (\Exception $e) {
            return $e;
        }

    }
    public function parentFile(Request $request)
    {
        $path = public_path('user_upload');
        $files = scandir($path."/".auth()->id());
        array_splice($files, 0, 2 );
        return response()->json(["files" => $files]);
    }

    public function childFile(Request $request)
    {
        $path = public_path('user_upload');
        $files = scandir($path."/".auth()->id()."/".$request->directory);
        array_splice($files, 0, 2 );
        return response()->json(["files" => $files]);
    }

    public function deleteFile(Request $request){
        // $result = rmdir( storage_path('/app/UserUpload/'.auth()->id().'/'.$request->directory));

        if( is_dir(storage_path('/app/UserUpload/'.auth()->id().'/'.$request->directory))){
            File::deleteDirectory(storage_path('/app/UserUpload/'.auth()->id().'/'.$request->directory));  
        }
        else {
            unlink( storage_path('/app/UserUpload/'.auth()->id().'/'.$request->directory));
        }
        // File::deleteDirectory(storage_path('/app/UserUpload/'.auth()->id().'/'.$request->directory));    

        return response()->json(["message"=> "File Deleted!" , "result" => $request ]);
    }

}
