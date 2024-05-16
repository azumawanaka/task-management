<script setup>
    import axios from 'axios';
    import { ref, onMounted } from 'vue';
    import { Form, Field } from 'vee-validate';

    import * as yup from 'yup';
    import { useToastr } from '../../toastr';

    import VueDatePicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css';

    const toastr = useToastr();
    const loading = ref(false);
    const isValid = ref(true);
    const statuses = ref([]);
    const filePreviews = ref([]);

    const formData = ref({
        title: '',
        content: '',
        status: '',
        images: [],
        is_published: 'is_published',
    });

    // Define Yup schema for validation
    const schema = yup.object().shape({
        title: yup.string().required('Title is required').max(100),
        content: yup.string().required('Content is required'),
        status: yup.string().required('Status is required'),
    });

    // Function to validate form data
    const validateForm = async () => {
        try {
            await schema.validate(formData.value, { abortEarly: false });
            return true;
        } catch (error) {
            toastr.error('An error occurred. Please try again!');
            return false;
        }
    };

    const saveBy = (v) => {
        formData.value.is_published = v
    }

    const handleChange = (event) => {
        const selectedFiles = event.target.files;
        formData.value.images = selectedFiles;

        filePreviews.value = [];
        for (let i = 0; i < selectedFiles.length; i++) {
            const reader = new FileReader();

            reader.onload = () => {
                filePreviews.value.push(reader.result);
            };

            if (selectedFiles[i]) {
                reader.readAsDataURL(selectedFiles[i]);
            }
        }
    };

    const deleteFile = (index) => {
        filePreviews.value.splice(index, 1);
    };

    const submitForm = async ({ setErrors }) => {
        isValid.value = await validateForm();

        if (isValid.value) {
            const formDataToSend = new FormData();
            formDataToSend.append('title', formData.value.title);
            formDataToSend.append('content', formData.value.content);
            formDataToSend.append('status', formData.value.status);
            formDataToSend.append('is_published', formData.value.is_published);

            for (let i = 0; i < formData.value.images.length; i++) {
                formDataToSend.append('images[]', formData.value.images[i]);
            }

            axios.post('/api/tasks', formDataToSend)
                .then((response) => {
                    loading.value = false;

                    Swal.fire({
                        title: "Success!",
                        text: formData.value.is_published === 'is_published' ? "Task was successfully published!" : "Task was succesfully drafted!",
                        icon: "success",
                        timer: 3000,
                        willClose: () => {
                            window.location.href = '/tasks';
                        }
                    });
                }).catch((error) => {
                    console.log(error.response.data.message);
                    if (error.response && error.response.data.errors) {
                        toastr.error(error.response.data.message);
                    } else {
                        toastr.error('An error occurred. Please try again!');
                    }
                });
        }
    };

    const getStatuses = () => {
        axios.get(`/api/tasks/statuses`)
            .then((response) => {
                console.log(response)
                statuses.value = response.data;
            });
    };

    onMounted(() => {
        getStatuses();
    });
</script>

<template>
     <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Task</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/tasks" class="">Tasks</router-link>
                        </li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <Form @submit="submitForm"
                        :validation-schema="schema" v-slot="{ errors }"
                        :class="loading ? 'loading' : ''"
                        enctype="multipart/form-data">

                        <div class="form-validation mb-5">
                            <div class="input-group mb-3">
                                <Field type="text" id="title" name="title" v-model="formData.title" placeholder="Enter Task Title" class="form-control" :class="{ 'is-invalid': errors.title }" />
                                <span class="invalid-feedback">{{ errors.title }}</span>
                            </div>
                            <div class="input-group mb-3">
                                <Field as="textarea" name="content" id="content" v-model="formData.content" placeholder="Add contents..." cols="30" rows="5" class="form-control" :class="{ 'is-invalid': errors.content }" />
                                <span class="invalid-feedback">{{ errors.content }}</span>
                            </div>

                            <div class="input-group mb-3">
                                <Field name="status" as="select" v-model="formData.status" class="form-control" :class="{ 'is-invalid': errors.status }">
                                    <option v-for="(status, index) in statuses" :key="index" :value="index">{{ status }}</option>
                                </Field>
                                <span class="invalid-feedback">{{ errors.status }}</span>
                            </div>

                            <div class="row">
                                <div v-for="(prev, index) in filePreviews" :key="index" class="d-flex flex-column justify-content-between col-md-2 mb-3 border py-2">
                                    <img :src="prev" alt="Task Image Preview" class="img-fluid">
                                    <button type="button" class="btn btn-block btn-xs btn-danger py-0" @click="deleteFile(index)">
                                        Delete
                                    </button>
                                </div>

                                <div class="col-md-2 mb-3 ml-2 border py-2 h3 d-flex align-items-center justify-content-center text-info hover">
                                    <i class="fa fa-plus-square"></i>
                                    <small style="font-size: 12px;" class="ml-2">Add Files</small>
                                    <Field name="images" v-slot="{ value, errorMessage  }" rules="required">
                                        <input type="file" id="fileBtn" @change="handleChange($event)" accept="image/*" multiple />
                                        <span v-if="errorMessage" class="text-danger">{{ errorMessage }}</span>
                                    </Field>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mb-5">
                                <button class="btn btn-info btn-md mr-2" type="submit" @click="saveBy('draft')">Save as draft</button>
                                <button class="btn btn-primary btn-md" type="submit" @click="saveBy('is_published')">Publish</button>
                            </div>

                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
input#fileBtn {
    position: absolute;
    font-size: 12px;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9;
    opacity: 0;
}

.hover:hover {
    background-color: #f0f0f0;
    box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
    transition: linear .3s;
}
</style>
