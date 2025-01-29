<template>
    <AppLayout :title="project.title">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ project.title }}
                </h2>
                <Link :href="route('projects.index')"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                Back to Projects
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Project Details -->
                <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Project Details
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Description:</span>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">
                                        {{
                                            project.description ||
                                            "No description"
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100':
                                                project.status ===
                                                'not_started',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100':
                                                project.status ===
                                                'in_progress',
                                            'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100':
                                                project.status === 'completed',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300':
                                                project.status === 'on_hold',
                                        }">
                                        {{ formatStatus(project.status) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Due Date:</span>
                                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{
                                        formatDate(project.due_date)
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Task Summary
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ taskStats.total }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Total Tasks
                                    </div>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                    <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                                        {{ taskStats.completed }}
                                    </div>
                                    <div class="text-sm text-green-500 dark:text-green-400">
                                        Completed
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Tasks
                        </h3>
                        <PrimaryButton @click="createTask"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Add Task
                        </PrimaryButton>
                    </div>

                    <div v-if="tasks.length" class="flex justify-between items-center mb-4">
                        <div>
                            <InputLabel for="search-filter" value="Search" />
                            <input id="search-filter" type="text" v-model="filters.search" placeholder="Search Tasks" class="text-sm mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"/>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div>
                                <InputLabel for="status-filter" value="Status" />
                                <select id="status-filter" v-model="filters.status" class="text-sm mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Statuses</option>
                                    <option value="not_started">Not Started</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="on_hold">On Hold</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel for="priority-filter" value="Priority" />
                                <select id="priority-filter" v-model="filters.priority" class="text-sm mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel value="Date" />
                                <VueDatePicker v-model="filters.completion_date" range :enable-time-picker="false" @cleared="clearDateFilter" placeholder="Select date range" class="mt-1 shadow-sm"/>
                            </div>
                        </div>
                    </div>

                    <div v-if="tasks.length === 0" class="text-center py-12">
                        <div class="text-gray-500 dark:text-gray-400 mb-2">
                            No tasks found
                        </div>
                        <div class="text-sm text-gray-400 dark:text-gray-500">
                            Get started by adding your first task
                        </div>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button @click="toggleSort('title')" class="flex items-center space-x-2 cursor-pointer">
                                            Title
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button @click="toggleSort('assignee')" class="flex items-center space-x-2 cursor-pointer">
                                            Assignee
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button @click="toggleSort('status')" class="flex items-center space-x-2 cursor-pointer">
                                            Status
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button @click="toggleSort('priority')" class="flex items-center space-x-2 cursor-pointer">
                                            Priority
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button @click="toggleSort('completion_date')" class="flex items-center space-x-2 cursor-pointer">
                                            Completion Date
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="task in filteredTasks" :key="task.id">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ task.title }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ task.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <img :src="task.user_id ? task.user.profile_photo_url : ''" class="h-8 rounded-full" alt="">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ task.user_id ? task.user.name : 'No Assignee' }}
                                                </div>
                                                <div v-if="task.user_id" class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ task.user_id ? task.user.email : 'No Assignee' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100':
                                                    task.status ===
                                                    'not_started',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100':
                                                    task.status ===
                                                    'in_progress',
                                                'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100':
                                                    task.status === 'completed',
                                            }">
                                            {{ formatStatus(task.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300':
                                                    task.priority === 'low',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100':
                                                    task.priority === 'medium',
                                                'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100':
                                                    task.priority === 'high',
                                            }">
                                            {{
                                                task.priority
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                task.priority.slice(1)
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{
                                            task.completion_date
                                                ? formatDate(
                                                    task.completion_date
                                                )
                                                : "Not completed"
                                        }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                        <button v-if="task.status !== 'completed'" @click="markAsCompleted(task)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-500 dark:text-green-400 bg-white dark:bg-gray-800 hover:text-green-700 dark:hover:text-green-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                            Complete
                                        </button>
                                        <button @click="editTask(task)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                            Edit
                                        </button>
                                        <button v-if="project.isAuthorized" @click="deleteTask(task)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-500 dark:text-red-400 bg-white dark:bg-gray-800 hover:text-red-700 dark:hover:text-red-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Modal -->
        <Modal :show="showTaskModal" @close="closeTaskModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ taskForm.id ? "Edit Task" : "Create Task" }}
                </h2>

                <div class="mt-6">
                    <form @submit.prevent="submitTaskForm">
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="taskForm.title"
                                required />
                            <InputError :message="taskForm.errors.title" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="description" value="Description" />
                            <textarea id="description"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="taskForm.description" rows="3"></textarea>
                            <InputError :message="taskForm.errors.description" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="assignee" value="Assignee" />
                            <select id="assignee"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="taskForm.user_id" required>
                                <option disabled>Select a user</option>
                                <option v-for="user in teamUsers" :value="user.id" :key="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                            <InputError :message="taskForm.errors.user_id" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="status" value="Status" />
                            <select id="status"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="taskForm.status" required>
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            <InputError :message="taskForm.errors.status" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="priority" value="Priority" />
                            <select id="priority"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="taskForm.priority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <InputError :message="taskForm.errors.priority" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <SecondaryButton @click="closeTaskModal" class="mr-3">
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton :disabled="taskForm.processing">
                                {{ taskForm.id ? "Update" : "Create" }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<style>
    :root {
        --dp-input-padding: 6px 30px 6px 12px;
        --dp-border-radius: 6px;
        --dp-border-color: #d1d5db;
        --dp-font-size: 0.900rem;
    }
</style>

<script setup>
    import { ref, computed } from "vue";
    import { useForm, Link } from "@inertiajs/vue3";
    import AppLayout from "@/Layouts/AppLayout.vue";
    import Modal from "@/Components/Modal.vue";
    import InputLabel from "@/Components/InputLabel.vue";
    import TextInput from "@/Components/TextInput.vue";
    import InputError from "@/Components/InputError.vue";
    import PrimaryButton from "@/Components/PrimaryButton.vue";
    import SecondaryButton from "@/Components/SecondaryButton.vue";

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

    const clearDateFilter = () => {
        filters.value.completion_date = ""
    }

    const showTaskModal = ref(false);

    const taskForm = useForm({
        id: null,
        title: "",
        description: "",
        status: "not_started",
        priority: "medium",
        completion_date: null,
        user_id: null,
    });

    const createTask = () => {
        taskForm.reset();
        showTaskModal.value = true;
    };

    const editTask = (task) => {
        taskForm.id = task.id;
        taskForm.title = task.title;
        taskForm.description = task.description;
        taskForm.status = task.status;
        taskForm.priority = task.priority;
        taskForm.completion_date = task.completion_date;
        taskForm.user_id = task.user_id;
        showTaskModal.value = true;
    };

    const closeTaskModal = () => {
        showTaskModal.value = false;
        taskForm.reset();
    };

    const submitTaskForm = () => {
        if (taskForm.id) {
            taskForm.put(route("tasks.update", taskForm.id), {
                preserveScroll: true,
                onSuccess: () => closeTaskModal(),
            });
        } else {
            taskForm.post(route("projects.tasks.store", props.project.id), {
                preserveScroll: true,
                onSuccess: () => closeTaskModal(),
            });
        }
    };

    const deleteTask = (task) => {
        if (confirm("Are you sure you want to delete this task?")) {
            taskForm.delete(route("tasks.destroy", task.id), {
                preserveScroll: true,
            });
        }
    };

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

    const formatStatus = (status) => {
        return status
            .split("_")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ");
    };

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString();
    };
</script>
