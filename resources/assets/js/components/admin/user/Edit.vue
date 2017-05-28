<template>
    <div>
        <loading v-if="loading"></loading>
        <div class="box" v-else>
            <article class="media">
                <div class="media-left">
                    <figure class="image is-64x64">
                        <img :src="avatar(user.email)" alt="Image">
                    </figure>
                </div>
                <div class="media-content">
                    <div class="content">
                        <div class="notification" v-if="message">
                            <button class="delete" @click="message = ''"></button>
                            {{ message }}
                        </div>
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
                            <label class="label">余额</label>
                            <p class="control">
                                <input class="input" type="text" v-model.number="editor_user.balance">
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
        <table v-if="!loading" class="table is-bordered is-striped">
            <thead>
                <tr>
                    <th>区域</th>
                    <th>端口</th>
                    <th>密码</th>
                    <th>可用流量</th>
                    <th>已用流量（上 / 下）</th>
                    <th>最后活跃时间</th>
                    <th>过期时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in region">
                    <th>{{ item.detail.name }}</th>
                    <th>{{ item.user.port }}</th>
                    <th>{{ item.user.passwd }}</th>
                    <th>{{ format_transfer(item.user.transfer_enable - item.user.u - item.user.d) }}</th>
                    <th><div>{{ format_transfer(item.user.u) }}</div>
                        <div>{{ format_transfer(item.user.d) }}</div></th>
                    <th>{{ format_time(item.user.t) }}</th>
                    <th>{{ format_time(item.user.expired_time) }}</th>
                    <th>
                        <router-link class="button" :to="{
                            name: 'admin_user_edit',
                            params: {
                                id: id,
                                region_id: item.detail.id
                            }
                        }">编辑</router-link>
                        <a :class="{'button': true, 'is-loading': enable_loading[item.detail.id]}" @click="change_enable(item)">{{ item.user.enable > 0 ? '禁用' : '启用' }}</a></p>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
    import Loading from '../../layouts/Loading.vue'
    import md5 from 'blueimp-md5'
    import moment from 'moment'

    export default {
        components: {
            Loading
        },
        computed: {
            id: function () {
                return this.$route.params.id
            }
        },
        data: function () {
            return {
                loading: true,
                user: {},
                region: [],
                editor: false,
                editor_user: {},
                editor_loading: false,
                message: "",
                enable_loading: {}
            }
        },
        watch: {
            id: function () {
                this.get_detail()
            }
        },
        mounted: function () {
            this.get_detail()
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
                    wechat: this.user.wechat,
                    balance: this.user.balance
                }
            },
            save_profile: function () {
                let self = this
                this.editor_loading = true
                axios.post("/admin/user/profile", {
                    id: this.id,
                    user: this.editor_user
                }).then(function (response) {
                    self.editor = false
                    self.editor_loading = false
                    if (response.data.user) {
                        self.user = response.data.user
                    }
                    if (response.data.message) {
                        self.message = response.data.message
                    }
                })
            },
            get_detail: function () {
                let self = this
                this.loading = true
                axios.get("/admin/user/detail", {params: {
                    id: this.id
                }}).then(function (response) {
                    self.loading = false
                    self.user = response.data.user
                    self.region = response.data.region
                })
            },
            format_transfer: function (transfer) {
                let units = ["Byte", "KByte", "MByte", "GByte", "TByte", "PByte"],
                    level = 0
                while (transfer > 1023 && level < units.length - 1) {
                    transfer /= 1024
                    level++
                }
                transfer = parseInt(transfer)
                return `${transfer} ${units[level]}`
            },
            change_enable: function (item) {
                let region_id = item.detail.id,
                    username = item.user.username,
                    enable = item.user.enable,
                    self = this

                this.$set(this.enable_loading, item.detail.id, true)
                axios.get("/admin/user/enable", {params: {
                    id: region_id,
                    username: username,
                    enable: item.user.enable ? 0 : 1
                }}).then(function (response) {
                    self.$set(self.enable_loading, item.detail.id, false)
                    self.get_detail()
                }).catch(function (error) {
                    self.$set(self.enable_loading, item.detail.id, false)
                })
            },
            format_time: function (time) {
                return moment(time * 1000).format("YYYY 年 M 月 D 日 H:mm:ss")
            }
        }
    }
</script>