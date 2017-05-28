<template>
    <div>
        <index v-if="$route.name == 'index'"></index>
        <navbar v-if="$route.name != 'index'"></navbar>
        <div v-if="$route.name != 'index'" :class="{'container': !full}">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    import store from './store.js'
    import moment from 'moment'
    import Navbar from './components/layouts/Navbar.vue'
    import Index from './components/Index'

    export default {
        components: {
            Navbar,
            Index
        },
        computed: {
            user: function () {
                return store.state.user
            },
            full: function () {
                return store.state.full
            }
        },
        mounted: function () {
            window.$router = this.$router
            store.dispatch("get_user_noredirect")
        }
    }
</script>