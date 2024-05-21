require('./bootstrap');
import '../css/app.css';
import Vue from 'vue';
import 'bootstrap-icons/font/bootstrap-icons.css'


Vue.component('form-create',require('./components/form/create.vue').default);
Vue.component('form-edit',require('./components/form/edit.vue').default);
Vue.component('form-index',require('./components/form/index.vue').default);
Vue.component('userform',require('./components/form/userform.vue').default);

const app = new Vue({
    el: '#app',
});