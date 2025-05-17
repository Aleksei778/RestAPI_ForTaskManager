<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Task;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|opened,closed,processed',
        ]);

        $task = auth()->user()->tasks()->create($request->all());

        return response()->json([
            'task' => new TaskResource($task),
            'message'=> 'Task created successfully',
        ], 201);
    }

    public function index(Request $request) {
        $query = auth()->user()->tasks();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->get();

        return TaskResource::collection($tasks);
    }

    public function update(Request $request, Task $task) {
        if (auth()->id() !== $task->user_id) {
            return response()->json([
                'message' => 'has not permission to update this task.'
            ], 403);
        }
        
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|opened,closed,processed',
        ]);
        
        $task->update($request->all());
            return response()->json([
                'task' => new TaskResource($task),
                'message' => "Task $task->id updated"
            ]);
    }

    public function destroy(Task $task) {
        if (auth()->id() !== $task->user_id) {
            return response()->json([
                'message' => 'has not permission to delete this task.'
            ], 403);
        }
        
        $task->delete();
        return response()->json([
            'message' => "Task $task->id deleted"
        ]);
    }
}
