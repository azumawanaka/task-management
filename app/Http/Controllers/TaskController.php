<?php

namespace App\Http\Controllers;

use App\Actions\FilterTasksAction;
use App\Actions\SaveImagesAction;
use App\Actions\StoreTaskAction;
use App\Actions\UpdateTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TaskController extends Controller
{

    /**
     * @param FilterTasksAction $filterTasksAction
     * 
     * @return JsonResponse
     */
    public function index(FilterTasksAction $filterTasksAction): JsonResponse
    {
        $tasks = $filterTasksAction->execute();
 
        return response()->json($tasks);
    }

    /**
     * @param TaskRequest $taskRequest
     * @param StoreTaskAction $storeTaskAction
     * @param SaveImagesAction $saveImagesAction
     * 
     * @return Task
     */
    public function store(
        TaskRequest $taskRequest,
        StoreTaskAction $storeTaskAction,
        SaveImagesAction $saveImagesAction
    ): Task {
        $task = $storeTaskAction->execute($taskRequest->all());
        $task->files = $saveImagesAction->execute($taskRequest);
        $task->save();

        return $task;
    }

    /**
     * @param Task $task
     * @param UpdateTaskRequest $updateTaskRequest
     * @param UpdateTaskAction $updateTaskAction
     * @param SaveImagesAction $saveImagesAction
     * 
     * @return Task
     */
    public function update(
        Task $task,
        UpdateTaskRequest $updateTaskRequest,
        UpdateTaskAction $updateTaskAction,
        SaveImagesAction $saveImagesAction
    ): Task {
        $existingImages = $updateTaskRequest->existingImages ?? [];

        $updateTaskAction->execute($task, $updateTaskRequest);

        $imagePaths = $saveImagesAction->execute($updateTaskRequest);

        $allImages = array_merge($existingImages, $imagePaths);
        $task->files = $allImages;

        $task->save();

        return $task;
    }

    /**
     * @param Task $task
     * 
     * @return Response
     */
    public function destroy(Task $task): Response
    {
        $this->deleteFiles($task);
        $task->delete();

        return response()->noContent();
    }

    /**
     * @param JsonResponse
     */
    public function bulkDelete(): JsonResponse
    {
        $ids = request('ids');

        foreach ($ids as $t) {
            $task = Task::find($t);
            
            // Check if $task->files is a string
            if (is_string($task->files)) {
                // Assuming $task->files contains comma-separated file paths
                $filePaths = explode(',', $task->files);
                
                // Loop through each file path and delete the file
                foreach ($filePaths as $filePath) {
                    $this->deleteFile($filePath);
                }
            } elseif (is_array($task->files)) {
                // Assuming $task->files is an array of file objects
                foreach ($task->files as $tf) {
                    if (!is_string($tf)) {
                        $this->deleteFiles($tf);
                    }
                }
            }
        }

        Task::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Selected task was successfully moved to trash!']);
    }

    /**
     * @param Task $task
     */
    private function deleteFiles(Task $task)
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

    /**
     * Restore a soft-deleted task.
     *
     * @param int $id
     * @return Response
     */
    public function restore($id): Response
    {
        $task = Task::withTrashed()->findOrFail($id);
        $task->restore();

        return response()->noContent();
    }

    /**
     * @param Task $task
     * 
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        $task = $task->newQuery()->first();
        $task->load('subtasks');

        return response()->json($task);
    }

    /**
     * @param Task $task
     * 
     * @return bool
     */
    public function updatePublishStatus(Task $task): bool
    {
        $is_published = request('is_published') === 'true';
        return $task->update(['is_published' => $is_published]);
    }
}
