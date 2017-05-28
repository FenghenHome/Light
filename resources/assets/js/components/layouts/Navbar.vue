<template>
    <nav class="nav has-shadow">
        <div class="container">
            <div class="nav-left">
                <router-link class="nav-item" to="/">
                    Light
                </router-link>
                
                <router-link v-for="(text, key) in main_menu" v-bind:class="{'nav-item is-tab is-hidden-mobile': true, 'is-active': key == name}" :to="{name: key}">
                    {{ text }}
                </router-link>
            </div>
            <span v-bind:class="{'nav-toggle': true, 'is-active': display_bar}" @click="display_bar = !display_bar">
                <span></span>
                <span></span>
                <span></span>
            </span>
            <div v-bind:class="{'nav-right nav-menu': true, 'is-active': display_bar}">
                <router-link v-for="(text, key) in main_menu" v-bind:class="{'nav-item is-tab is-hidden-tablet': true, 'is-active': key == name}" :to="{name: key}">
                    {{ text }}
                </router-link>
                <router-link v-for="(text, key) in right_menu" v-bind:class="{'nav-item is-tab': true, 'is-active': key == name}" :to="{name: key}">
                    {{ text }}
                </router-link>
            </div>
        </div>
    </nav>
</template>
<script>
    import store from '../../store.js'

    export default {
        data: function () {
            return {
                main_menu: {},
                display_bar: false
            }
        },
        watch: {
            $route: function () {
                this.display_bar = false
            }
        },
        computed: {
            name: function () {
                return this.$route.name
            },
            user: function () {
                return store.state.user
            },
            right_menu: function () {
                let user = this.user
                if (user) {
                    return {
                        'index': '首页',
                        'profile': user.name,
                        'logout': '退出'
                    }
                } else {
                    return {
                        'index': '首页',
                        'login': '登录',
                        'register': '注册'
                    }
                }
            }
        },
        mounted: function () {

        }
    }
</script>