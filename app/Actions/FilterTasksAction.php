<?php

namespace App\Actions;

use App\Models\Task;

class FilterTasksAction
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function execute()
    {
        $limit = request('limit') ?? 10;
        $sortColumn = request('sortColumn') ?? 'created_at';
        $sortOrder = request('sortOrder') ?? 'desc';
        $filterBy = request('filterBy') ?? '';
        $query = request('query') ?? '';

        $authenticatedUser = auth()->user();    
        $tasksQuery = $authenticatedUser->tasks()->newQuery()
                ->where(function ($queryBuilder) use ($query, $filterBy) {
                    if (empty($filterBy)) {
                        $queryBuilder->where('title', 'like', "%".$query."%");
                    } else {
                        $queryBuilder->where('status', $filterBy)
                                     ->where('title', 'like', "%".$query."%");
                    }
                })
                ->whereNull('parent_id')
                ->orderBy($sortColumn , $sortOrder);

        $tasks = $tasksQuery->paginate($limit);

        // Eager load subtasks for each task
        $tasks->load('subtasks');

        return $tasks;
    }
}
