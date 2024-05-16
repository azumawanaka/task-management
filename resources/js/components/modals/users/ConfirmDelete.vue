<script setup>

    defineProps({
        user_id: Number
    });

    const emit = defineEmits(['toggleDelete']);

    const deleteUser = (userId) => {
        emit('toggleDelete', userId);
    }

</script>

<template>
    <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm close-modal" data-dismiss="modal">Cancel</button>
                    <button @click="deleteUser(user_id)" type="button" class="btn btn-info btn-sm">Delete User</button>
                </div>
            </div>
        </div>
    </div>
</template>