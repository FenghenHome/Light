<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <loading v-if="loading"></loading>
        <form v-else @keydown.enter="create_region">
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
            <a :class="{'button': true, 'is-loading': save_loading}" @click="save_server">保存</a>
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
                server: {},
                message: ""
            }
        },
        methods: {
            get_region: function () {
                let self = this
                this.loading = true
                axios.get('/region').then(function (response) {
                    self.regions = response.data
                    self.get_detail()
                })
            },
            get_detail: function () {
                let self = this
                axios.get('/admin/server/detail', {params: {
                    id: this.$route.params.id
                }}).then(function (response) {
                    self.loading = false
                    self.server = response.data.server
                })
            },
            save_server: function () {
                let self = this
                this.save_loading = true
                axios.post("/admin/server/update", {
                    id: this.$route.params.id,
                    server: this.server
                }).then(function (response) {
                    self.save_loading = false
                    self.server = response.data.server
                    self.message = response.data.message
                }).catch(function (error) {
                    self.save_loading = false
                    self.message = "网络错误"
                })
            }
        },
        mounted: function () {
            this.get_region()
        }
    }
</script>