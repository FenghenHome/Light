<template>
    <section class="section">
        <loading v-if="loading"></loading>
        <div class="columns" v-else>
            <div class="column is-half is-offset-one-quarter">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">
                            登录
                        </p>
                    </header>
                    <div class="card-content" @keydown.enter="login">
                        <div class="notification" v-if="message">
                            <button class="delete" @click="message = ''"></button>
                            {{ message }}
                        </div>
                        <label class="label">名称</label>
                        <p class="control">
                            <input class="input" type="text" placeholder="名称" v-model="username">
                        </p>
                        <label class="label">密码</label>
                        <p class="control">
                            <input class="input" type="password" placeholder="密码" v-model="password">
                        </p>
                    </div>
                    <footer class="card-footer">
                        <a class="card-footer-item" @click="login">确定</a>
                    </footer>
                </div>
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
                loading: false
            }
        },
        computed: {
            user: function () {
                return store.state.user
            }
        },
        watch: {
            user: function () {
                if (this.user) {
                    this.$router.push({name: 'profile'})
                }
            }
        },
        methods: {
            login: function () {
                let self = this
                this.loading = true
                axios.post('/user/login', {
                    username: this.username,
                    password: this.password
                }).then(function (response) {
                    self.loading = false
                    if (!response.data.success) {
                        self.message = response.data.message
                    } else {
                        store.commit('set_user', response.data.user)
                    }
                }).catch(function (error) {
                    self.loading = false
                    self.message = "网络错误"
                })
            }
        },
        mounted: function () {
            if (this.user) {
                this.message = "你已经登录了，但你可以选择再次登录以切换账户"
            }
        }
    }
</script>