<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <loading v-if="loading"></loading>
        <form v-else @keydown.enter="create_commodity">
            <label class="label">区域</label>
            <p class="control">
                <span class="select">
                    <select v-model="commodity.region_id">
                        <option :value="region.id" v-for="region in regions">{{ region.name }}</option>
                    </select>
                </span>
            </p>
            <label class="label">名称</label>
            <p class="control">
                <input class="input" type="text" placeholder="套餐名称" v-model="commodity.name">
            </p>
            <label class="label">价格</label>
            <p class="control">
                <input class="input" type="text" v-model="commodity.price">
            </p>
            <p>流量时间二选一填写，优先按时间套餐计算。</p>
            <div style="padding: 1rem 0 1rem 4rem">
                <label class="label">时间（秒数）</label>
                <p class="control">
                    <input class="input" type="text" placeholder="有效时间秒数" v-model.number="commodity.expiration">
                </p>
                <p class="control has-addons">
                    <a class="button" @click="commodity.expiration = 3600 * 24">
                        <span>一天</span>
                    </a>
                    <a class="button" @click="commodity.expiration = 3600 * 24 * 7">
                        <span>七天</span>
                    </a>
                    <a class="button" @click="commodity.expiration = 3600 * 24 * 30">
                        <span>一月</span>
                    </a>
                    <a class="button" @click="commodity.expiration = 3600 * 24 * 180">
                        <span>六月</span>
                    </a>
                    <a class="button" @click="commodity.expiration = 3600 * 24 * 365">
                        <span>一年</span>
                    </a>
                </p>

                <label class="label">流量</label>
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
            </div>
            <label class="label">介绍</label>
            <p class="control">
                <textarea class="textarea" type="text" placeholder="套餐介绍" v-model="commodity.introduction"></textarea>
            </p>
            <a :class="{'button': true, 'is-loading': save_loading}" @click="create_commodity">保存</a>
        </form>
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
                loading: true,
                save_loading: false,
                regions: [],
                commodity: {
                    expiration: 0
                },
                message: "",

                transfer: {
                    enable: 0,
                    level: 1
                }
            }
        },
        methods: {
            get_region: function () {
                let self = this
                this.loading = true
                axios.get('/region').then(function (response) {
                    self.regions = response.data
                    self.loading = false
                })
            },
            create_commodity: function () {
                let self = this
                this.loading = true
                this.commodity.flow = this.transfer.enable * Math.pow(1024, this.transfer.level)
                axios.post("/admin/commodity/create", {
                    commodity: this.commodity
                }).then(function (response) {
                    self.loading = false
                    self.message = response.data.message
                }).catch(function (error) {
                    self.loading = false
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
            }
        },
        mounted: function () {
            this.get_region()
        }
    }
</script>