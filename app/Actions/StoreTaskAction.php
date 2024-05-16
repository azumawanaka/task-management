<?php

namespace App\Actions;

use App\Models\Task;

class StoreTaskAction
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function execute($request = [])
    {
        return $this->model->create([
                'user_id' => auth()->user()->id,
                'title' => $request['title'],
                'content' => $request['content'],
                'status' => $request['status'],
                'is_published' => $request['is_published'] == 'is_published' ? true : false,
            ]);
    }
}
