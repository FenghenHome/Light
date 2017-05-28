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
                        <input class="input" type="text" v-model="search_query">
                        <a :class="{'button': true, 'is-loading': loading}" @click="search">确定</a>
                    </p>
                    <p>搜索支持激活码 / 套餐名称 / 备注。</p>
                </div>
            </div>
        </form>
        <loading v-if="loading"></loading>
        <table v-else class="table is-bordered is-striped" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>使用者</th>
                    <th>激活码</th>
                    <th>套餐名称</th>
                    <th>备注</th>
                    <th>创建时间</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="discount in discounts">
                    <th>{{ discount.username }}</th>
                    <th>{{ discount.code }}</th>
                    <th>{{ discount.name }}</th>
                    <th>{{ discount.remark }}</th>
                    <th>{{ discount.created_at }}</th>
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
                discounts: [],
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
                axios.get("/admin/discount", {params: {
                    query: this.search_query,
                    page: this.page
                }}).then(function (response) {
                    self.loading = false
                    self.discounts = response.data.data
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