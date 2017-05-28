<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <loading v-if="loading"></loading>
        <form v-else-if="region_user">
            <label class="label">区域名称</label>
            <p class="control">
                <input class="input" type="text" placeholder="区域名称" v-model="detail.name" disabled>
            </p>
            <label class="label">端口（一般情况下不要修改）</label>
            <p class="control">
                <input class="input" type="text" placeholder="端口" v-model.number="region_user.port">
            </p>
            <label class="label">密码</label>
            <p class="control">
                <input class="input" type="text" placeholder="密码" v-model="region_user.passwd">
            </p>
            <label class="label">可用流量</label>
            <p class="control has-addons">
                <input class="input" type="text" placeholder="可用流量" v-model.number="transfer.enable">
                <span class="select">
                    <select v-model="transfer.level">
                        <option value="0">Byte</option>
                        <option value="1">KByte</option>
                        <option value="2">MByte</option>
                        <option value="3">GByte</option>
                        <option value="4">TByte</option>
                        <option value="5">PByte</option>
                    </select>
                </span>
            </p>
            <label class="label">到期时间</label>
            <p class="control">
                <input class="input" type="text" :placeholder="'到期时间：' + format_time()" v-model="expired_time">
            </p>
            <a :class="{'button': true, 'is-loading': save_loading}" @click="save_region">保存</a>
        </form>
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
                detail: {},
                user: {},
                region_user: {},
                message: "",
                loading: true,
                save_loading: false,

                transfer: {
                    enable: 0,
                    level: 1
                },
                expired_time: ""
            }
        },
        computed: {
            id: function () {
                return this.$route.params.id
            },
            region_id: function () {
                return this.$route.params.region_id
            }
        },
        methods: {
            get_region: function () {
                let self = this
                this.loading = true
                axios.get("/admin/user/region", {
                    params: {
                        user_id: this.id,
                        region_id: this.region_id
                    }
                }).then(function (response) {
                    self.message = ""
                    self.loading = false
                    self.detail = response.data.detail
                    self.user = response.data.user
                    let region_user = self.region_user = response.data.region_user
                    self.transfer = self.format_transfer(region_user.transfer_enable - region_user.u - region_user.d)
                    self.expired_time = self.format_time(region_user.expired_time)
                })
            },
            save_region: function () {
                let self = this
                this.save_loading = true
                axios.post("/admin/user/region_user", {
                    user_id: this.id,
                    region_id: this.region_id,
                    user: {
                        port: this.region_user.port,
                        passwd: this.region_user.passwd,
                        transfer_enable: this.region_user.u + this.region_user.d + this.transfer.enable * Math.pow(1024, this.transfer.level),
                        expired_time: moment(this.expired_time).unix()
                    }
                }).then(function (response) {
                    self.save_loading = false
                    if (response.data.success) {
                        self.get_region()
                    } else {
                        self.message = response.data.message
                    }
                }).catch(function (error) {
                    self.save_loading = false
                    self.message = "网络错误"
                })
            },
            format_transfer: function (transfer) {
                let units = ["Byte", "KByte", "MByte", "GByte", "TByte", "PByte"],
                    level = 0
                while (transfer > 1023 && level < units.length - 1) {
                    transfer /= 1024
                    level++
                }
                // units[level]
                return {
                    enable: transfer,
                    level: level
                }
            },
            format_time: function (time) {
                let now = moment().unix()
                if (!time) {
                    return moment().format('YYYY-M-D')
                } else if (time > now) {
                    return moment(time * 1000).format('YYYY-M-D')
                }
                
            }
        },
        mounted: function () {
            this.get_region()
        }
    }
</script>