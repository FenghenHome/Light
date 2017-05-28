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
                    <div class="control is-grouped">
                        <p class="control is-expanded">
                            <input class="input" type="text" placeholder="名称 / 套餐名称 / 备注" v-model="search_query">
                        </p>
                        <p class="control is-expanded">
                            <input class="input" type="text" placeholder="激活码 / 激活码备注" v-model="search_code">
                        </p>
                    </div>
                    <a :class="{'button': true, 'is-loading': loading}" @click="search">确定</a>
                </div>
            </div>
        </form>
        <loading v-if="loading"></loading>
        <table v-else class="table is-bordered is-striped" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>名称</th>
                    <th>套餐名称</th>
                    <th>剩余流量</th>
                    <th>发生时间</th>
                    <th>到期时间</th>
                    <th>金额</th>
                    <th>备注</th>
                    <th>激活码</th>
                    <th>激活码备注</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="record in records">
                    <th>{{ record.username }}</th>
                    <th>{{ record.commodity_name }}</th>
                    <th><span v-if="record.flow > 0">{{ format_transfer(record.flow) }}</span></th>
                    <th><span v-if="record.created_at">{{ format_time(record.created_at) }}</span></th>
                    <th><span v-if="record.expired_time">{{ format_time(record.expired_time * 1000) }}</span></th>
                    <th><span v-if="record.price > 0">￥{{ record.price }}</span></th>
                    <th>{{ record.content }}</th>
                    <th>{{ record.code }}</th>
                    <th>{{ record.discount_remark }}</th>
                    <th><router-link :to="{name: 'admin_user_detail', params: {
                        id: record.user_id
                    }}" class="button is-primary">编辑</router-link></th>
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
    import moment from 'moment'

    export default {
        components: {
            Loading
        },
        data: function () {
            return {
                message: "",
                search_query: "",
                search_code: "",
                records: [],
                loading: true,
                page: 1,
                last_page: 1,
                delete_loading: {},
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
                axios.get("/admin/user/record", {params: {
                    query: this.search_query,
                    code: this.search_code,
                    page: this.page
                }}).then(function (response) {
                    self.loading = false
                    self.records = response.data.data
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
            format_time: function (time) {
                return moment(time).format("YYYY 年 M 月 D 日 H:mm:ss")
            },
            format_transfer: function (transfer) {
                let units = ["Byte", "KByte", "MByte", "GByte", "TByte", "PByte"],
                    level = 0
                while (transfer > 1023 && level < units.length - 1) {
                    transfer /= 1024
                    level++
                }
                transfer = parseInt(transfer)
                return `${transfer} ${units[level]}`
            }
        },
        mounted: function () {
            this.search()
        }
    }
</script>