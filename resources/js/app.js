import router from './router';
import Vuex from 'vuex'
import {
    BootstrapVue,
    IconsPlugin
} from 'bootstrap-vue'
import Axios from 'axios'
import VueAxios from 'vue-axios'
import 'vuejs-dialog/dist/vuejs-dialog.min.css';
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import SweetModal from 'sweet-modal-vue/src/plugin.js'
import VuejsDialog from 'vuejs-dialog';
import {
    VueSpinners
} from '@saeris/vue-spinners'

import { VueEditor } from "vue2-editor";

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Install BootstrapVue
Vue.use(BootstrapVue)
Vue.use(VueSpinners)
Vue.use(VueAxios, Axios)
Vue.use(SweetModal)
Vue.use(VuejsDialog);
Vue.use(Vuex)

Vue.use(VueEditor)


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('crearAtributo', require('./components/crearAtributo.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router
});
