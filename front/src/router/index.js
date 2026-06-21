import {createRouter, createWebHistory} from 'vue-router'
import ListView from '@/views/ListView.vue'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes : [
        {
            path: "/",
            name: "List",
            component: ListView,
        },


        {
            path: "/event/:id",
            name: "event-detail",
            component: () => import("@/views/EventDetailView.vue"),
        },

        // 🔥 2.3 - EDIT
        {
            path: "/event/:id/edit",
            name: "event-edit",
            component: () => import("@/views/EventEditView.vue"),
        },
    ],
})

export default router
