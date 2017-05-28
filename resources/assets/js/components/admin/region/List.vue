<template>
    <div>
        <div class="notification" v-if="message">
            <button class="delete" @click="message = ''"></button>
            {{ message }}
        </div>
        <loading v-if="loading"></loading>
        <table v-else class="table is-bordered is-striped" style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>介绍</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="region in regions">
                    <th>{{ region.id }}</th>
                    <th>{{ region.name }}</th>
                    <th>{{ region.introduction }}</th>
                    <th>{{ region.created_at }}</th>
                    <th>{{ region.updated_at }}</th>
                    <th>
                        <router-link :to="{name: 'admin_region', params: {
                            id: region.id
                        }}" class="button is-primary">编辑</router-link>

                        <a :class="{'button is-danger': true, 'is-loading': delete_loading[region.id]}" @click="delete_region(region.id)">删除</a>
                    </th>
                </tr>
            </tbody>
        </table>
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
                regions: [],
                loading: true,
                delete_loading: {},
                message: ""
            }
        },
        watch: {
            page: function () {
                this.search()
            }
        },
        methods: {
            search: function () {
                let self = this
                this.loading = true
                axios.get('/region').then(function (response) {
                    self.loading = false
                    self.regions = response.data
                })
            },
            delete_region: function (id) {
                let self = this
                this.$set(this.delete_loading, id, true)
                axios.post("/admin/region/delete", {
                    id: id
                }).then(function (response) {
                    self.$set(self.delete_loading, id, false)
                    if (!response.data.success) {
                        self.message = response.data.message
                    } else {
                        self.search()
                    }
                })
            }
        },
        mounted: function () {
            this.search()
        }
    }
</script>