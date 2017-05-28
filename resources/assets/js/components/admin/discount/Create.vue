<template>
    <div>
        <div class="notification content" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
            <ul v-if="discounts">
                <li v-for="item in discounts">
                    {{ item.code }}
                </li>
            </ul>
        </div>
        <form @keydown.enter="create_discount">
            <label class="label">区域</label>
            <p class="control">
                <span class="select">
                    <select v-model="region_id">
                        <option :value="region.id" v-for="region in regions">{{ region.name }}</option>
                    </select>
                </span>
            </p>
            <label class="label">套餐</label>
            <p class="control">
                <span class="select">
                    <select v-model="commodity_id">
                        <option :value="commodity.id" v-for="commodity in commodities">{{ commodity.name }}</option>
                    </select>
                </span>
            </p>
            <label class="label">数量</label>
            <p class="control">
                <input class="input" type="text" placeholder="数量" v-model.number="discount.number">
            </p>
            <label class="label">备注</label>
            <p class="control">
                <input class="input" type="text" placeholder="这些激活码会拥有相同的备注" v-model="discount.remark">
            </p>
            <a :class="{'button': true, 'is-loading': loading}" @click="create_discount">生成</a>
        </form>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                loading: false,
                regions: [],
                region_id: 0,
                commodities: [],
                commodity_id: 0,
                discount: {
                    remark: "",
                    number: 1
                },
                discounts: [],
                message: ""
            }
        },
        watch: {
            region_id: function () {
                this.get_commodtiy()
            }
        },
        methods: {
            get_region: function () {
                let self = this
                this.loading = true
                axios.get('/region').then(function (response) {
                    self.loading = false
                    self.regions = response.data
                })
            },
            get_commodtiy: function () {
                let self = this
                this.loading = true
                axios.get('/admin/commodity', {params: {
                    id: this.region_id
                }}).then(function (response) {
                    self.loading = false
                    self.commodities = response.data
                }).catch(function (error) {
                    self.loading = false
                    self.message = "网络错误"
                })
            },
            create_discount: function () {
                let self = this
                this.loading = true
                axios.post("/admin/discount/create", {
                    commodity_id: this.commodity_id,
                    discount: this.discount
                }).then(function (response) {
                    self.loading = false
                    self.message = response.data.message
                    self.discounts = response.data.discounts
                }).catch(function (error) {
                    self.loading = false
                    self.message = "网络错误"
                })
            }
        },
        mounted: function () {
            this.get_region()
        }
    }
</script>