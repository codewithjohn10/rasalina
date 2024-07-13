<?php

namespace App\Http\Controllers\Home;

use App\Models\HomeSlide;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;

class HomeSliderController extends Controller
{
    //
    public function HomeSlider()
    {
        $homeSlide = HomeSlide::find(1);
        return view('admin.home_slide.home_slide_all', compact('homeSlide'));
    }

    public function UpdateSlider(Request $request)
    {
        $slideId = $request->id;
        if ($request->file('home_slide')) {
            $width = 636; // Maximum width for the image
            $height = 852; // Maximum height for the image

            $currentTimestamp = time(); // Get the current timestamp, e.g., 1631703954
            $uploadedFile = $request->file('home_slide'); // Retrieve the uploaded file from the request
            $extension = $uploadedFile->getClientOriginalExtension(); // Get the original file extension

            // Generate a slug from the title
            $imageSlug = Str::slug($request->title);

            // Construct the file name using the slug, timestamp, and original extension
            $fileName = $imageSlug . '-' . $currentTimestamp . '.' . $extension;
            $originalPublicDir = 'uploads/home_slide/' . $fileName; // Define the path to save the file

            // Create an instance of the image from the uploaded file and correct its orientation
            $img = Image::make($uploadedFile)->orientate();

            // Determine whether to set width or height to null for aspect ratio resizing
            $img->height() > $img->width() ? ($width = null) : ($height = null);

            // Resize the image while maintaining the aspect ratio
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });

            // Save the resized image to the specified path in the public directory
            $img->save(public_path($originalPublicDir));

            HomeSlide::findOrFail($slideId)->update([
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'video_url' => $request->video_url,
                'home_slide' => $originalPublicDir,
            ]);

            $notification = [
                'message' => 'Home Slide Updated with image Successfully.',
                'alert-type' => 'success',
            ];

            return redirect()->back()->with($notification);
        } else {
            HomeSlide::findorfail($slideId)->update([
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'video_url' => $request->video_url,
            ]);

            $notification = [
                'message' => 'Home Slide Updated without image Successfully.',
                'alert-type' => 'success',
            ];

            return redirect()->back()->with($notification);
        }
    }
}
