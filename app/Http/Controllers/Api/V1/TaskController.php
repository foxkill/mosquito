<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: check if the user is available.
        $user = auth()->user();
        
        $data = request()->only('title', 'description', 'state');

        // TODO: validate the data.
        // make sure the state is set to: "todo".
        return Task::create(
            array_merge($data, ['user_id' => $user->id])
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Task $task)
    {
        $data = request()->only('title', 'description', 'state');

        return $task->update($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent(); 
    }
}
