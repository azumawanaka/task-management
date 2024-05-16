import axios from 'axios';
window.axios = axios;

import jquery from 'jquery';
window.$ = window.jQuery = jquery;

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
window.Swal = Swal;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
