<template>
    <section class="section">
        <div class="columns">
            <div class="column is-1">
                <aside class="menu">
                    <p class="menu-label">
                        用户
                    </p>
                    <ul class="menu-list">
                        <li>
                            <router-link :to="{name: 'admin_quick_create_user'}" :class="{
                                'is-active': $route.name == 'admin_quick_create_user'
                            }">快速创建用户</router-link>
                        </li>
                        <li>
                            <router-link :to="{name: 'admin_record'}" :class="{
                                'is-active': $route.name == 'admin_record'
                            }">订单</router-link>
                        </li>
                        <li>
                            <router-link :to="{name: 'admin_user'}" :class="{
                                'is-active': $route.name == 'admin_user'
                            }">列表</router-link>
                        </li>
                    </ul>
                    <p class="menu-label">
                        区域
                    </p>
                    <ul class="menu-list">
                        <li>
                            <router-link :to="{name: 'admin_region_create'}" :class="{
                                'is-active': $route.name == 'admin_region_create'
                            }">创建</router-link>
                        </li>
                        <li>
                            <router-link :to="{name: 'admin_region_list'}" :class="{
                                'is-active': $route.name == 'admin_region_list'
                            }">列表</router-link>
                        </li>
                    </ul>
                    <p class="menu-label">
                        服务器
                    </p>
                    <ul class="menu-list">
                        <li>
                            <router-link :to="{name: 'admin_server_create'}" :class="{
                                'is-active': $route.name == 'admin_server_create'
                            }">创建</router-link>
                        </li>
                        <li>
                            <router-link :to="{name: 'admin_server'}" :class="{
                                'is-active': $route.name == 'admin_server'
                            }">列表</router-link>
                        </li>
                    </ul>
                    <p class="menu-label">
                        套餐
                    </p>
                    <ul class="menu-list">
                        <li>
                            <router-link :to="{name: 'admin_commodity_create'}" :class="{
                                'is-active': $route.name == 'admin_commodity_create'
                            }">创建</router-link>
                        </li>
                        <li>
                            <router-link :to="{name: 'admin_commodity'}" :class="{
                                'is-active': $route.name == 'admin_commodity'
                            }">列表</router-link>
                        </li>
                    </ul>
                    <p class="menu-label">
                        激活码
                    </p>
                    <ul class="menu-list">
                        <li>
                            <router-link :to="{name: 'admin_discount_create'}" :class="{
                                'is-active': $route.name == 'admin_discount_create'
                            }">创建</router-link>
                        </li>
                        <li>
                            <router-link :to="{name: 'admin_discount'}" :class="{
                                'is-active': $route.name == 'admin_discount'
                            }">列表</router-link>
                        </li>
                    </ul>
                </aside>
            </div>
            <div class="column">
                <user-list v-if="$route.name == 'admin_user'"></user-list>
                <user-quick v-else-if="$route.name == 'admin_quick_create_user'"></user-quick>
                <user-record v-else-if="$route.name == 'admin_record'"></user-record>
                <user-detail v-else-if="$route.name == 'admin_user_detail'"></user-detail>
                <user-region-edit v-else-if="$route.name == 'admin_user_edit'"></user-region-edit>

                <region-detail v-else-if="$route.name == 'admin_region'"></region-detail>
                <region-create v-else-if="$route.name == 'admin_region_create'"></region-create>
                <region-list v-else-if="$route.name == 'admin_region_list'"></region-list>

                <server-list v-else-if="$route.name == 'admin_server'"></server-list>
                <server-create v-else-if="$route.name == 'admin_server_create'"></server-create>
                <server-edit v-else-if="$route.name == 'admin_server_edit'"></server-edit>

                <commodity-list v-else-if="$route.name == 'admin_commodity'"></commodity-list>
                <commodity-create v-else-if="$route.name == 'admin_commodity_create'"></commodity-create>
                <commodity-edit v-else-if="$route.name == 'admin_commodity_edit'"></commodity-edit>

                <discount-list v-else-if="$route.name == 'admin_discount'"></discount-list>
                <discount-create v-else-if="$route.name == 'admin_discount_create'"></discount-create>
            </div>
        </div>
    </section>
</template>
<script>
    import store from '../../store'
    import RegionList from './region/List.vue'
    import RegionCreate from './region/Create.vue'
    import RegionDetail from './region/Detail.vue'
    import UserQuick from './user/Quick.vue'
    import UserList from './user/List.vue'
    import UserRecord from './user/Record.vue'
    import UserDetail from './user/Detail.vue'
    import UserRegionEdit from './user/RegionEdit.vue'
    import ServerList from './server/List.vue'
    import ServerCreate from './server/Create.vue'
    import ServerEdit from './server/Edit.vue'
    import CommodityList from './commodity/List.vue'
    import CommodityCreate from './commodity/Create.vue'
    import CommodityEdit from './commodity/Edit.vue'
    import DiscountList from './discount/List.vue'
    import DiscountCreate from './discount/Create.vue'

    export default {
        components: {
            RegionDetail,
            RegionCreate,
            RegionList,
            UserList,
            UserQuick,
            UserRecord,
            UserDetail,
            UserRegionEdit,
            ServerList,
            ServerCreate,
            ServerEdit,
            CommodityList,
            CommodityCreate,
            CommodityEdit,
            DiscountList,
            DiscountCreate
        },
        mounted: function () {
            store.commit('set_full', true)
            store.dispatch("get_user_admin")
        },
        destroyed: function () {
            store.commit('set_full', false)
        }
    }
</script>