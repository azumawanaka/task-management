<script setup>
    import axios from 'axios';
    import { useRouter } from 'vue-router';
    import { ref, onMounted, reactive, nextTick, watch } from 'vue';
    import { Form, Field, useResetForm } from 'vee-validate';
    import * as yup from 'yup';
    import { useToastr } from '../../toastr';
    import { formatDate } from '../../helper.js';

    const router = useRouter();

    const toastr = useToastr();
    const form = ref(null);
    const loading = ref(false);
    const remember = ref(false);

    const formData = reactive({
        username_or_email: '',
        password: '',
    });

    const loginSchema = yup.object({
        username_or_email: yup.string().required('Username field is required.'),
        password: yup.string().required('Password is required.'),
    });

    const signin = (values, { setErrors, setFieldError }) => {
        axios.post('/login', values)
            .then((response) => {
                if (remember.value) {
                    localStorage.setItem('remember', true);
                    localStorage.setItem('e', values.username_or_email);
                    localStorage.setItem('p', values.password);
                } else {
                    localStorage.removeItem('remember');
                    localStorage.removeItem('e'); // Clear stored username_or_email
                }

                localStorage.setItem('token', response.data.token);
                loading.value = false;
                toastr.success('Signing in please wait...');
                window.location.href = '/tasks';
            }).catch((error) => {
                setFieldError('username_or_email', error.response.data.error);
                toastr.error(error.response.data.error);
            });
    };

    // Retrieve stored email from local storage on component mount
    onMounted(() => {
        const remembered = localStorage.getItem('remember');
        if (remembered) {
            remember.value = true;
            formData.username_or_email = localStorage.getItem('e') || '';
            formData.password = localStorage.getItem('p') || '';
        }
    });
</script>

<template>
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Task</b> Management</a>
        </div>

        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <Form @submit="signin"
                    :validation-schema="loginSchema"
                    v-slot="{ errors }"
                    :class="loading ? 'loading' : ''">

                    <div class="form-validation">
                        <div class="input-group mb-3">
                            <Field type="text" v-model="formData.username_or_email" id="username_or_email" name="username_or_email" placeholder="johndoe@example.com" class="form-control" :class="{ 'is-invalid': errors.username_or_email }" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            <span class="invalid-feedback">{{ errors.username_or_email }}</span>
                        </div>
                        <div class="input-group mb-3">
                            <Field type="password" v-model="formData.password" id="password" name="password" placeholder="" class="form-control" :class="{ 'is-invalid': errors.password }" />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" v-model="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </Form>

                <!-- <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p> -->
                <p class="mb-0">
                    <a href="/register" class="text-center">Register a new membership</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
</template>
