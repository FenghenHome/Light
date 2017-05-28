<template>
    <section class="section">
        <loading v-if="loading"></loading>
        <div v-else class="columns is-mobile">
            <div class="column is-half is-offset-one-quarter">  
                已经清理在这台设备上的用户信息，你可以选择重新登录或是离开。
            </div>
        </div>
    </section>
</template>
<script>
    import store from '../../store'
    import Loading from '../layouts/Loading.vue'

    export default {
        components: {
            Loading
        },
        data: function () {
            return {
                username: "",
                password: "",
                message: "",
                loading: true
            }
        },
        mounted: function () {
            let self = this
            store.commit('set_user', null)
            axios.post('/user/logout').then(function (response) {
                self.loading = false
            }).catch(function (error) {
                self.loading = false
            })
        }
    }
</script>