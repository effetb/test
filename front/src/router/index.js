import {createRouter, createWebHistory} from 'vue-router'
import ListView from '@/views/ListView.vue'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes : [
		{
			path     : "/",
			name     : "List",
			component: ListView,
		},
	],
})

export default router
