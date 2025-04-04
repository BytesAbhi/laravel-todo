<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\UserEmail;
use App\Mail\TaskReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    protected $signature = 'send:task-reminders';

    protected $description = 'Send email reminders 1 hour before task start time';

    public function handle()
    {
        $targetTime = Carbon::now()->addHour()->format('Y-m-d H:i:00');

        $tasks = Task::where('is_completed', false)
                    ->where('task_time', $targetTime)
                    ->get();

        $emails = UserEmail::pluck('email'); // get all saved emails

        foreach ($tasks as $task) {
            foreach ($emails as $email) {
                Mail::to($email)->send(new TaskReminder($task));
            }
        }

        $this->info('Reminders sent successfully!');
    }
}