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
        $request->validate([
            'title' => 'required|string|max:255|unique:tasks',
            'objective' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'task_time' => 'nullable|date_format:Y-m-d H:i:s',
            'duration' => 'nullable|integer|min:1',
        ]);

        $task = new Task();
        $task->title = $request->title;
        $task->objective = $request->objective;
        $task->duration = $request->duration;
        $task->task_time = $request->task_time;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tasks', 'public');
            $task->image = $imagePath;
        }

        $task->save();

        return response()->json(['message' => 'Task added successfully!']);
    }


    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->title = $request->title;
        $task->objective = $request->objective;
        $task->task_time = $request->task_time;
        $task->duration = $request->duration;

        if ($request->hasFile('image')) {
            if ($task->image) {
                Storage::disk('public')->delete($task->image);
            }
            $task->image = $request->file('image')->store('tasks', 'public');
        }

        $task->save();
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
