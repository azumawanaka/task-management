<script setup>
    import { ref, defineProps, onMounted, defineEmits, watch } from 'vue';
    import { formatDate } from '../../../helper.js';
    import { useToastr } from '../../../toastr';

    const toastr = useToastr();

    const emit = defineEmits(['subtasksUpdated']);

    const props = defineProps({
        sub_tasks: Array,
        task: Object,
    });

    const subtasks = ref([]);

    const isSubTaskRow = ref(false);
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

    // const fetchSubTasks = (task) => {
    //     axios.get(`/api/tasks/${task.id}/show`)
    //         .then((response) => {
    //             task.subtasks.value = response.data.subtasks;
    //             emit('subtasksUpdated', response.data.subtasks); // Emit event to notify parent
    //         });
    // }

    const saveNewSubTask = async () => {
        if (validateFields()) {
            form.value.parent_id = props.task.id;
            axios.post('/api/tasks', form.value)
                .then((response) => {
                    toastr.success('Sub-task was successfully added!');

                    isSubTaskRow.value = false;
                    clearForm();

                    emit('subtasksUpdated', props.task);

                }).catch((error) => {
                    if (error.response && error.response.data) {
                        toastr.error(error.response.data.message);
                    }
                });
        }
    };

    watch(props.sub_tasks, (newSubtasks, oldSubtasks) => {
        console.log('Watcher triggered:', newSubtasks, oldSubtasks);
        fetchSubTasks(props.task);
    }, { deep: true });

    onMounted(() => {
        getStatuses();
    });
</script>

<template>
    <div class="modal fade" id="subTaskModal" tabindex="-1" role="dialog" aria-labelledby="subTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ task.title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
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
                            <tr v-for="(task, index) in task.subtasks" :key="task.id">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm close-modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
