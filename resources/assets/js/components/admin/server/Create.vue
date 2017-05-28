<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <form @keydown.enter="create_server">
            <label class="label">区域</label>
            <p class="control">
                <span class="select">
                    <select v-model="server.region_id">
                        <option :value="region.id" v-for="region in regions">{{ region.name }}</option>
                    </select>
                </span>
            </p>
            <label class="label">名称</label>
            <p class="control">
                <input class="input" type="text" placeholder="服务器名称" v-model="server.name">
            </p>
            <label class="label">协议</label>
            <p class="control">
                <input class="input" type="text" placeholder="AES-256-CFB" v-model="server.protocol">
            </p>
            <label class="label">介绍</label>
            <p class="control">
                <input class="input" type="text" placeholder="服务器介绍" v-model="server.introduction">
            </p>
            <label class="label">地址</label>
            <p class="control">
                <input class="input" type="text" placeholder="服务器地址" v-model="server.address">
            </p>
            <a :class="{'button': true, 'is-loading': loading}" @click="create_server">创建</a>
        </form>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                loading: false,
                regions: [],
                server: {
                    region_id: null,
                    name: "",
                    protocol: "AES-256-CFB",
                    introduction: "",
                    address: ""
                },
                message: ""
            }
        },
        methods: {
            get_region: function () {
                let self = this
                this.loading = true
                axios.get('/region').then(function (response) {
                    self.loading = false
                    self.regions = response.data
                    self.$set(self.server, "region_id", self.regions[0].id)
                })
            },
            create_server: function () {
                let self = this
                this.loading = true
                axios.post("/admin/server/create", {
                    server: this.server
                }).then(function (response) {
                    self.loading = false
                    self.message = response.data.message
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