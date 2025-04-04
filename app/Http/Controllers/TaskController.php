<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return response()->json($tasks);
    }

  public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'title' => 'required|max:255',
        'objective' => 'nullable|string',
        'image' => 'nullable|image',
        'task_time' => 'nullable',
        'duration' => 'nullable'
    ]);


if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imagePath = $image->store('tasks', 'public');
        $validated['image'] = $imagePath;
    }
    
    $existing = Task::where('title', $validated['title'])->first();

    if ($existing) {
        
        return response()->json([
            'error' => 'A task with this title already exists.'
        ], 409);
    }

    
    Task::create($validated);

    return response()->json(['message' => 'Task added successfully'], 201);
}


    public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'objective' => 'nullable|string',
        'task_time' => 'nullable|date',
        'duration' => 'nullable|integer',
    ]);

    $task = Task::findOrFail($id);
    $task->update([
        'title' => $request->title,
        'objective' => $request->objective,
        'task_time' => $request->task_time,
        'duration' => $request->duration,
    ]);

    return response()->json(['message' => 'Task updated successfully']);
}



    public function updateCompletion(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->is_completed = $request->is_completed;
        $task->save();

        return response()->json(['message' => 'Task updated successfully!']);
    }


    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if ($task->image) {
            Storage::disk('public')->delete($task->image);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
