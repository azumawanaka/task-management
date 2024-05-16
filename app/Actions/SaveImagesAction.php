<?php

namespace App\Actions;

use App\Jobs\SaveImages;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class SaveImagesAction
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function execute($request)
    {
        $imagePaths = [];
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {
                // Store the file and get the path
                $path = $image->store('temp', 'public');
                
                // Dispatch the job with the file path
                SaveImages::dispatch($path)->onQueue('image-processing');
                
                // Save the URL to the image
                $imagePaths[] = Storage::url($path);
            }
        }

        return $imagePaths;
    }
}
