<script setup>
    import { ref, watch } from 'vue';
    import { formatDate } from '../../helper.js';
    import ConfirmDelete from '../../components/modals/tasks/ConfirmDelete.vue';
    import SubTasks from '../../components/modals/tasks/SubTasks.vue';

    import { useToastr } from '../../toastr';
    const toastr = useToastr();

    const props = defineProps({
        task: Object,
        index: Number,
    });
    const statuses = ref([]);
    const subTasks = ref([]);

    const emit = defineEmits(['taskDeleted', 'editTask', 'toggleSelection', 'showSubTasks']);
    const taskId = ref(null);

    const confirmTaskDeletion = (task) => {
        taskId.value = task.id;
        Swal.fire({
            title: "Are you sure?",
            // text: "Moving",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteTask(task.id).then((response) => {
                    Swal.fire({
                        title: "Success!",
                        text: "Task was moved to trash.",
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

    const updatePublishStatus = () => {
        axios.post(`/api/tasks/${props.task.id}/publish_status`,
            {
                is_published: props.task.is_published
            },
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((response) => {
                console.log(response.data);
                toastr.success(`Task was successfully ${props.task.is_published ? 'published!' : 'moved to draft!'}`);
            })
            .catch((err) => {
                toastr.error('An error occurred. Please try again!');
                props.task.is_published = props.task.is_published;
            });
    };

    const togglePublish = (v) => {
        props.task.is_published = !props.task.is_published;
        updatePublishStatus();
    };

</script>

<template>

    <tr>
        <td>{{ task.id }}</td>
        <td>{{ task.title }}</td>
        <td>{{ task.content.length > 50 ? task.content.slice(0, 50) + '...' : task.content }}</td>
        <td>{{ task.status }}</td>
        <td>{{ formatDate(task.created_at) }}</td>
        <td>
            <div class="toggle-switch">
                <div>
                    <input type="checkbox" class="toggle-input" v-model="task.is_published">
                    <label for="toggle" class="toggle-label" @click="togglePublish"></label>
                </div>
                <span class="toggle-status">{{ task.is_published ? 'Published' : 'Draft' }}</span>
            </div>
        </td>
        <td>
            <router-link :to="`/tasks/${task.id}/edit`" class="text-info"><i class="fa fa-edit"></i></router-link>

            <a href="#"
                class="text-danger ml-2"
                @click="confirmTaskDeletion(task)"><i class="fa fa-trash"></i> </a>

            <router-link :to="`/tasks/${task.id}/sub-tasks`" class="ml-2 text-primary"
                title="sub-tasks"><i class="fa fa-list"></i></router-link>
        </td>
    </tr>

    <ConfirmDelete :task_id="taskId" @toggle-delete="deleteTask" />
</template>

<style scoped>
/* Basic styling for the toggle switch container */
.toggle-switch {
  display: inline-flex;
  align-items: center;
  font-family: Arial, sans-serif;
  font-size: 12px;
}

/* Hide the checkbox */
.toggle-input {
  display: none;
}

/* Styling for the label (the switch) */
.toggle-label {
  position: relative;
  width: 40px;
  height: 20px;
  background-color: #ccc;
  border-radius: 20px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.toggle-label::before {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background-color: white;
  top: 2px;
  left: 2px;
  transition: transform 0.2s;
}

/* Change background color and move the circle when checked */
.toggle-input:checked + .toggle-label {
  background-color: #4caf50;
}

.toggle-input:checked + .toggle-label::before {
  transform: translateX(20px);
}

/* Status text next to the toggle */
.toggle-status {
  margin-left: 8px;
}

/* Optional: Initial status text update */
.toggle-input:checked + .toggle-label + .toggle-status {
  content: 'is_published';
}
</style>