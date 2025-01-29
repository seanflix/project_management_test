<template>
    <AppLayout title="Projects">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Projects
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Project List
                            </h3>
                        </div>
                        <PrimaryButton @click="createProject"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Create Project
                        </PrimaryButton>
                    </div>

                    <div v-if="projects.length" class="flex justify-between items-center mb-4">
                        <div>
                            <InputLabel for="title-filter" value="Search" />
                            <input id="title-filter" type="text" v-model="filters.titleSearch" placeholder="Search Projects" class="text-sm mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"/>
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
                                <InputLabel for="tasks-filter" value="Tasks" />
                                <select id="tasks-filter" v-model="filters.tasks" class="text-sm mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Tasks</option>
                                    <option value="0">No Tasks</option>
                                    <option value="1">1+ Tasks</option>
                                </select>
                            </div>
                            <div>
                                <InputLabel value="Date" />
                                <VueDatePicker v-model="filters.date" range :enable-time-picker="false" @cleared="clearDateFilter" placeholder="Select date range" class="mt-1 shadow-sm"/>
                            </div>
                        </div>
                    </div>

                    <div v-if="projects.length === 0" class="text-center py-12">
                        <div class="text-gray-500 dark:text-gray-400 mb-2">
                            No projects found
                        </div>
                        <div class="text-sm text-gray-400 dark:text-gray-500">
                            Get started by creating your first project
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
                                        <button @click="toggleSort('status')" class="flex items-center space-x-2 cursor-pointer">
                                            Status
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button @click="toggleSort('due_date')" class="flex items-center space-x-2 cursor-pointer">
                                            Due Date
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <button @click="toggleSort('tasks')" class="flex items-center space-x-2 cursor-pointer">
                                            Tasks
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M11.47 4.72a.75.75 0 0 1 1.06 0l3.75 3.75a.75.75 0 0 1-1.06 1.06L12 6.31 8.78 9.53a.75.75 0 0 1-1.06-1.06l3.75-3.75Zm-3.75 9.75a.75.75 0 0 1 1.06 0L12 17.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.75-3.75a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="project in filteredProjects" :key="project.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ project.title }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ project.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100':
                                                    project.status ===
                                                    'not_started',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100':
                                                    project.status ===
                                                    'in_progress',
                                                'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100':
                                                    project.status ===
                                                    'completed',
                                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300':
                                                    project.status ===
                                                    'on_hold',
                                            }">
                                            {{ formatStatus(project.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDate(project.due_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ project.tasks_count }} tasks
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                        <Link :href="route('projects.show', project.id)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                            View
                                        </Link>
                                        <button @click="editProject(project)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                            Edit
                                        </button>
                                        <button v-if="project.isAuthorized" @click="deleteProject(project)"
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

        <!-- Create/Edit Project Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ form.id ? "Edit Project" : "Create Project" }}
                </h2>

                <div class="mt-6">
                    <form @submit.prevent="submitForm">
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                            <InputError :message="form.errors.title" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="description" value="Description" />
                            <textarea id="description"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.description" rows="3"></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="status" value="Status" />
                            <select id="status"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                v-model="form.status" required>
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                            <InputError :message="form.errors.status" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="due_date" value="Due Date" />
                            <TextInput id="due_date" type="date" class="mt-1 block w-full" v-model="form.due_date"
                                required />
                            <InputError :message="form.errors.due_date" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <SecondaryButton @click="closeModal" class="mr-3">
                                Cancel
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing">
                                {{ form.id ? "Update" : "Create" }}
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
        projects: {
            type: Array,
            required: true,
        },
    });

    console.log(props.projects);
    

    const filters = ref({
        titleSearch: "",
        status: "",
        tasks: "",
        date: "",
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

    const clearDateFilter = () => {
        filters.value.date = ""
    }

    const showModal = ref(false);

    const form = useForm({
        id: null,
        title: "",
        description: "",
        status: "not_started",
        due_date: "",
    });

    const createProject = () => {
        form.reset();
        showModal.value = true;
    };

    const editProject = (project) => {
        form.id = project.id;
        form.title = project.title;
        form.description = project.description;
        form.status = project.status;
        form.due_date = project.due_date;
        showModal.value = true;
    };

    const closeModal = () => {
        showModal.value = false;
        form.reset();
    };

    const submitForm = () => {
        if (form.id) {
            form.put(route("projects.update", form.id), {
                preserveScroll: true,
                onSuccess: () => closeModal(),
            });
        } else {
            form.post(route("projects.store"), {
                preserveScroll: true,
                onSuccess: () => closeModal(),
            });
        }
    };

    const deleteProject = (project) => {
        if (confirm("Are you sure you want to delete this project?")) {
            form.delete(route("projects.destroy", project.id), {
                preserveScroll: true,
            });
        }
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
