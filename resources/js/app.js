/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// import * as mdb from 'mdb-ui-kit'; // lib
// import {Input} from 'mdb-ui-kit'; // module

window.mdb = require('mdb-ui-kit'); // module

window.swal = require('sweetalert2');

require('./functions');

require('@fortawesome/fontawesome-free/js/all');
require('lity');


// window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
// Vue.component('image-uploader', require('./components/imageUploader.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


import imageUploader from './components/imageUploader.vue';
import videoUploader from './components/videoUploader.vue';
import clubTimes from './components/ClubTimes.vue';
import dataSwiper from './components/DataSwiper.vue';
import searchPlayers from './components/searchPlayers.vue';
import searchCoaches from './components/searchCoaches.vue';
import searchClubs from './components/searchClubs.vue';
import searchProducts from './components/searchProducts.vue';
import searchShops from './components/searchShops.vue';
import searchBlogs from './components/searchBlogs.vue';
import blogEditor from './components/BlogEditor.vue';
import tableEditor from './components/TableEditor.vue';
import searchTables from './components/searchTables.vue';
import blogs from './components/Blogs.vue';
import systemSetting from './components/SystemSetting.vue';
import referral from './components/Referral.vue';
import searchUsers from './components/searchUsers.vue';
import systemLogs from './components/SystemLogs.vue';
import coupons from './components/Coupons.vue';
import searchEvents from './components/searchEvents.vue';
import searchAgencies from './components/searchAgencies.vue';
import tagEditor from './components/tagEditor.vue';
import searchVideos from './components/searchVideos.vue';
import dropdown from './components/dropdown.vue';
import videoCard from './components/VideoCard.vue';


import {createApp, computed, ref} from 'vue'

const app = createApp({

    mode: 'production',
    config: {devtools: true},
    components: {
        imageUploader,
        videoUploader,
        clubTimes,
        dataSwiper,
        searchPlayers,
        searchCoaches,
        searchClubs,
        searchProducts,
        searchShops,
        searchBlogs,
        blogEditor,
        searchTables,
        tableEditor,
        blogs,
        systemSetting,
        systemLogs,
        referral,
        searchUsers,
        coupons,
        searchEvents,
        searchAgencies,
        tagEditor,
        searchVideos,
        dropdown,
        videoCard,


    }
}).mount('#app');
window.app = app;
// window.refs = ref;
// app.config.devtools = true;
// app.use(ref);
// app.mount('#app');

let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// create countdown phone activation code
document.addEventListener("DOMContentLoaded", function (event) {

});
