<template>
    <div class="box" v-if="user">
        <article class="media">
            <div class="media-left">
                <figure class="image is-64x64">
                    <img :src="avatar(user.email)" alt="Image">
                </figure>
            </div>
            <div class="media-content">
                <div class="content">
                    <div class="notification" v-if="message">{{ message }}</div>
                    <div v-if="!editor">
                        <strong>@{{ user.name }}</strong> <small>{{ user.email }}</small>
                        <div>余额：<strong>￥{{ user.balance }}</strong></div>
                        <div v-if="user.qq">QQ：<strong>{{ user.qq }}</strong></div>
                        <div v-if="user.wechat">微信：<strong>{{ user.wechat }}</strong></div>
                    </div>
                    <div v-else>
                        <label class="label">名称</label>
                        <p class="control">
                            <input class="input" type="text" v-model="editor_user.name" disabled>
                        </p>
                        <label class="label">邮箱</label>
                        <p class="control">
                            <input class="input" type="email" v-model="editor_user.email">
                        </p>
                        <label class="label">QQ</label>
                        <p class="control">
                            <input class="input" type="text" v-model.number="editor_user.qq">
                        </p>
                        <label class="label">微信</label>
                        <p class="control">
                            <input class="input" type="text" v-model="editor_user.wechat">
                        </p>
                        <label class="label">登录密码</label>
                        <p class="control">
                            <input class="input" type="text" placeholder="留空表示不修改，修改成功后会注销用户" v-model="editor_user.password">
                        </p>
                    </div>
                </div>
                <div v-if="!editor">
                    <a class="button" @click="display_editor">编辑</a></p>
                </div>
                <div v-else>
                    <a class="button" :class="{'is-loading': editor_loading}" @click="save_profile">保存</a></p>
                </div>
            </div>
        </article>
    </div>
</template>
<script>
    import store from '../../store'
    import Loading from '../layouts/Loading.vue'
    import md5 from 'blueimp-md5'

    export default {
        components: {
            Loading
        },
        data: function () {
            return {
                editor: false,
                editor_user: {},
                editor_loading: false,
                message: ""
            }
        },
        computed: {
            user: function () {
                return store.state.user
            }
        },
        methods: {
            avatar: function (email) {
                return "https://cdn.v2ex.com/gravatar/" + md5(email)
            },
            display_editor: function () {
                this.editor = !this.editor
                this.editor_user = {
                    name: this.user.name,
                    email: this.user.email,
                    qq: this.user.qq,
                    wechat: this.user.wechat
                }
            },
            save_profile: function () {
                if (this.editor_user.email && !this.checkEmail(this.editor_user.email)) {
                    this.message = "邮箱格式不正确"
                    return
                }
                this.message = ""
                let self = this
                this.editor_loading = true
                axios.post("/user/profile", {
                    user: this.editor_user
                }).then(function (response) {
                    self.editor = false
                    self.editor_loading = false
                    if (response.data.success) {
                        if (self.editor_user.password) {
                            self.$router.push({name: "logout"})
                        }
                        store.commit('set_user', response.data.user)
                    }
                    if (response.data.message) {
                        self.message = response.data.message
                    }
                })
            },
            checkEmail: function (emailStr) {
                let start = 0,
                    end = emailStr.length;
                while (start < end) { //判断是否含有除"@"和"."之外的特殊字符和中文字符
                    var charcode = emailStr.charCodeAt(start)
                    if (!(charcode == 45 || charcode == 46 ||
                        (charcode >= 48 & charcode <= 59) ||
                        (charcode >= 64 & charcode <= 90) || (charcode >= 97 & charcode <= 122))) {
                        //alert("有非法字符"+emailStr.charAt(start))
                        return false
                    }
                    start++
                }

                let emailStrArr = emailStr.split("@")
                if (emailStrArr.length != 2) { //判断@的个数是否为有且仅有一个
                    return false
                } else if (emailStrArr[0] == '' || emailStrArr[1] == '') { //判断"@"的位置是否正确
                    return false
                } else {
                    if (emailStrArr[0].split(".").length > 1) { //判断@前面的字符串是否含有"."
                        return false
                    }
                    let emailStr2Arr = emailStrArr[1].split(".")
                    if (emailStr2Arr.length < 2) { //判断@后面的字符串是否含有"."
                        return false
                    } else if (emailStr2Arr[0] == '' || emailStr2Arr[emailStr2Arr.length] == '') { //判断"."的位置是否正确
                        return false
                    } else if (!(emailStr2Arr[emailStr2Arr.length - 1] == 'com' ||
                        emailStr2Arr[emailStr2Arr.length - 1] == 'cn' ||
                        emailStr2Arr[emailStr2Arr.length - 1] == 'gov' ||
                        emailStr2Arr[emailStr2Arr.length - 1] == 'edu' ||
                        emailStr2Arr[emailStr2Arr.length - 1] == 'net' ||
                        emailStr2Arr[emailStr2Arr.length - 1] == 'org' ||
                        emailStr2Arr[emailStr2Arr.length - 1] == 'int' ||
                        emailStr2Arr[emailStr2Arr.length - 1] == 'mil')) { //判断域名后缀名是否正确
                        return false
                    }
                }
                return true
            }
        }
    }
</script>