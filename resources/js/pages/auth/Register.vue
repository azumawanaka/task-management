<script setup>
    import axios from 'axios';
    import { ref } from 'vue';
    import { Form, Field } from 'vee-validate';
    import * as yup from 'yup';
    import { useToastr } from '../../toastr';

    const toastr = useToastr();
    const loading = ref(false);

    const registerSchema = yup.object({
        name: yup.string().min(5, 'Name must be at least 5 characters').required('Name is required'),
        user_name: yup.string()
            .min(5, 'User Name must be at least 5 characters')
            .matches(/^\S*$/, 'User Name must not contain spaces')
            .required('User Name is required'),
        email: yup.string().email('Invalid email').required('Email is required'),
        password: yup.string().required('Password is required'),
        confirm_password: yup.string().oneOf([yup.ref('password'), null], 'Passwords must match')
            .required('Confirm password is required')
    });

    const register = (values, { setFieldError, setErrors }) => {
        axios.post('/register', values)
            .then((response) => {
                loading.value = false;

                Swal.fire({
                    title: "Success!",
                    text: "You have successfully registered!",
                    icon: "success",
                    timer: 3000,
                    willClose: () => {
                        window.location.href = '/login';
                    }
                });
            }).catch((error) => {
                toastr.error('An error occurred while saving.');
                if (error.response && error.response.data.errors) {
                    setErrors(error.response.data.errors);
                } else {
                    console.error(error);
                }
            });
    };

</script>

<template>
    <div class="register-box">
        <div class="register-logo">
            <a href="#"><b>Task</b>Management</a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
            <p class="login-box-msg">Register a new membership</p>

            <Form @submit="register"
                :validation-schema="registerSchema" v-slot="{ errors }"
                :class="loading ? 'loading' : ''">

                <div class="form-validation">
                    <div class="input-group mb-3">
                        <Field type="text" id="name" name="name" placeholder="Enter Name" class="form-control" :class="{ 'is-invalid': errors.name }" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <span class="invalid-feedback">{{ errors.name }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <Field type="text" id="user_name" name="user_name" placeholder="Enter user_name" class="form-control" :class="{ 'is-invalid': errors.user_name }" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <span class="invalid-feedback">{{ errors.user_name }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <Field type="email" id="email" name="email" placeholder="johndoe@example.com" class="form-control" :class="{ 'is-invalid': errors.email }" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-mail"></span>
                            </div>
                        </div>
                        <span class="invalid-feedback">{{ errors.email }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <Field type="password" id="password" name="password" placeholder="Enter password" class="form-control" :class="{ 'is-invalid': errors.password }" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <span class="invalid-feedback">{{ errors.password }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <Field type="password" id="confirm_password" name="confirm_password" placeholder="Retype password" class="form-control" :class="{ 'is-invalid': errors.confirm_password }" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <span class="invalid-feedback">{{ errors.confirm_password }}</span>
                    </div>
                </div>

                <div class="row">
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                    <!-- /.col -->
                </div>
            </Form>

                <a href="/login" class="text-center">I already have a membership</a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
</template>
