import { defineStore } from "pinia";
import { dataManager } from "@/services/data-manager.js";

export const useMainStore = defineStore("main", {
    state: () => ({
        list: [],
    }),

    actions: {
        setList(data) {
            this.list = data;
        },

        fetchList() {
            return dataManager.list()
                .then((res) => {
                    console.log("EVENTS API:", res);
                    this.setList(res);
                })
                .catch((err) => {
                    console.error("ERROR FETCH LIST:", err);
                });
        }
    }
});
