# Project Management Web App
This is a test project for exploring project management features and best practices. It includes task tracking, team assignments, deadlines, and simplified sorting/filtering to enhance productivity and collaboration. All fixes, updates, and development steps are thoroughly documented, ensuring a clear understanding of the project's implementation process.


## Quick Fixes
**Error:**
    ```
    SQLSTATE[HY000]: General error: 1005 Can't create table `dev_test_project`.`projects` (errno: 150 "Foreign key constraint is incorrectly formed") (Connection: mysql, SQL: alter table `projects` add constraint `projects_team_id_foreign` foreign key (`team_id`) references `teams` (`id`) on delete cascade)
    ```
> **Fix:** Re-ordered list of created migration files in correct order to fix foreign key constraint errors during migration.  

**Error:** When clicking the "Complete" button in tasks lists, console error ```Title is required``` appears.  
> **Fix:** Made some adjustments to the PUT request method to fix the validation error.


## Summary of Created Steps
- [x] Added sorting functionality (asc/desc) using frontend scripts to reduce server request/load.
- [x] Added search filtering for project name/title.
- [x] Added filtering in project status and tasks.
- [x] Added https://vue3datepicker.com/ package for date range filtering and added functionality.
- [x] Made minor adjustments to ProjectPolicy to strictly follow the assigned roles(admin/editor) of users from a team.
- [x] Modified Frontend, Controllers, and Models to implement task assigninment to users.
- [x] Added a basic reminder email functionality which can be run using cronjobs. The functionality can also be tested using command ```php artisan send:project-reminder-test```.

## Detailed Summary of Changes
- [x] Installed package `@vuepic/vue-datepicker` for date range filtering in sorting projects/tasks. Also, added these on `app.js` for registering the component as global.
    **Installation:**
    ```npm
    npm install @vuepic/vue-datepicker
    ```
    **Import/Globalization:**
    ```javascript
    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css';
    ```
    ```javascript
    .component('VueDatePicker', VueDatePicker)
    ```
- [x] Slight modification in `ProjectController.php` for fetching user team members for user assignment in task list.
    FROM:
    ```php
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['tasks' => function ($query) {
            $query->latest();
        }]);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'tasks' => $project->tasks,
            'taskStats' => [
                'total' => $project->tasks->count(),
                'completed' => $project->tasks->where('status', 'completed')->count(),
            ],
        ]);
    }
    ```
    TO:
    ```php
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['tasks' => function ($query) {
            $query->latest()->with('user');
        }]);

        $project->isAuthorized = auth()->user()->can('delete', $project);
        $teamUsers = $project->team->users()->get();

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'tasks' => $project->tasks,
            'taskStats' => [
                'total' => $project->tasks->count(),
                'completed' => $project->tasks->where('status', 'completed')->count(),
            ],
            'teamUsers' => $teamUsers,
        ]);
    }
    ```
- [x] Added `'user_id' => ['nullable', 'exists:users,id']` in the `store` and `update` functions in `TaskController.php` for user assignment functionality.
- [x] Added a relationship method in `Team` model for team ownership.
    ```php
    /**
     * Get team owner.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    ```
- [x] Slight adjustment in `ProjectPolicy.php` permissions for strict user role security.
    FROM:
    ```php
    public function delete(User $user, Project $project): bool
    {
        return $user->belongsToTeam($project->team);
    }
    ```
    TO:
    ```php
    public function delete(User $user, Project $project): bool
    {
        return $user->belongsToTeam($project->team) && 
            $user->hasTeamPermission($project->team, 'delete');
    }
    ```
- [x] Modified `Projects/Index.vue` template and scripts for the additional functionality added such as sorting and filtering.
    **Variables:**
    ```javascript
    const filters = ref({
        titleSearch: "",
        status: "",
        tasks: "",
        date: "",
    });

    const sortBy = ref("created_at");
    const sortOrder = ref("desc");
    ```
    **Sorting Toggle Method:**
    ```javascript
    function toggleSort(column) {
        if (sortBy.value === column) {
            sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
        } else {
            sortBy.value = column;
            sortOrder.value = "asc";
        }
    }
    ```
    **Filtered Projects Computed Method for Filtered or Sorted Results:**
    ```javascript
    const filteredProjects = computed(() => {
        return props.projects.filter((project) => {
            let matchesTitle = true;
            let matchesStatus = true;
            let matchesTasks = true;
            let matchesDate = true;

            if (filters.value.titleSearch && !project.title.toLowerCase().includes(filters.value.titleSearch.toLowerCase())) {
                matchesTitle = false;
            }

            if (filters.value.status && project.status !== filters.value.status) {
                matchesStatus = false;
            }

            if (filters.value.tasks === "0" && project.tasks_count > 0) {
                matchesTasks = false;
            } else if (filters.value.tasks === "1" && project.tasks_count === 0) {
                matchesTasks = false;
            }

            if (filters.value.date[0] || filters.value.date[1]) {
                const projectDueDate = new Date(project.due_date);
                const startDate = filters.value.date[0] ? new Date(filters.value.date[0]) : null;
                const endDate = filters.value.date[1] ? new Date(filters.value.date[1]) : null;

                projectDueDate.setHours(0, 0, 0, 0);
                if (startDate) startDate.setHours(0, 0, 0, 0);
                if (endDate) endDate.setHours(23, 59, 59, 999);

                if (startDate && projectDueDate < startDate) {
                    matchesDate = false;
                }
                if (endDate && projectDueDate > endDate) {
                    matchesDate = false;
                }
            } else {
                filters.value.date = ""
            }

            return matchesTitle && matchesStatus && matchesTasks && matchesDate;
        }).sort((a, b) => {
            let modifier = sortOrder.value === "asc" ? 1 : -1;
            let aValue = a[sortBy.value];
            let bValue = b[sortBy.value];

            if (sortBy.value === "created_at") {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            } 

            else if (sortBy.value === "due_date") {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            } 
            
            else if (sortBy.value === "tasks") {
                aValue = a.tasks_count || 0;
                bValue = b.tasks_count || 0;
            } 
            
            else if (sortBy.value === "title") {
                return aValue.localeCompare(bValue, undefined, {
                    numeric: true,
                    sensitivity: "base",
                }) * modifier;
            }

            if (aValue < bValue) return -1 * modifier;
            if (aValue > bValue) return 1 * modifier;
            return 0;
        });
    });
    ```
- [x] Modified `Projects/Show.vue` template and scripts for the additional functionality added such as user assignment, sorting and filtering.
    **Variables and Props:**
    ```javascript
    const props = defineProps({
        project: {
            type: Object,
            required: true,
        },
        tasks: {
            type: Array,
            required: true,
        },
        taskStats: {
            type: Object,
            required: true,
        },
        selectedTask: {
            type: Object,
            required: false,
            default: null,
        },
        teamUsers: {
            type: Array,
            required: true,
        },
    });

    const filters = ref({
        search: "",
        status: "",
        priority: "",
        completion_date: "",
    });

    const priorityMap = ref({
        low: 1,
        medium: 2,
        high: 3
    });

    const sortBy = ref("created_at");
    const sortOrder = ref("desc");
    ```
    **Sorting + Filtering Methods:**
    ```javascript
    function toggleSort(column) {
        if (sortBy.value === column) {
            sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
        } else {
            sortBy.value = column;
            sortOrder.value = "asc";
        }
    }

    const filteredTasks = computed(() => {
        return props.tasks.filter((task) => {
            let matchesTitle = true;
            let matchesUser = true;
            let matchesStatus = true;
            let matchesPriority = true;
            let matchesDate = true;

            const searchLower = filters.value.search.toLowerCase();

            if (filters.value.search) {
                const taskTitleMatches = task.title && task.title.toLowerCase().includes(searchLower);
                let userMatches = false;

                if (task.user) {
                    const userNameLower = task.user.name.toLowerCase();
                    const userEmailLower = task.user.email.toLowerCase();
                    userMatches = userNameLower.includes(searchLower) || userEmailLower.includes(searchLower);
                }

                if (!taskTitleMatches && !userMatches) {
                    return false; // Skip this task since it doesn't match title or user
                }
            }

            if (filters.value.status && task.status !== filters.value.status) {
                matchesStatus = false;
            }

            if (filters.value.priority && task.priority !== filters.value.priority) {
                matchesPriority = false;
            }

            if (filters.value.completion_date[0] || filters.value.completion_date[1]) {
                const taskCompletionDate = new Date(task.completion_date);
                const startDate = filters.value.completion_date[0] ? new Date(filters.value.completion_date[0]) : null;
                const endDate = filters.value.completion_date[1] ? new Date(filters.value.completion_date[1]) : null;

                taskCompletionDate.setHours(0, 0, 0, 0);
                if (startDate) startDate.setHours(0, 0, 0, 0);
                if (endDate) endDate.setHours(23, 59, 59, 999);

                if (startDate && taskCompletionDate < startDate) {
                    matchesDate = false;
                }
                if (endDate && taskCompletionDate > endDate) {
                    matchesDate = false;
                }
            } else {
                filters.value.date = ""
            }

            return matchesTitle && matchesUser && matchesStatus && matchesPriority && matchesDate;
        }).sort((a, b) => {
            let modifier = sortOrder.value === "asc" ? 1 : -1;
            let aValue = a[sortBy.value];
            let bValue = b[sortBy.value];

            if (sortBy.value === "created_at") {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            } 
            
            else if (sortBy.value === "assignee") {
                aValue = a.user ? a.user.name : "";
                bValue = b.user ? b.user.name : "";
            } 

            else if (sortBy.value === "completion_date") {
                aValue = new Date(aValue);
                bValue = new Date(bValue);
            } 
            
            else if (sortBy.value === "priority") {
                aValue = priorityMap.value[aValue] || 0;
                bValue = priorityMap.value[bValue] || 0;
            } 
            
            else if (sortBy.value === "title") {
                return aValue.localeCompare(bValue, undefined, {
                    numeric: true,
                    sensitivity: "base",
                }) * modifier;
            }

            if (aValue < bValue) return -1 * modifier;
            if (aValue > bValue) return 1 * modifier;
            return 0;
        });
    });
    ```
    **Mark As Completed method functionality error fix:**
    ```javascript
    const markAsCompleted = (task) => {
        taskForm.id = task.id;
        taskForm.title = task.title;
        taskForm.description = task.description;
        taskForm.status = "completed";
        taskForm.priority = task.priority;
        taskForm.completion_date = task.completion_date;
        taskForm.user_id = task.user_id;

        taskForm.put(route("tasks.update", task.id),{
            preserveScroll: true,
            onSuccess: () => {
                taskForm.reset({
                    id: null,
                    title: "",
                    description: "",
                    status: "not_started",
                    priority: "medium",
                    user_id: null,
                });
            },
            onError: (errors) => {
                console.error(errors); // Log errors or show them in the UI
            },
        });
    };
    ```
- [x] Created `SendProjectReminder.php` for the send email command `php artisan send:project-reminder` used for cronjobs.
- [x] Created `SendProjectReminderTest.php` for the send email command `php artisan send:project-reminder-test` used for manual functionality testing.
- [x] Created `ProjectReminderMail.php` mail file the email structure template.
- [x] Created `project-reminder.blade.php` blade file for the email template.
- [x] Added commands to console for the email reminder functionality:
    ```php
    use App\Console\Commands\SendProjectReminder;
    use App\Console\Commands\SendProjectReminderTest;

    Artisan::command('send:project-reminder', function () {
        SendProjectReminder::dispatch();
        $this->info('Reminder emails sent!');
    })->purpose('Send reminder email for projects due in 24 hours')->daily();

    Artisan::command('send:project-reminder-test', function () {
        // Call the test command via Artisan
        $this->call(SendProjectReminderTest::class);
        $this->info('Reminder emails sent without queues!');
    })->purpose('Send reminder email for projects due in 24 hours (no queue)')->daily();
    ```
- [x] Added `user_id` in fillable fields on the `Task` model for task ownership. Also this function for easy data access based on relationship:
    ```php
    public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }
    ```
- [x] Slight modification in `ProjectController.php` for user restrictions based on roles.
    FROM:
    ```php
    public function index()
    {
        $projects = Auth::user()->currentTeam->projects()
            ->withCount('tasks')
            ->latest()
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }
    ```
    TO:
    ```php
    public function index()
    {
        $projects = Auth::user()->currentTeam->projects()
            ->withCount('tasks')
            ->latest()
            ->get()
            ->map(function ($project) {
                $project->isAuthorized = auth()->user()->can('delete', $project);
                return $project;
            });

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }
    ```
