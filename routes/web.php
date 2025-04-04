<?php


use App\Models\UserEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});





Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return "Storage link has been created successfully.";
});

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::post('/tasks/{id}', [TaskController::class, 'update']);
Route::put('/tasks/{id}/complete', [TaskController::class, 'updateCompletion']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);


// sending Task reminder emails

Route::post('/save-user-email', function (Request $request) {
    $request->validate([
        'email' => 'required|email'
    ]);

    UserEmail::create([
        'email' => $request->email
    ]);

    return response()->json(['message' => 'Email saved']);
});