<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <div class="columns">
            <div class="column">
                <label class="label">激活码</label>
                <p class="control has-addons">
                    <input class="input" type="text" v-model.trim="code" placeholder="激活码">
                    <a :class="{'button is-info': true, 'is-loading': loading}" @click="find">确定</a>
                </p>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                loading: false,
                code: "",
                message: ""
            }
        },
        methods: {
            find: function () {
                let self = this
                this.loading = true
                axios.post("/user/discount", {
                    code: this.code
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