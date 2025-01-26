<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class dashboardController extends Controller
{
public function index(){

    $id_pt = Auth::user()->id_pt;

    $image = DB::table('public.tbl_pt')
    ->where('id_pt', $id_pt)
    ->first();

    if ($image && !empty($image->image)) {
         // Check the type of the image data
         if (is_resource($image->image)) {
            // If it's a resource, you need to convert it to a string
            $binaryData = stream_get_contents($image->image);
        } else {
            // Otherwise, use it directly
            $binaryData = $image->image;
        }

        // Check if binaryData is a string
        if (is_string($binaryData)) {
            // Convert binary data to base64
            $base64Image = base64_encode($binaryData);

        return view('dashboard.view', [
            'base64Image' => $base64Image,
            'mimeType' => $image->mime_type, // Retrieve the MIME type
            'imgname' => $image->imgname, 
        ]);
    }
        return redirect()->route('report-initial-recognition.index', ['id_pt' => $id_pt]); // Redirect to report
}
}

public function uploadImage(Request $request)
{
    return view('superadmin.dashboard.layout');

}

public function store(Request $request)
{

    $id_pt = Auth::user()->id_pt;

    // Validate the image
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Store the image in the 'public/images' directory
    // $path = file_get_contents($request->file('image')->getRealPath());

    // Process the new image
    if ($request->hasFile('image')) {
        $file = $request->file('image');

        // Debugging: Check if the file is valid
        if (!$file->isValid()) {
            return back()->with('error', 'Uploaded file is not valid.');
        }

        $contents = file_get_contents($file->getRealPath());

        // Check if contents are retrieved correctly
        if ($contents === false) {
            return back()->with('error', 'Failed to read the file contents.');
        }

    $user = DB::table('public.tbl_pt')
    ->where('id_pt', $id_pt)
    ->update([ 
    'imgname' => $file->getClientOriginalName(),
    'image' => DB::raw("decode('".bin2hex($contents)."', 'hex')"), // Assuming this is a bytea column
    'mime_type' => $file->getClientMimeType(), 
    ]); // Save the image path
   
    if ($user) {
        return back()->with('success', 'Image uploaded successfully!');
    } else {
        return back()->with('error', 'Failed to update the database.');
    } 
    // Return a success response
    // return back()->with('success', 'Image uploaded successfully!')->with('image', $path);
}
return back()->with('error', 'No image file was uploaded.');
}
}