import 'babel-polyfill'
import Vue from 'vue'
import VueRouter from 'vue-router'
import VueClipboard from 'vue-clipboard2'
import App from './App.vue'

Vue.use(VueRouter)
Vue.use(VueClipboard)

window.axios = require('axios')
window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
}
window.axios.defaults.transformResponse.push(function (data) {
    if (data.success == false && data.redirect) {
        $router.push({name: data.redirect})
    }
    return data
})

const router = new VueRouter({
    routes: [
        {
            name: 'index',
            path: '/',
            component: require('./components/Index.vue')
        },
        // 用户
        {
            name: 'login',
            path: '/user/login',
            component: require('./components/user/Login.vue')
        },
        {
            name: 'register',
            path: '/user/register',
            component: require('./components/user/Register.vue')
        },
        {
            name: 'logout',
            path: '/user/logout',
            component: require('./components/user/Logout.vue')
        },

        // 区域
        {
            name: 'profile',
            path: '/user/profile',
            component: require('./components/home/Dashboard.vue')
        },
        {
            name: 'record',
            path: '/user/record',
            component: require('./components/home/Dashboard.vue')
        },
        {
            name: 'discount',
            path: '/user/discount',
            component: require('./components/home/Dashboard.vue')
        },
        {
            name: 'region',
            path: '/region/:type?/:id?',
            component: require('./components/home/Dashboard.vue')
        },
        {
            name: 'sale',
            path: '/sale/:id',
            component: require('./components/home/Dashboard.vue')
        },

        // 管理面板
        {
            path: '/admin',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_region_list',
            path: '/admin/region',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_region_create',
            path: '/admin/region/create',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_region',
            path: '/admin/region/:id',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            // 用户列表
            name: 'admin_user',
            path: '/admin/user',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_quick_create_user',
            path: '/admin/user/quick',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_record',
            path: '/admin/user/record',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_user_detail',
            path: '/admin/user/:id',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_user_edit',
            path: '/admin/user/:id/:region_id',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_server',
            path: '/admin/server',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_server_create',
            path: '/admin/server/create',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_server_edit',
            path: '/admin/server/:id',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_commodity',
            path: '/admin/commodity',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_commodity_create',
            path: '/admin/commodity/create',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_commodity_edit',
            path: '/admin/commodity/:id',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_discount',
            path: '/admin/discount',
            component: require('./components/admin/Dashboard.vue')
        },
        {
            name: 'admin_discount_create',
            path: '/admin/discount/create',
            component: require('./components/admin/Dashboard.vue')
        }
    ]
})

const app = new Vue({
    el: "#app",
    render: h => h(App),
    router
})
