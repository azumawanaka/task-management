import './bootstrap';
import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import { createRouter, createWebHistory } from 'vue-router';
import Routes from './routes.js';
import LogoutLink from './components/LogoutLink.vue';

const app = createApp({});

const router = createRouter({
    routes: Routes,
    history: createWebHistory(),
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isLoggedIn()) {
      next('/login'); 
    } else {
      next();
    }
});

function isLoggedIn() {
    const token = localStorage.getItem('token');
    return token !== null;
}

app.use(router);

app.component('logout-link', LogoutLink);

app.mount('#app');
