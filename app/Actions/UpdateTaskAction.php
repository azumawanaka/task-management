<?php

namespace App\Actions;

use App\Models\Task;

class UpdateTaskAction
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function execute(Task $task, $request = [])
    {
        $task->update([
            'title' => $request['title'],
            'content' => $request['content'],
            'status' => $request['status'],
            'is_published' => $request['is_published'] == 'is_published' ? true : false,
        ]);

        return $task;
    }
}
