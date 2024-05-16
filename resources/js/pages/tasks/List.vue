<script setup>
    import axios from 'axios';
    import { ref, onMounted, watch } from 'vue';
    import { Bootstrap4Pagination } from 'laravel-vue-pagination';

    import { useToastr } from '../../toastr';
    const toastr = useToastr();

    import Items from './Items.vue';
    import Search from '../../components/searchbars/tasks/Search.vue';

    const tasks = ref([]);
    const formValues = ref();
    const form = ref(null);
    const loading = ref(false);
    const selectedtasks = ref([]);
    const parentChecked = ref(false);
    const searchParam = ref({
        page: 1,
        query: '',
        limit: 2,
    });

    const getTasks = (param) => {
        searchParam.value = param;
        axios.get(`/api/tasks?page=${searchParam.value.page}`, {
            params: {
                query: searchParam.value.query,
                limit: searchParam.value.limit,
            }
        })
        .then((response) => {
            tasks.value = response.data;
        })
    };

    const changePage = (num) => {
        searchParam.value.page = num;
        getTasks(searchParam.value);
    };

    const taskDeleted = (taskId) => {
        // tasks.value.data = tasks.value.data.filter(task => task.id !== task);
        getTasks(searchParam.value);
    };

    const checkAllChildren = (el) => {
        const taskIds = tasks.value.data.map(task => task.id);

        $('.task_checkbox').prop('checked', el.srcElement.checked).change();

        if (el.srcElement.checked) {
            selectedtasks.value = taskIds;
        } else {
            selectedtasks.value = [];
        }
    };

    const toggleSelection = (task) => {
        const index = selectedtasks.value.indexOf(task.id);
        if (index === -1) {
            selectedtasks.value.push(task.id);
        } else {
            selectedtasks.value.splice(index, 1);
        }
    };

    const bulkDelete = () => {
        axios.delete(`/api/tasks`, {
            data: {
                ids: selectedtasks.value
            }
        })
        .then((response) => {
            toastr.success(response.data.message);

            selectedtasks.value = {};

            $('.bulk-delete').prop('checked', false).change();

            getTasks(searchParam.value);
        });
    }

    watch(formValues, (newValues) => {
        if (form.value) {
            form.value.setValues(newValues);
        }
    });

    onMounted(() => {
        getTasks(searchParam.value);
    });

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tasks</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Tasks</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="mb-3">
                    <router-link to="/tasks/create" class="btn btn-primary btn-sm">Create</router-link>

                    <button v-if="selectedtasks.length > 0" type="button" class="btn btn-danger btn-sm ml-2" @click="bulkDelete">
                        Delete Selected
                    </button>
                </div>

                <div class="col-md-3">
                    <Search :page="searchParam.page" :limit="searchParam.limit" @toggle-search="getTasks" />
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hovered">
                            <thead>
                                <tr>
                                    <th style="width: 10px"><input type="checkbox" class="bulk-delete" v-model="parentChecked" @change="checkAllChildren"></th>
                                    <th style="width: 50px">ID</th>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Status</th>
                                    <th>Date Added</th>
                                    <th>Options</th>
                                </tr>
                            </thead>

                            <tbody v-if="tasks.data && tasks.data.length > 0">
                                <Items v-for="(task, index) in tasks.data" :key="task.id"
                                    :task=task
                                    :index=index
                                    @task-deleted="taskDeleted"
                                    @toggle-selection="toggleSelection"  />
                            </tbody>

                            <tfoot v-else>
                                <tr>
                                    <td colspan="6">Empty result</td>
                                </tr>
                            </tfoot>
                        </table>

                        <Bootstrap4Pagination :data="tasks" @pagination-change-page="changePage"/>

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
        /* Add additional styling for indicating loading state */
    }
</style>
