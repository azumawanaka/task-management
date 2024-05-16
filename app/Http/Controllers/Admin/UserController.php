<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $limit = request('limit') ?? 10;
        $query = request('query') ?? '';
        $users = User::where('id', '!=', auth()->user()->id)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'like', "%".$query."%")
                                 ->orWhere('email', 'like', '%'.$query.'%')
                                 ->orWhere('created_at', 'like', '%'.$query.'%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

        return response()->json($users);
    }

    public function store(UserRequest $userRequest)
    {
        return User::create([
            'name' => $userRequest->name,
            'email' => $userRequest->email,
            'password' => bcrypt($userRequest->password),
        ]);
    }

    public function update(User $user, UserUpdateRequest $userUpdateRequest)
    {
        $user->update([
            'name' => $userUpdateRequest->name,
            'email' => $userUpdateRequest->email,
            'password' => $userUpdateRequest->password ? bcrypt($userUpdateRequest->password) : $user->password,
        ]);

        return $user;
    }

    public function checkEmail(): JsonResponse
    {
        $userId = request('user_id');
        $user = User::find($userId);

        $email = request('email');
        $query = User::where('email', $email);

        if ($user) {
            $query->where('email', '!=', $user->email);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }


    public function search()
    {
        $limit = request('limit') ?? 10;
        $query = request('query');
        $users = User::where('id', '!=', auth()->user()->id)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'like', "%".$query."%")
                                 ->orWhere('email', 'like', '%'.$query.'%')
                                 ->orWhere('created_at', 'like', '%'.$query.'%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

        return response()->json($users);
    }

    public function bulkDelete()
    {
        User::whereIn('id', request('ids'))->delete();
        return response()->json(['message' => 'Selected data was successfully deleted!']);
    }
}
