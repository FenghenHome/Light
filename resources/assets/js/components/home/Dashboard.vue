<template>
    <section class="section">
        <loading v-if="loading"></loading>
        <div v-else class="columns">
            <div class="column is-one-quarter">
                <aside class="menu">
                    <p class="menu-label">
                        账户
                    </p>
                    <ul class="menu-list">
                        <li>
                            <router-link :to="{name: 'profile'}" :class="{
                                'is-active': $route.name == 'profile'
                            }">信息</router-link>
                            <router-link :to="{name: 'record'}" :class="{
                                'is-active': $route.name == 'record'
                            }">订单</router-link>
                            <router-link :to="{name: 'discount'}" :class="{
                                'is-active': $route.name == 'discount'
                            }">激活码</router-link>
                        </li>
                    </ul>
                    <p class="menu-label">
                        区域
                    </p>
                    <ul class="menu-list" v-if="regions">
                        <li v-for="region in regions">
                            <router-link :to="{name: 'region', params: {
                                id: region.id,
                                type: 'overview'
                            }}" :class="{
                                'is-active': $route.name == 'region' && region_id == region.id
                            }">{{ region.name }}</router-link>
                        </li>
                    </ul>
                </aside>
            </div>
            <div class="column">
                <region v-if="$route.name == 'region'" :id="parseInt(region_id)"></region>
                <profile v-else-if="$route.name == 'profile'"></profile>
                <sale v-else-if="$route.name == 'sale'"></sale>
                <record v-else-if="$route.name == 'record'"></record>
                <discount v-else-if="$route.name == 'discount'"></discount>
            </div>
        </div>
    </section>
</template>
<script>
    import store from '../../store'
    import Loading from '../layouts/Loading.vue'
    import Region from './Region.vue'
    import Profile from './Profile.vue'
    import Sale from './Sale.vue'
    import Record from './Record.vue'
    import Discount from './Discount.vue'

    export default {
        components: {
            Loading,
            Region,
            Profile,
            Sale,
            Record,
            Discount
        },
        data: function () {
            return {
                loading: true,
                regions: []
            }
        },
        computed: {
            user: function () {
                return store.state.user
            },
            region_id: function () {
                return this.$route.params.id
            }
        },
        methods: {
            get_regions: function () {
                let self = this
                this.loading = true
                axios.get('/region').then(function (response) {
                    self.loading = false
                    self.regions = response.data
                })
            }
        },
        mounted: function () {
            store.dispatch("get_user")
            this.get_regions()
        }
    }
</script>