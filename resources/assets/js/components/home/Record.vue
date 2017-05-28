<template>
    <div>
        <loading v-if="loading"></loading>
        <div v-else>
            <table class="am-table am-table-bordered am-text-nowrap is-hidden-mobile">
                <thead>
                    <tr>
                        <th>套餐名称</th>
                        <th>剩余流量</th>
                        <th>发生时间</th>
                        <th>到期时间</th>
                        <th>金额</th>
                        <th>备注</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in actives">
                        <th>{{ item.commodity_name }}</th>
                        <th><span v-if="item.flow > 0">{{ format_transfer(item.flow) }}</span></th>
                        <th><span v-if="item.created_at">{{ format_time(item.created_at) }}</span></th>
                        <th>{{ format_time(item.expired_time * 1000) }}</th>
                        <th><span v-if="item.price > 0">￥{{ item.price }}</span></th>
                        <th>{{ item.content }}</th>
                    </tr>
                </tbody>
            </table>
            <div class="is-hidden-desktop">
                <div class="card" v-for="item in actives">
                    <header class="card-header">
                        <p class="card-header-title">
                            套餐名称： {{ item.commodity_name }}
                        </p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p v-if="item.flow > 0">剩余流量： <b>{{ format_transfer(item.flow) }}</b></p>
                            <p v-if="item.created_at">发生时间： <b>{{ format_time(item.created_at) }}</b></p>
                            <p>到期时间： <b>{{ format_time(item.expired_time * 1000) }}</b></p>
                            <p v-if="item.price > 0">金额： <b>￥{{ item.price }}</b></p>
                            <p>备注： <b>{{ item.content }}</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Loading from '../layouts/Loading.vue'
    import moment from 'moment'

    export default {
        components: {
            Loading
        },
        data: function () {
            return {
                loading: true,
                actives: []
            }
        },
        methods: {
            get_actives: function () {
                let self = this
                this.loading = true
                axios.get("/user/active").then(function (response) {
                    self.loading = false
                    self.actives = response.data
                })
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
            },
        },
        mounted: function () {
            this.get_actives()
        }
    }
</script>