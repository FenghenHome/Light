<template>
    <div>
        <div class="notification content" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <form @keydown.enter="create_user">
            <label class="label">名称<span style="color: red;">*</span></label>
            <p class="control">
                <input class="input" type="text" placeholder="用户名" v-model="user.name">
            </p>
            <label class="label">密码<span style="color: red;">*</span></label>
            <p class="control">
                <input class="input" type="text" placeholder="密码" v-model="user.password">
            </p>
            <label class="label">邮箱</label>
            <p class="control">
                <input class="input" type="text" placeholder="邮箱" v-model="user.email">
            </p>
            <label class="label">QQ</label>
            <p class="control">
                <input class="input" type="text" placeholder="QQ" v-model="user.qq">
            </p>
            <label class="label">微信</label>
            <p class="control">
                <input class="input" type="text" placeholder="密码" v-model="user.wechat">
            </p>
            <label class="label">区域<span style="color: red;">*</span></label>
            <p class="control">
                <span class="select">
                    <select v-model="region_id">
                        <option :value="region.id" v-for="region in regions">{{ region.name }}</option>
                    </select>
                </span>
            </p>
            <label class="label">套餐<span style="color: red;">*</span></label>
            <p class="control">
                <span class="select">
                    <select v-model="commodity_id">
                        <option :value="commodity.id" v-for="commodity in commodities">{{ commodity.name }}</option>
                    </select>
                </span>
            </p>
            <a :class="{'button': true, 'is-loading': loading}" @click="create_user">创建用户</a>
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
                user: {
                    name: "",
                    password: "",
                    email: "",
                    qq: "",
                    wechat: ""
                },
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
            create_user: function () {
                let self = this
                this.loading = true
                axios.post("/admin/user/create", {
                    user: this.user,
                    region_id: this.region_id,
                    commodity_id: this.commodity_id
                }).then(function (response) {
                    self.loading = false
                    self.message = response.data.message
                    if (response.data.region_user) {
                        self.$router.push({
                            name: "admin_user_detail",
                            params: {
                                id: response.data.user.id
                            }
                        })
                    }
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