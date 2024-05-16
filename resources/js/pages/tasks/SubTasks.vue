<script setup>
    import axios from 'axios';
    import { ref, onMounted, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import { Bootstrap4Pagination } from 'laravel-vue-pagination';
    import Search from '../../components/searchbars/tasks/Search.vue';
    import { formatDate } from '../../helper.js';

    import { useToastr } from '../../toastr';
    const toastr = useToastr();

    const taskId = ref(null);
    const isSubTaskRow = ref(false);
    const route = useRoute();
    const task = ref({});
    const subTasks = ref([]);
    const statuses = ref([]);
    const form = ref({
        parent_id: null,
        title: '',
        content: '',
        status: 'to_do',
        is_published: 'is_published',
    });

    const clearForm = () => {
        form.value = {
            parent_id: null,
            title: '',
            content: '',
            status: 'to_do',
            is_published: 'is_published',
        };
    }

    const getSubTasks = (taskId) => {
        axios.get(`/api/tasks/${taskId}/show`)
        .then((response) => {
            task.value = response.data;
            subTasks.value = response.data.subtasks;
        })
    };

    const addSubTaskRow = () => {
        isSubTaskRow.value = true
        clearForm();
    };

    const getStatuses = () => {
        axios.get(`/api/tasks/statuses`)
            .then((response) => {
                statuses.value = response.data;
            });
    };

    const cancel = () => {
        isSubTaskRow.value = false
    };

    const validateFields = () => {
        if (form.value.title === '') {
            toastr.error('Title field is required.');
            return false;
        }

        if (form.value.content === '') {
            toastr.error('Content field is required.');
            return false;
        }

        if (form.value.status === '') {
            toastr.error('Status field is required.');
            return false;
        }

        return true;
    };

    const saveNewSubTask = async () => {
        if (validateFields()) {

            form.value.parent_id = taskId.value;
            axios.post('/api/tasks', form.value)
                .then((response) => {
                    toastr.success('Sub-task was successfully added!');

                    isSubTaskRow.value = false;
                    clearForm();

                    getSubTasks(taskId.value);

                }).catch((error) => {
                    if (error.response && error.response.data) {
                        toastr.error(error.response.data.message);
                    }
                });
        }
    };

    onMounted(() => {
        taskId.value = route.params.id;
        getSubTasks(taskId.value);

        getStatuses();
    });

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="mb-2">
                <h1 class="m-0">{{ task.title }} (sub-tasks)</h1>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hovered">
                            <thead>
                                <tr>
                                    <!-- <th style="width: 10px"><input type="checkbox" class="bulk-delete" v-model="parentChecked" @change="checkAllChildren"></th> -->
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Status</th>
                                    <th @click="sortBy('created_at')">Date Added</th>
                                    <th>Options</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(task, index) in subTasks" :key="task.id">
                                    <td>{{ task.title }}</td>
                                    <td>{{ task.content.length > 50 ? task.content.slice(0, 50) + '...' : task.content }}</td>
                                    <td>{{ task.status }}</td>
                                    <td>{{ formatDate(task.created_at) }}</td>
                                    <td>
                                        <router-link :to="`/tasks/${task.id}/edit`" class="text-info"><i class="fa fa-edit"></i></router-link>

                                        <a href="#"
                                            class="text-danger ml-2"
                                            @click="confirmTaskDeletion(task)"><i class="fa fa-trash"></i> </a>
                                    </td>
                                </tr>
                                
                            </tbody>
                            <tfoot>
                                <tr v-if="isSubTaskRow">
                                    <td>
                                        <input type="text" class="form-control" placeholder="Title" v-model="form.title">
                                    </td>
                                    <td colspan="2">
                                        <textarea class="form-control" rows="1" v-model="form.content"></textarea>
                                    </td>
                                    <td>
                                        <select class="form-control" name="status" v-model="form.status">
                                            <option v-for="(status, index) in statuses" :key="index" :value="index">{{ status }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info mr-2" @click.prevent="saveNewSubTask">Save</button>
                                        <button type="button" class="btn btn-default" @click.prevent="cancel">Cancel</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <button type="button" class="btn btn-info" @click.prevent="addSubTaskRow">
                                            <i class="fa fa-plus"></i>
                                            Add sub-task
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="d-flex justify-content-between">
                            <!-- <Bootstrap4Pagination
                                :data="tasks"
                                @pagination-change-page="changePage"
                                :limit="searchParam.limit" /> -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .loading button {
        pointer-events: none; /* Prevent form submission while loading */
        opacity: 0.7; /* Optionally decrease opacity to indicate loading state */
    }
</style>
