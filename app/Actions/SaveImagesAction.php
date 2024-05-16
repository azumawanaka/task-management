<?php

namespace App\Actions;

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
                $path = $image->store('tasks', 'public');
                $imagePaths[] = Storage::url($path); 
            }
        }

        return $imagePaths;
    }
}
