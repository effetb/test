import {defineStore} from "pinia";
import {dataManager} from "@/services/data-manager.js";

export const useMainStore = defineStore('mainStore', {
	id   : "Main",
	state: () => ({
		list   : [],
		current: null,
		loading: false
	}),

	actions: {
		fetchList()
		{
			return dataManager.list().then((res) =>
			{
				this.setList(res);
			});
		},

		setList(list)
		{
			this.list = list;
		},

		fetchEvent(id)
		{
			return dataManager.detail(id).then((res) =>
			{
				this.setCurrent(res);
			});
		},

		setCurrent(event)
		{
			this.current = event;
		},

		updateEvent(id, data)
		{
			return dataManager.update(id, data).then((res) =>
			{
				this.setCurrent(res);

				return res;
			});
		}
	},
});
