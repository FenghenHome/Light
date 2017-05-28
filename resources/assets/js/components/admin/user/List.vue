<template>
    <div>
        <form @keydown.enter="search" @submit.prevent>
            <div class="columns">
                <div class="column">
                    <label class="label">搜索条件</label>
                    <p class="control has-addons">
                        <input class="input" type="text" placeholder="名称 / 邮箱 / QQ / 微信" v-model="search_query">
                        <a :class="{'button': true, 'is-loading': loading}" @click="search">确定</a>
                    </p>
                </div>
            </div>
        </form>
        <loading v-if="loading"></loading>
        <table v-else class="table is-bordered is-striped" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>邮箱</th>
                    <th>QQ</th>
                    <th>微信</th>
                    <th>余额</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users">
                    <th>{{ user.id }}</th>
                    <th>{{ user.name }}</th>
                    <th>{{ user.email }}</th>
                    <th>{{ user.qq }}</th>
                    <th>{{ user.wechat }}</th>
                    <th>￥{{ user.balance }}</th>
                    <th><router-link :to="{name: 'admin_user_detail', params: {
                        id: user.id
                    }}" class="button is-primary">详情</router-link></th>
                </tr>
            </tbody>
        </table>
        <p>共 {{ last_page }} 页</p>
        <nav class="pagination is-centered" v-if="!loading" style="margin-top: 1rem">
            <a class="pagination-previous" @click="page = 1">第一页</a>
            <a class="pagination-next" @click="page = last_page">最后一页</a>
            <ul class="pagination-list">
                <li v-for="i in range(page - 3, page + 3)"><a :class="{'pagination-link': true, 'is-current': page == i + 1}" @click="page = i + 1">{{ i + 1 }}</a></li>
            </ul>
        </nav>
    </div>
</template>
<script>
    import Loading from '../../layouts/Loading.vue'

    export default {
        components: {
            Loading
        },
        data: function () {
            return {
                search_query: "",
                page: 1,
                users: [],
                loading: true,
                last_page: 1
            }
        },
        watch: {
            page: function () {
                this.search()
            }
        },
        methods: {
            search: function () {
                let self = this
                this.loading = true
                axios.get("/admin/user", {params: {
                    query: this.search_query,
                    page: this.page
                }}).then(function (response) {
                    let data = response.data.data
                    self.loading = false
                    self.users = data
                    self.last_page = response.data.last_page
                })
            },
            range: function (start, end) {
                if (start < 1) {
                    start = 1
                }
                if (end > this.last_page) {
                    end = this.last_page
                }
                return Array(end - start + 1).fill().map((_, i) => start + i - 1)
            }
        },
        mounted: function () {
            this.search()
        }
    }
</script>