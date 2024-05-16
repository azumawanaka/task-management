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
        $sortColumn = request('sortColumn') ?? 'created_at';
        $sortOrder = request('sortOrder') ?? 'desc';
        $filterBy = request('filterBy') ?? '';
        // $toggleBy = request('toggleBy') ?? 'is_published';
        $query = request('query') ?? '';

        $authenticatedUser = auth()->user();    
        $tasks = $authenticatedUser->tasks()->newQuery()
                ->where(function ($queryBuilder) use ($query, $filterBy) {
                    if (empty($filterBy)) {
                        $queryBuilder->where('title', 'like', "%".$query."%");
                    } else {
                        $queryBuilder->where('status', $filterBy)
                                     ->where('title', 'like', "%".$query."%");
                    }
                });

        // $isPublished = $toggleBy === 'is_published';
        // $tasks->where('is_published', $isPublished)
        $tasks->orderBy($sortColumn , $sortOrder);

        return $tasks;
    }
}
