import Vue from 'vue'
import Vuex from 'vuex'
import VueRouter from 'vue-router'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        user: null,
        full: false,
        message: ""
    },
    mutations: {
        set_user: (state, user) => {
            state.user = user
        },
        set_full: (state, full) => {
            state.full = full
        },
        set_message: (state, message) => {
            state.message = message
        }
    },
    actions: {
        get_user: function (content) {
            axios.get("/user/profile").then(function (response) {
                if (response.data.success) {
                    content.commit("set_user", response.data.user)
                } else {
                    content.commit("set_user", null)
                }
            })
        },
        get_user_admin: function (content) {
            axios.get("/user/profile").then(function (response) {
                if (response.data.success) {
                    let user = response.data.user
                    if (user && user.level < 1) {
                        $router.push({name: "index"})
                    }
                    content.commit("set_user", user)
                } else {
                    content.commit("set_user", null)
                }
            })
        },
        get_user_noredirect: function (content) {
            axios.get("/user/profile", {params: {redirect: false}}).then(function (response) {
                if (response.data.success) {
                    content.commit("set_user", response.data.user)
                } else {
                    content.commit("set_user", null)
                }
            })
        }
    }
})