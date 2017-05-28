<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <form @keydown.enter="search" @submit.prevent>
            <div class="columns">
                <div class="column">
                    <label class="label">搜索条件</label>
                    <p class="control has-addons">
                        <input class="input" type="text" placeholder="名称 / 介绍" v-model="search_query">
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
                    <th>区域名称</th>
                    <th>名称</th>
                    <th>介绍</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="commodity in commodities">
                    <th>{{ commodity.id }}</th>
                    <th><router-link :to="{
                            name: 'admin_region',
                            params: {
                                id: commodity.region.id
                            }
                        }">{{ commodity.region.name }}</router-link></th>
                    <th>{{ commodity.name }}</th>
                    <th>{{ commodity.introduction }}</th>
                    <th><router-link :to="{name: 'admin_commodity_edit', params: {
                        id: commodity.id
                    }}" class="button is-primary">编辑</router-link>
                        <a :class="{'button is-danger': true, 'is-loading': delete_loading[commodity.id]}" @click="delete_commodity(commodity.id)">删除</a>
                    </th>
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
                message: "",
                search_query: "",
                commodities: [],
                loading: true,
                page: 1,
                last_page: 1,
                delete_loading: {}
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
                axios.get("/admin/commodity", {params: {
                    query: this.search_query,
                    page: this.page
                }}).then(function (response) {
                    self.loading = false
                    self.commodities = response.data.data
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
            },
            delete_commodity: function (id) {
                let self = this
                this.$set(this.delete_loading, id, true)
                axios.post("/admin/commodity/delete", {
                    id: id
                }).then(function (response) {
                    self.$set(self.delete_loading, id, false)
                    if (response.data.success) {
                        self.search()
                        self.message = ""
                    } else {
                        self.message = response.data.message
                    }
                })
            }
        },
        mounted: function () {
            this.search()
        }
    }
</script>