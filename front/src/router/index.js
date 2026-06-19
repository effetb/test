import {createRouter, createWebHistory} from 'vue-router'
import ListView from '@/views/ListView.vue'
import EventDetailView from '@/views/EventDetailView.vue'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes : [
		{
			path     : "/",
			name     : "List",
			component: ListView,
		},
		{
			path     : "/events/:id",
			name     : "EventDetail",
			component: EventDetailView,
			props    : true,
		},
	],
})

export default router
