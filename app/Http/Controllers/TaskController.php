<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TaskController extends Controller
{
    public function index()
    {
        $limit = request('limit') ?? 10;
        $sortColumn = request('sortColumn') ?? 'created_at';
        $sortOrder = request('sortOrder') ?? 'desc';
        $filterBy = request('filterBy') ?? '';
        $toggleBy = request('toggleBy') ?? 'is_published';
        $query = request('query') ?? '';
        $tasks = Task::where(function ($queryBuilder) use ($query, $filterBy) {
                    if (empty($filterBy)) {
                        $queryBuilder->where('title', 'like', "%".$query."%");
                    } else {
                        $queryBuilder->where('status', $filterBy)
                                     ->where('title', 'like', "%".$query."%");
                    }
                });

        $isPublished = $toggleBy === 'is_published';
        $tasks->where('is_published', $isPublished)
            ->orderBy($sortColumn , $sortOrder);
        
        return response()->json($tasks->paginate($limit));
    }

    public function store(TaskRequest $taskRequest)
    {
        $task = Task::create([
            'user_id' => auth()->user()->id,
            'title' => $taskRequest->title,
            'content' => $taskRequest->content,
            'status' => $taskRequest->status,
            'is_published' => $taskRequest->is_published == 'is_published' ? true : false,
        ]);

        $imagePaths = [];
        if ($taskRequest->hasFile('images')) {
            foreach ($taskRequest->file('images') as $image) {
                $path = $image->store('tasks', 'public');
                $imagePaths[] = Storage::url($path); 
            }
        }

        $task->files = $imagePaths;
        $task->save();

        return $task;
    }

    public function update(Task $task, UpdateTaskRequest $updateTaskRequest)
    {
        $existingImages = $updateTaskRequest->existingImages ?? [];
        $task->update([
            'title' => $updateTaskRequest->title,
            'content' => $updateTaskRequest->content,
            'status' => $updateTaskRequest->status,
            'is_published' => $updateTaskRequest->is_published == 'is_published' ? true : false,
        ]);

         $imagePaths = [];
         if ($updateTaskRequest->hasFile('images')) {
             foreach ($updateTaskRequest->file('images') as $image) {
                $path = $image->store('tasks', 'public');
                $imagePaths[] = Storage::url($path);
             }
         }

        $allImages = array_merge($existingImages, $imagePaths);

        $task->files = $allImages;
        $task->save();

        return $task;
    }

    public function destroy(Task $task)
    {
        $this->deleteFiles($task);

        $task->delete();
        return response()->noContent();
    }

    public function bulkDelete()
    {
        $ids = request('ids');
        $tasks = Task::whereIn('id', $ids); 

        foreach ($tasks->get() as $task) {
            $this->deleteFiles($task);
        }

        $tasks->delete();
        return response()->json(['message' => 'Selected task was successfully deleted!']);
    }

    private function deleteFiles($task)
    {
        foreach ($task->files as $relativePath) {
            $absolutePath = public_path($relativePath);
            
            if (file_exists($absolutePath)) {
                // Attempt to delete the file
                if (unlink($absolutePath)) {
                    \Log::info("File deleted: $relativePath");
                } else {
                    \Log::error("Failed to delete file: $relativePath");
                }
            } else {
                \Log::error("File not found: $relativePath");
            }
        }
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }
}
