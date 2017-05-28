<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <loading v-if="loading"></loading>
        <form v-else-if="region" @keydown.enter="save_region">
            <label class="label">名称（必填）</label>
            <p class="control">
                <input class="input" type="text" placeholder="区域名称" v-model="region.name">
            </p>
            <label class="label">介绍</label>
            <p class="control">
                <input class="input" type="text" placeholder="区域介绍" v-model="region.introduction">
            </p>
            <label class="label">数据库信息（必填）</label>
            <p class="control">
                <textarea class="textarea" type="text" placeholder="数据库连接字符串" v-model="region.connection"></textarea>
            </p>
            <a :class="{'button': true, 'is-loading': save_loading}" @click="save_region">保存</a>
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
                region: null,
                message: ""
            }
        },
        computed: {
            region_id: function () {
                return this.$route.params.id
            }
        },
        watch: {
            region_id: function () {
                this.get_region()
            }
        },
        methods: {
            get_region: function () {
                let self = this
                this.message = ""
                this.loading = true
                axios.get("/admin/region/detail", {params: {
                    id: this.region_id
                }}).then((response) => {
                    this.loading = false
                    if (response.data.success) {
                        this.region = response.data.region
                    } else {
                        this.message = response.data.message
                    }
                }).catch((error) => {
                    self.loading = false
                    self.message = "网络错误"
                })
            },
            save_region: function () {
                let self = this
                this.save_loading = true
                axios.post("/admin/region/update", {
                    id: this.region_id,
                    region: this.region
                }).then(function (response) {
                    self.save_loading = false
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