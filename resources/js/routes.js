import Login from './pages/auth/Login.vue';
import Register from './pages/auth/Register.vue';
import UserList from './pages/users/UserList.vue';
import TaskList from './pages/tasks/List.vue';
import TaskCreate from './pages/tasks/Create.vue';
import TaskEdit from './pages/tasks/Edit.vue';
import SubTasks from './pages/tasks/SubTasks.vue';

export default [
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: UserList,
        meta: { requiresAuth: true },
    },
    {
        path: '',
        name: 'tasks',
        component: TaskList,
        meta: { requiresAuth: true },
    },
    {
        path: '/tasks',
        name: 'tasks',
        component: TaskList,
        meta: { requiresAuth: true },
    },
    {
        path: '/tasks/create',
        name: 'tasks.create',
        component: TaskCreate,
        meta: { requiresAuth: true },
    },
    {
        path: '/tasks/:id/edit',
        name: 'tasks.edit',
        component: TaskEdit,
        meta: { requiresAuth: true },
    },
    {
        path: '/tasks/:id/sub-tasks',
        name: 'tasks.sub-tasks',
        component: SubTasks,
        meta: { requiresAuth: true },
    },
]
