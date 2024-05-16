<script setup>

    import { ref } from 'vue';
    import { formatDate } from '../../helper.js';
    import { useToastr } from '../../toastr';

    import ConfirmDelete from '../../components/modals/users/ConfirmDelete.vue';

    const toastr = useToastr();

    const props = defineProps({
        user: Object,
        index: Number,
    });

    const emit = defineEmits(['userDeleted', 'editUser', 'toggleSelection']);
    const userId = ref(null);

    const confirmUserDeletion = (user) => {
        userId.value = user.id;
        // $('#deleteUserModal').modal('show');

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
                deleteUser(user.id).then((response) => {
                    Swal.fire({
                        title: "Deleted!",
                        text: "User has been deleted.",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    });

                    emit('userDeleted', userId.value);
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

    const deleteUser = (userId) => {
        return axios.delete(`/api/users/${userId}`);
    };

    // const deleteUser = (userId) => {
    //     axios.delete(`/api/users/${userId}`)
    //         .then(() => {
    //             $('#deleteUserModal').modal('hide');

    //             toastr.success('User deleted successfully.');

    //             emit('userDeleted', userId);
    //         });
    // };

    const toggleSelection = (v) => {
        emit('toggleSelection', v)
    }

</script>

<template>

    <tr>
        <td><input type="checkbox" class="user_checkbox" @change="toggleSelection(user)"></td>
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ formatDate(user.created_at)}}</td>
        <td>{{ user.role }}</td>
        <td>
            <a href="#"
                class="text-info"
                @click="$emit('editUser', user)"><i class="fa fa-edit"></i> </a>

            <a href="#"
                class="text-danger ml-2"
                @click="confirmUserDeletion(user)"><i class="fa fa-trash"></i> </a>
        </td>
    </tr>

    <ConfirmDelete :user_id="userId" @toggle-delete="deleteUser" />

</template>
