<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $limit = request('limit') ?? 10;
        $query = request('query') ?? '';
        $tasks = Task::where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('title', 'like', "%".$query."%")
                                 ->orWhere('status', $query);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

        return response()->json($tasks);
    }

    public function store(TaskRequest $taskRequest)
    {
        $task = Task::create([
            'user_id' => auth()->user()->id,
            'title' => $taskRequest->title,
            'content' => $taskRequest->content,
            'status' => $taskRequest->status,
            'is_published' => $taskRequest->is_published,
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
            'is_published' => $updateTaskRequest->is_published,
            'files' => $existingImages,
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
        $task->delete();
        return response()->noContent();
    }

    public function bulkDelete()
    {
        Task::whereIn('id', request('ids'))->delete();
        return response()->json(['message' => 'Selected task was successfully deleted!']);
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }
}
