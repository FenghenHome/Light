<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <form @keydown.enter="create_region">
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
            <a :class="{'button': true, 'is-loading': loading}" @click="create_region">创建</a>
        </form>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                loading: false,
                region: {
                    name: "",
                    introduction: "",
                    connection: ""
                },
                message: ""
            }
        },
        methods: {
            create_region: function () {
                let self = this
                this.loading = true
                axios.post("/admin/region/create", {
                    id: this.region_id,
                    region: this.region
                }).then(function (response) {
                    self.loading = false
                    self.message = response.data.message
                }).catch(function (error) {
                    self.loading = false
                    self.message = "网络错误"
                })
            }
        }
    }
</script>