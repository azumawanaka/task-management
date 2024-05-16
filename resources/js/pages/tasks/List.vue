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
    const statuses = ref({
        '' : 'Filter By',
        to_do:'To Do',
        in_progress: 'In Progress',
        done: 'Done',
    });
    const selectedtasks = ref([]);

    const togglePub = ref('is_published');
    const filterBy = ref('');
    const page_limits = ref([10, 20, 50, 100]);
    const searchParam = ref({
        page: 1,
        query: '',
        limit: 10,
        sortColumn: 'created_at',
        sortOrder: 'desc',
        filterBy: '',
    });

    const getTasks = (param) => {
        searchParam.value = param;
        console.log(searchParam.value)
        axios.get(`/api/tasks?page=${searchParam.value.page}`, {
            params: {
                query: searchParam.value.query,
                limit: searchParam.value.limit,
                sortColumn: searchParam.value.sortColumn,
                sortOrder: searchParam.value.sortOrder,
                filterBy: filterBy.value,
                toggleBy: searchParam.value.toggleBy,
            }
        })
        .then((response) => {
            tasks.value = response.data;
        })
    };

    const changePage = (num) => {
        searchParam.value.page = num;
        searchParam.value.toggleBy = togglePub.value
        getTasks(searchParam.value);
    };

    const taskDeleted = (taskId) => {
        getTasks(searchParam.value);

        if (tasks.value.length === 0) { // reset to page = 1 if current page has empty items
            searchParam.value.page = 1;
        } else {
            searchParam.value.page = 1;
        }

        getTasks(searchParam.value);
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
    };

    const toggleSelection = (task) => {
        const index = selectedtasks.value.indexOf(task.id);
        if (index === -1) {
            selectedtasks.value.push(task.id);
        } else {
            selectedtasks.value.splice(index, 1);
        }
    };

    const toggleLimit = () => {
        getTasks(searchParam.value);
    };

    const sortBy = (column) => {
        if (searchParam.value.sortColumn === column) {
            searchParam.value.sortOrder = searchParam.value.sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            searchParam.value.sortOrder = 'asc';
        }
        searchParam.value.sortColumn = column;

        getTasks(searchParam.value);
    };

    const toggleFilterByStatus = (v) => {
        searchParam.value.filterBy = filterBy.value;
        getTasks(searchParam.value);
    };

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
            <div class="mb-2">
                <h1 class="m-0">Tasks</h1>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="mb-3">
                    <router-link to="/tasks/create" class="btn btn-primary btn-sm">Create</router-link>
                </div>

                <div class="col-md-6 d-flex align-items-center justify-content-end">
                    <div class="md-4 mr-1">
                        <select name="filter_by" v-model="filterBy" class="form-control mr-2" @change="toggleFilterByStatus">
                            <option v-for="(status, index) in statuses" :key="index" :value="index">{{ status }}</option>
                        </select>
                    </div>
                    <div class="md-7">
                        <Search :page="searchParam.page" :limit="searchParam.limit" @toggle-search="getTasks" />
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hovered">
                            <thead>
                                <tr>
                                    <th style="width: 50px">ID</th>
                                    <th @click="sortBy('title')">
                                        Title
                                        <span v-if="searchParam.sortColumn === 'title'">
                                            <i :class="searchParam.sortOrder === 'asc' ? 'fa fa-sort-up' : 'fa fa-sort-down'"></i>
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th>Content</th>
                                    <th>Status</th>
                                    <th @click="sortBy('created_at')">
                                        Date Added
                                        <span v-if="searchParam.sortColumn === 'created_at'">
                                            <i :class="searchParam.sortOrder === 'asc' ? 'fa fa-sort-up' : 'fa fa-sort-down'"></i>
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-sort-down"></i>
                                        </span>
                                    </th>
                                    <th></th>
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

                        <div class="d-flex justify-content-between">
                            <div class="mr-2">
                                <select class="form-control" name="per_page" v-model="searchParam.limit" @change="toggleLimit">
                                    <option v-for="(limit, index) in page_limits" :key="index" :value="limit">{{ limit }} per page</option>
                                </select>
                            </div>

                            <Bootstrap4Pagination
                                :data="tasks"
                                @pagination-change-page="changePage"
                                :limit="searchParam.limit" />
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
