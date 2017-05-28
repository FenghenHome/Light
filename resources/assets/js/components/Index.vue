<template>
    <div>
        <section class="hero is-dark is-bold is-fullheight">
            <div class="hero-head">
                <navbar></navbar>
            </div>

            <div class="hero-body">
                <div class="container has-text-centered">
                    <h1 class="title">
                        Light
                    </h1>
                </div>
            </div>

            <div class="hero-foot" v-if="!user">
                <nav class="tabs">
                    <div class="container">
                        <ul>
                            <li><router-link class="button is-primary" :to="{
                                name: 'register'
                            }">注册</router-link></li>
                        </ul>
                    </div>
                </nav>
            </div>
        </section>
    </div>
</template>
<script>
    import store from '../store'
    import Navbar from './layouts/Navbar.vue'

    export default {
        components: {
            Navbar
        },
        computed: {
            user: function () {
                return store.state.user
            }
        }
    }
</script>