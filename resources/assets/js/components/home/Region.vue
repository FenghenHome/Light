<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <loading v-if="loading"></loading>
        <div v-else class="card">
            <div class="card-content">
                <div class="tabs">
                    <ul>
                        <li :class="{'is-active': type == 'overview'}"><router-link :to="{name: 'region', params: {type: 'overview', id: id}}">概览</router-link></li>
                        <li :class="{'is-active': type == 'commodity'}"><router-link :to="{name: 'region', params: {type: 'commodity', id: id}}">购买与续费</router-link></li>
                    </ul>
                </div>
                <div v-if="type == 'overview'">
                    <h1 class="title">{{ region.name }}</h1>
                    <h2 class="subtitle">{{ region.introduction }}</h2>
                    <p class="content" v-if="region.user"><button class="button is-primary is-outlined" @click="reset_password_modal = true">
                        重置密码
                    </button></p>
                    <p v-if="region.user && region.user.enable < 1">你在该区域的账户已被禁用，请联系管理员。</p>
                    <nav class="panel" v-else-if="region.user">
                        <a class="panel-block">
                            端口：{{ region.user.port }}
                        </a>
                        <a class="panel-block">
                            密码：{{ region.user.passwd }}
                        </a>
                        <a class="panel-block is-active" v-if="region.user.expired_time > Date.parse(new Date()) / 1000">
                            过期时间：{{ format_time(region.user.expired_time) }}
                            </a>
                        <a class="panel-block is-active" v-else>
                            可用流量：{{ format_transfer(region.user.transfer_enable - region.user.u - region.user.d) }}
                        </a>
                    </nav>
                    <p v-else>你还没有这个区域的账户。</p>
                    <div class="am-scrollable-horizontal is-hidden-touch">
                        <table class="am-table am-table-bordered am-text-nowrap">
                            <thead>
                                <tr>
                                    <th>服务器名称</th>
                                    <th>地址</th>
                                    <th v-if="region.user">服务器端口</th>
                                    <th v-if="region.user">密码</th>
                                    <th>加密方式</th>
                                    <th>介绍</th>
                                    <th v-if="region.user">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in region.server">
                                    <th>{{ item.name }}</th>
                                    <th>{{ item.address }}</th>
                                    <th v-if="region.user">{{ region.user.port }}</th>
                                    <th v-if="region.user">{{ region.user.passwd }}</th>
                                    <th>{{ item.protocol }}</th>
                                    <th>{{ item.introduction }}</th>
                                    <th v-if="region.user"><a class="button is-primary" @click="show_qrcode(item, region.user)">显示二维码</a></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="is-hidden-desktop">
                        <div class="card" v-for="item in region.server">
                            <header class="card-header">
                                <p class="card-header-title">
                                    服务器名称： {{ item.name }}
                                </p>
                            </header>
                            <div class="card-content">
                                <div class="content">
                                    <p>地址： <b>{{ item.address }}</b></p>
                                    <p v-if="region.user">服务器端口： <b>{{ region.user.port }}</b></p>
                                    <p v-if="region.user">密码： <b>{{ region.user.passwd }}</b></p>
                                    <p>加密方式： <b>{{ item.protocol }}</b></p>
                                    <p>介绍： <b>{{ item.introduction }}</b></p>
                                    <p v-if="region.user"><a class="button is-primary" @click="show_qrcode(item, region.user)">显示二维码</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else-if="type == 'commodity'">
                    <table class="table is-bordered is-striped is-narrow">
                        <thead>
                            <tr>
                                <th>价格</th>
                                <th>名称</th>
                                <th>介绍</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in region.commodity">
                                <th>￥{{ item.price }}</th>
                                <th>{{ item.name }}</th>
                                <th>{{ item.introduction }}</th>
                                <th>
                                    <router-link class="button is-small" :to="{name: 'sale', params: {
                                        id: item.id
                                    }}">
                                        购买
                                    </router-link>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav class="panel" v-else-if="type == 'commodity'">
                    <router-link v-if="type == 'commodity'" class="panel-block" v-for="item in region.commodity" :to="{name: 'sale', params: {
                            id: item.id
                        }}">
                        <span class="panel-icon">￥{{ item.price }}</span>
                        <span class="tag is-primary">{{ item.name }}</span>
                        <p>{{ item.introduction }}</p>
                    </router-link>
                </nav>
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
                                <label class="label">加密方式</label>
                                <p class="control">
                                    <input class="input" :value="active_server.protocol" disabled>
                                </p>
                                <label class="label">端口</label>
                                <p class="control">
                                    <input class="input" :value="region.user.port" disabled>
                                </p>
                                <label class="label">密码</label>
                                <p class="control">
                                    <input class="input" :value="region.user.passwd" disabled>
                                </p>
                            </div>
                        </div>
                        <button class="button is-pulled-right is-primary" @click="qrcode_modal = false">确定</button>
                    </div>
                </div>
            </div>
            <button class="modal-close" @click="qrcode_modal = false"></button>
        </div>
        <div :class="{'modal': true, 'is-active': reset_password_modal}" v-if="reset_password_modal">
            <div class="modal-background"></div>
            <div class="modal-content">
                <div class="card">
                    <div class="card-content" style="overflow: hidden;">
                        <label class="label">是否确认重置密码？</label>
                        <div class="is-pulled-right">
                            <button class="button" @click="reset_password_modal = false">取消</button>
                            <button class="button is-primary" @click="reset_password">确定</button>
                        </div>
                    </div>
                </div>
            </div>
            <button class="modal-close" @click="reset_password_modal = false"></button>
        </div>
    </div>
</template>
<script>
    import Loading from '../layouts/Loading.vue'
    import moment from 'moment'
    import qrcode from 'v-qrcode'
    import store from '../../store'

    export default {
        props: {
            id: {
                type: Number,
                required: true
            }
        },
        data: function () {
            return {
                loading: true,
                region: null,
                active_panel: 'commodity',
                qrcode_modal: false,
                format_ss: "",
                message: "",
                reset_password_modal: false
            }
        },
        components: {
            Loading,
            qrcode
        },
        computed: {
            type: function () {
                return this.$route.params.type
            }
        },
        methods: {
            get_detail: function (id) {
                let self = this
                this.loading = true
                axios.get("/region/detail", {params: {id: id}}).then(function (response) {
                    self.loading = false
                    self.region = response.data
                }).catch(function (error) {
                    self.loading = false
                })
            },
            format_transfer: function (transfer) {
                let units = ["Byte", "KByte", "MByte", "GByte", "TByte", "PByte"],
                    level = 0
                while (transfer > 1023 && level < units.length - 1) {
                    transfer /= 1024
                    level++
                }
                transfer = transfer.toFixed(2)
                return `${transfer} ${units[level]}`
            },
            format_time: function (time) {
                return moment(time * 1000).format("YYYY 年 M 月 D 日 H:mm:ss")
            },
            show_qrcode: function (server) {
                let user = this.region.user
                this.format_ss = 'ss://' + btoa(server.protocol.toLowerCase() + `:${user.passwd}@${server.address}:${user.port}`)
                this.active_server = server
                this.qrcode_modal = true
            },
            reset_password: function () {
                let self = this
                this.loading = true
                this.reset_password_modal = false
                axios.post("/user/password/reset", {
                    id: this.id
                }).then(function (response) {
                    self.loading = false
                    self.message = response.data.message
                    self.get_detail(self.id)
                }).catch(function (error) {
                    self.loading = false
                    self.message = "重置密码失败，网络错误"
                })
            }
        },
        watch: {
            id: function () {
                this.get_detail(this.id)
            }
        },
        mounted: function () {
            this.get_detail(this.id)
            if (store.state.message) {
                this.message = store.state.message
                store.commit("set_message", "")
            }
        }
    }
</script>