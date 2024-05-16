<script setup>
    import { useRouter } from 'vue-router';
    import axios from 'axios';

    const router = useRouter();

    const logout = async () => {
        try {
            axios.post('/logout').then((response) => {
                window.location.href = '/login';
            });
        } catch (error) {
            console.error('Logout failed:', error);
        }
    };
</script>

<template>
    <li class="nav-item">
      <a href="/logout" class="nav-link" @click.prevent="logout">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>Logout</p>
      </a>
    </li>
</template>