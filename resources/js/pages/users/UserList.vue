<script setup>
    import axios from 'axios';
    import { ref, onMounted, reactive, nextTick, watch } from 'vue';
    import { Form, Field, useResetForm } from 'vee-validate';
    import * as yup from 'yup';
    import { Bootstrap4Pagination } from 'laravel-vue-pagination';

    import { useToastr } from '../../toastr';
    const toastr = useToastr();

    import UserListItem from './UserListItem.vue';
    import Search from '../../components/searchbars/users/Search.vue';

    const users = ref([]);
    const editing = ref(false);
    const formValues = ref();
    const form = ref(null);
    const loading = ref(false);
    const searchQuery = ref(null);
    const limit = ref(5);
    const selectedUsers = ref([]);
    const parentChecked = ref(false);
    const searchParam = ref({
        page: 1,
        query: '',
        limit: 2,
    });

    // Delete Confirmation with SweetAlert
    const deleteConfirmation = () => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {

                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                });
            }
        });
    };

    const getUsers = (param) => {
        searchParam.value = param;
        // searchQuery.value = param.filter;
        axios.get(`/api/users?page=${searchParam.value.page}`, {
            params: {
                query: searchParam.value.query,
                limit: searchParam.value.limit,
            }
        })
        .then((response) => {
            users.value = response.data;
        })
    };

    const changePage = (num) => {
        searchParam.value.page = num;
        getUsers(searchParam.value);
    };

    const searchUser = (v = '') => {
        searchQuery.value = v
        axios.get(`/api/users/search`, {
            params: {
                query: v,
                limit: limit.value,
            }
        })
        .then(response => {
            users.value = response.data
        })
        .catch(error => {
            console.log(error);
        });
    };

    const createUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().required()
            .test('password', 'Passwords must be be minimum of 8 characters', function(value) {
                if (!!value) {
                    const schema = yup.string().min(8);
                    return schema.isValidSync(value);
                }
                return true;
            }),
    });

    const editUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().notRequired()
            .test('password', 'Passwords must be be minimum of 8 characters', function(value) {
                if (!!value) {
                    const schema = yup.string().min(8);
                    return schema.isValidSync(value);
                }
                return true;
            }),
    });

    const addUser = () => {
        editing.value = false

        form.value.resetForm();

        $('#userFormModal').modal('show');
    };

    // @submit event
    const storeUser = (values, { useResetForm, setErrors }) => {
        axios.post('/api/users', values)
            .then((response) => {
                if (response.data) {
                    getUsers(searchParam.value);
                    form.value.resetForm();

                    ////////////////////////////////////
                    // prepend latest value to the lists
                    // users.value.unset(response.data);
                    ////////////////////////////////////

                    $('#userFormModal').modal('hide');

                    toastr.success('User created successfully!');
                }
            }).catch((error) => {
                toastr.error('An error occurred while saving.');
                if (error.response && error.response.data.errors) {
                    setErrors(error.response.data.errors);
                } else {
                    console.error(error);
                }
            });
    };

    const editUser = (user) => {
        form.value.resetForm();

        editing.value = true

        formValues.value = {
            id: user.id,
            name: user.name,
            email: user.email,
        };

        // Wait for the next tick to ensure that the DOM has been updated
        // Then, show the modal
        nextTick(() => {
            $('#userFormModal').modal('show');
        });
    };

    // update user
    const updateUser = (values, { setErrors }) => {
        axios.put(`/api/users/${ formValues.value.id }`, values)
            .then((response) => {
                const index = users.value.data.findIndex(user => user.id === response.data.id);

                users.value.data[index] = response.data;

                $('#userFormModal').modal('hide');

                toastr.success('User updated successfully!');
            }).catch((error) => {
                toastr.error('An error occurred while updating.');

                if (error.response && error.response.data.errors) {
                    setErrors(error.response.data.errors);
                } else {
                    console.error(error);
                }
            });
    };

    const handleFormSubmit = (values, actions) => {
        if (editing.value) {
            updateUser(values, actions);
        } else {
            storeUser(values, actions);
        }
    }

    const userDeleted = (userId) => {
        // users.value.data = users.value.data.filter(user => user.id !== userId);
        getUsers(searchParam.value);
    };

    const checkAllChildren = (el) => {
        const userIds = users.value.data.map(user => user.id);

        $('.user_checkbox').prop('checked', el.srcElement.checked).change();

        if (el.srcElement.checked) {
            selectedUsers.value = userIds;
        } else {
            selectedUsers.value = [];
        }
    };

    const toggleSelection = (user) => {
        const index = selectedUsers.value.indexOf(user.id);
        if (index === -1) {
            selectedUsers.value.push(user.id);
        } else {
            selectedUsers.value.splice(index, 1);
        }
    };

    const bulkDelete = () => {
        axios.delete(`/api/users`, {
            data: {
                ids: selectedUsers.value
            }
        })
        .then((response) => {
            toastr.success(response.data.message);

            selectedUsers.value = {};

            $('.bulk-delete').prop('checked', false).change();

            getUsers(searchParam.value);
        });
    }

    watch(formValues, (newValues) => {
        if (form.value) {
            form.value.setValues(newValues);
        }
    });

    onMounted(() => {
        getUsers(searchParam.value);
    });

</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div class="mb-3">
                    <button type="button"
                        class="btn btn-primary btn-sm"
                        @click="addUser">Add New User</button>

                    <button v-if="selectedUsers.length > 0" type="button" class="btn btn-danger btn-sm ml-2" @click="bulkDelete">
                        Delete Selected
                    </button>
                </div>

                <div class="col-md-3">
                    <Search :page="searchParam.page" :limit="searchParam.limit" @toggle-search="getUsers" />
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hovered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="bulk-delete" v-model="parentChecked" @change="checkAllChildren"></th>
                                    <th style="width: 10px">ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registered Date</th>
                                    <th>Roles</th>
                                    <th>Options</th>
                                </tr>
                            </thead>

                            <tbody v-if="users.data && users.data.length > 0">
                                <UserListItem v-for="(user, index) in users.data" :key="user.id"
                                    :user=user
                                    :index=index
                                    @user-deleted="userDeleted"
                                    @edit-user="editUser"
                                    @toggle-selection="toggleSelection"  />
                            </tbody>

                            <tfoot v-else>
                                <tr>
                                    <td colspan="6">Empty result</td>
                                </tr>
                            </tfoot>
                        </table>

                        <Bootstrap4Pagination :data="users" @pagination-change-page="changePage"/>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userFormModal" tabindex="-1" role="dialog" aria-labelledby="createUserLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Create User</span>
                    </h5>
                </div>
                <Form ref="form"
                    @submit="handleFormSubmit"
                    :validation-schema="editing ? editUserSchema : createUserSchema"
                    v-slot="{ errors }"
                    :class="loading ? 'loading' : ''">

                    <div class="modal-body">
                        <div class="form-validation">
                            <div class="form-group row">
                                <label class="col-lg-12 col-form-label" for="name">Name</label>
                                <div class="col-lg-12">
                                    <Field type="text" class="form-control" :class="{ 'is-invalid': errors.name }" id="name" name="name" />
                                    <span class="invalid-feedback">{{ errors.name }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-12 col-form-label" for="email">Email</label>
                                <div class="col-lg-12">
                                    <Field type="email" class="form-control" :class="{ 'is-invalid': errors.email }" name="email" />
                                    <span class="invalid-feedback">{{ errors.email }}</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-12 col-form-label" for="password">Password</label>
                                <div class="col-lg-12">
                                    <Field type="password" class="form-control" :class="{ 'is-invalid': errors.password }" name="password" />
                                    <span class="invalid-feedback">{{ errors.password }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm close-modal" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info btn-sm">Save</button>
                    </div>
                </Form>
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
