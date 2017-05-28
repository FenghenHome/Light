<template>
    <section>
        <loading v-if="loading"></loading>
        <div v-else class="card">
            <div class="card-content">
                <div class="notification is-primary" v-if="message">{{ message }}</div>
                <div class="content">
                    <p>确认订单信息：</p>
                    <ul>
                        <li>价格：<strong>￥{{ commodity.price }}</strong></li>
                        <li v-if="commodity.expiration">购买后到期时间：<strong>{{ format_time(commodity.expiration, commodity.region.user) }}</strong></li>
                        <li v-else>流量：<strong>{{ format_transfer(commodity.flow) }}</strong></li>
                        <li>介绍：<strong>{{ commodity.introduction }}</strong></li>
                        <li>区域：<router-link :to="{name: 'region', params: {
                                id: commodity.region.id,
                                type: 'commodity'
                            }}">{{ commodity.region.name }}</router-link></li>
                    </ul>
                    <p v-if="!commodity.expiration && commodity.region.user && commodity.region.user.expired_time > Date.parse(new Date()) / 1000">
                        你的账户是时间计费，但套餐是流量计费，你不能购买这个套餐。
                    </p>
                    <a v-else :class="{'button is-success': true, 'is-loading': buy_loading}" @click="buy">购买</a>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
    import Loading from '../layouts/Loading.vue'
    import moment from 'moment'
    import store from '../../store'

    export default {
        components: {
            Loading
        },
        data: function () {
            return {
                commodity: null,
                message: "",
                loading: true,
                buy_loading: false
            }
        },
        computed: {
            id: function () {
                return this.$route.params.id
            }
        },
        watch: {
            id: function () {
                this.get_detail(this.id)
            }
        },
        methods: {
            get_detail: function (id) {
                let self = this
                this.loading = true
                axios.get("/commodity/detail", {params: {
                    id: id
                }}).then(function (response) {
                    self.loading = false
                    self.commodity = response.data
                })
            },
            format_transfer: function (transfer) {
                let units = ["Byte", "KByte", "MByte", "GByte", "TByte", "PByte"],
                    level = 0
                while (transfer > 1023 && level < units.length - 1) {
                    transfer /= 1024
                    level++
                }
                return transfer + units[level]
            },
            format_time: function (time, user) {
                if (user && user.expired_time > Date.parse(new Date()) / 1000) {
                    return moment(0).add(user.expired_time + time, 'seconds').format("YYYY 年 M 月 D 日 H 时 mm 分 ss 秒")
                } else {
                    return moment().add(time, 'seconds').format("YYYY 年 M 月 D 日 H 时 mm 分 ss 秒")
                }
            },
            buy: function () {
                let self = this
                this.message = ""
                this.buy_loading = true
                axios.post("/commodity/buy", {
                    id: this.id
                }).then(function (response) {
                    self.buy_loading = false
                    if (response.data.success) {
                        self.$router.push({name: 'region', params: {
                            id: self.commodity.region_id,
                            type: 'overview'
                        }})
                        store.commit("set_message", response.data.message)
                    }
                    self.message = response.data.message
                }).catch(function (error) {
                    self.buy_loading = false
                    self.message = "网络错误"
                })
            }
        },
        mounted: function () {
            this.get_detail(this.id)
        }
    }
</script>