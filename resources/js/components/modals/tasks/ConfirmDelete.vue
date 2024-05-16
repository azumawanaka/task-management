<script setup>
    defineProps({
        task_id: Number
    });

    const emit = defineEmits(['toggleDelete']);

    const deleteTask = (taskId) => {
        emit('toggleDelete', taskId);
    }
</script>

<template>
    <div class="modal fade" id="deleteTaskModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this task?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm close-modal" data-dismiss="modal">Cancel</button>
                    <button @click="deleteTask(task_id)" type="button" class="btn btn-info btn-sm">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
