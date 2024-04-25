import Vue from 'vue'
import {createInertiaApp, Head, Link} from '@inertiajs/vue2'
import Layout from "./Components/Layout.vue";
import axios from "axios";
import Empty from "./Components/Empty.vue";
import PageHeader from "./Components/PageHeader.vue";
import Length from "./Components/Length.vue";
import Swal from "sweetalert2";
import {Datetime} from "vue-datetime";
import Alert from "./Components/Alert.vue";
import Modal from "./Components/Modal.vue";
import SimplePagination from "./Components/SimplePagination.vue";
import FormError from "./Components/FormError.vue";
import Permissions from "./permissions.js";



window.axios = axios

window.Swal = Swal
Vue.component('Layout',Layout)
Vue.component('empty',Empty)
Vue.component('PageHeader',PageHeader)
Vue.component('Length',Length)
Vue.component('Multiselect',window.VueMultiselect.default)
Vue.component('Datetime',Datetime)
Vue.component('Alert',Alert)
Vue.component('Modal',Modal)
Vue.component('Pagination',SimplePagination)
Vue.component('FormError',FormError)


Vue.prototype.$route = route
Vue.prototype.$user = window.Laravel.user
Vue.prototype.$permissions = new Permissions()

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.filter('daysToGo',function(created){
    return moment(created).endOf('days').fromNow();
});

Vue.filter('fullTime',function(created){
    return  $.fullCalendar.formatDate(created,'DD MMMM YYYY');
});

Vue.filter('simpleDate',function(created){
    return  moment(created).format('DD-MM-YYYY');
});

Vue.filter('myDate',function(created){
    return moment(created).format('Do MMMM YYYY')
});

Vue.filter('abbDate',function(created){
    return moment(created).format('Do of')
});

Vue.filter('times',function(created){
    return moment(new Date().toDateString()+' '+ created).parseZone().format('HH:mm')
});

Vue.filter('myTime',function(created){
    return moment(created).parseZone().format('HH:mm')
});

Vue.filter('timeAgo',function(created){
    return moment(created).fromNow();
});

Vue.filter('currency',function(money){
    return Number(money).toLocaleString('en-US',{ style: "currency", currency: "MWK" })
});
Vue.filter('numberCurrency',function(money){
    return Number(money).toLocaleString('en-US')
});




createInertiaApp({
    progress: {
        color: '#88290c',
    },
    resolve: name => {
        const pages = import.meta.glob('./Components/**/*.vue', {eager: true})
        return pages[`./Components/${name}.vue`]
    },
    setup({el, App, props, plugin}) {
        Vue.use(plugin)
            .component('Link', Link)
            .component('Head', Head)

        new Vue({
            render: h => h(App, props),
            data() {
                return {
                    messages: []
                }
            },

        }).$mount(el)
    },

})
