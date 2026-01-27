import {defineStore} from "pinia";
import {dataManager} from "@/services/data-manager.js";

export const useMainStore = defineStore('mainStore', {
	id   : "Main",
	state: () => ({
		list   : [],
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
		}
	},
});
