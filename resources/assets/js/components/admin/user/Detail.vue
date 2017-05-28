<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <loading v-if="loading"></loading>
        <div class="box" v-else-if="user && user.name">
            <article class="media">
                <div class="media-left">
                    <figure class="image is-64x64">
                        <img :src="avatar(user.email)" alt="Image">
                    </figure>
                </div>
                <div class="media-content">
                    <div class="content">
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
                                <input class="input" type="text" v-model="editor_user.qq">
                            </p>
                            <label class="label">微信</label>
                            <p class="control">
                                <input class="input" type="text" v-model="editor_user.wechat">
                            </p>
                            <label class="label">余额</label>
                            <p class="control">
                                <input class="input" type="text" v-model="editor_user.balance">
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
        <div v-if="!loading && user && user.name" class="card" v-for="item in region">
            <header class="card-header">
                <p class="card-header-title">
                    {{ item.detail.name }}
                </p>
            </header>
            <div class="card-content">
                <table class="table is-bordered is-striped">
                    <thead>
                        <tr>
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
                        <tr>
                            <th>{{ item.user.port }}</th>
                            <th>{{ item.user.passwd }}</th>
                            <th>{{ format_transfer(item.user.transfer_enable - item.user.u - item.user.d) }}</th>
                            <th>{{ format_transfer(item.user.u) }} / {{ format_transfer(item.user.d) }}</th>
                            <th><span v-if="item.user.t > 0">{{ format_time(item.user.t) }}</span></th>
                            <th><span v-if="item.user.expired_time > 0">{{ format_time(item.user.expired_time) }}</span></th>
                            <th>
                                <router-link class="button" :to="{
                                    name: 'admin_user_edit',
                                    params: {
                                        id: id,
                                        region_id: item.detail.id
                                    }
                                }">编辑</router-link>
                                <a :class="{'button': true, 'is-loading': enable_loading[item.detail.id]}" @click="change_enable(item)">{{ item.user.enable > 0 ? '禁用' : '启用' }}</a></p>
                                <a :class="{'button': true, 'is-loading': delete_loading[item.detail.id]}" @click="remove_user(item)">删除</a></p>
                            </th>
                        </tr>
                    </tbody>
                </table>
                <table class="table is-bordered is-striped">
                    <thead>
                        <tr>
                            <th>名称</th>
                            <th>地址</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="server in item.servers">
                            <th>{{ server.name }}</th>
                            <th>{{ server.address }}</th>
                            <th>
                                <a class="button" v-clipboard:copy="format_text(server, item.user)">帐号</a>
                                <a class="button" @click="show_qrcode(server, item.user)">二维码</a>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>      
        <div :class="{'modal': true, 'is-active': qrcode_modal}" v-if="qrcode_modal">
            <div class="modal-background"></div>
            <div class="modal-content">
                <div class="card">
                    <div class="card-content" style="overflow: hidden;">
                        <label class="label">连接字符串</label>
                        <p class="control">
                            <input class="input" :value="format_ss" disabled>
                        </p>
                        <div class="columns">
                            <qrcode :value="format_ss" :size="288" class="column"></qrcode>
                            <div class="column">
                                <label class="label">地址</label>
                                <p class="control">
                                    <input class="input" :value="active_server.address" disabled>
                                </p>
                                <label class="label">协议</label>
                                <p class="control">
                                    <input class="input" :value="active_server.protocol" disabled>
                                </p>
                                <label class="label">端口</label>
                                <p class="control">
                                    <input class="input" :value="active_user.port" disabled>
                                </p>
                                <label class="label">密码</label>
                                <p class="control">
                                    <input class="input" :value="active_user.passwd" disabled>
                                </p>
                            </div>
                        </div>
                        <button class="button is-pulled-right is-primary" @click="qrcode_modal = false">确定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Loading from '../../layouts/Loading.vue'
    import md5 from 'blueimp-md5'
    import moment from 'moment'
    import qrcode from 'v-qrcode'

    export default {
        components: {
            Loading,
            qrcode
        },
        computed: {
            id: function () {
                return this.$route.params.id
            },
            format_qrcode: function () {

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
                enable_loading: {},
                delete_loading: {},
                active_server: null,
                active_user: null,
                format_ss: "",
                qrcode_modal: false
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
                    if (response.data.success) {
                        self.user = response.data.user
                        self.region = response.data.region
                        self.message = ""
                    } else {
                        self.message = response.data.message
                    }
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
            },
            stringify: function (object) {
                return JSON.stringify(object)
            },
            format_text: function (server, user) {
                return `服务器地址：${server.address}
协议：${server.protocol}
端口：${user.port}
密码：${user.passwd}`
            },
            show_qrcode: function (server, user) {
                this.format_ss = 'ss://' + btoa(server.protocol.toLowerCase() + `:${user.passwd}@${server.address}:${user.port}`)
                this.active_server = server
                this.active_user = user
                this.qrcode_modal = true
            },
            remove_user: function (user) {
                let region_id = user.detail.id,
                    region_user_id = user.user.id,
                    self = this

                this.$set(this.delete_loading, region_id, true)

                axios.post("/admin/user/region_user/delete", {
                    region_id: region_id,
                    region_user_id: region_user_id
                }).then(function (response) {
                    self.$set(self.delete_loading, region_id, false)
                    self.get_detail()
                }).catch(function (error) {
                    self.$set(self.delete_loading, region_id, false)
                })
            }
        }
    }
</script>