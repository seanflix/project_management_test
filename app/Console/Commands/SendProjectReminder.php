<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ProjectReminderMail;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendProjectReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:projects-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for projects due in 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projects = Project::whereDate('due_date', Carbon::tomorrow())
                    ->select(['id', 'title', 'team_id'])
                    ->with([
                        'team' => function ($query) {
                            $query->select('id', 'user_id')->with('user:id,name,email'); // Load the owner
                        }
                    ])
                    ->get();

        foreach ($projects as $project) {
            $user = $project->team->user ?? null;

            if (!$user || !$user->email) {
                Log::warning("Skipping project ID {$project->id}: No team owner or email found.");
                continue;
            }

            Log::debug("Sending email to: {$user->email} for project: {$project->title}");

            Mail::to($user->email)->send(new ProjectReminderMail($project));
        }

        $this->info('Project reminder emails sent.');
    }
}
