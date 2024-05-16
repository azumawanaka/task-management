<script setup>
    import { ref, onMounted } from 'vue';
    import { formatDate } from '../../helper.js';
    import ConfirmDelete from '../../components/modals/tasks/ConfirmDelete.vue';

    const props = defineProps({
        task: Object,
        index: Number,
    });
    const statuses = ref([]);

    const emit = defineEmits(['taskDeleted', 'editTask', 'toggleSelection']);
    const taskId = ref(null);

    const confirmTaskDeletion = (task) => {
        taskId.value = task.id;
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteTask(task.id).then((response) => {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Task has been deleted.",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    });

                    emit('taskDeleted', taskId.value);
                }).catch((error) => {
                    Swal.fire({
                        title: "Error!",
                        text: "An error occured. Please try again later!",
                        icon: "error"
                    });
                });
            }
        });
    };

    const deleteTask = (taskId) => {
        return axios.delete(`/api/tasks/${taskId}`);
    };

    const toggleSelection = (v) => {
        emit('toggleSelection', v)
    }

</script>

<template>

    <tr>
        <td><input type="checkbox" class="task_checkbox" @change="toggleSelection(task)"></td>
        <td>{{ task.id }}</td>
        <td>{{ task.title }}</td>
        <td>{{ task.content.length > 50 ? task.content.slice(0, 50) + '...' : task.content }}</td>
        <td>{{ task.status }}</td>
        <td>{{ formatDate(task.created_at) }}</td>
        <td><span :class="`badge badge-${task.is_published ? 'success' : 'warning text-white'}`">{{ task.is_published ? 'Published' : 'Draft' }}</span></td>
        <td>
            <router-link :to="`/tasks/${task.id}/edit`" class="text-info"><i class="fa fa-edit"></i></router-link>

            <a href="#"
                class="text-danger ml-2"
                @click="confirmTaskDeletion(task)"><i class="fa fa-trash"></i> </a>
        </td>
    </tr>

    <ConfirmDelete :task_id="taskId" @toggle-delete="deleteTask" />

</template>
