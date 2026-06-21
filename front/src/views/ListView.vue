<template>


    <div class="header">
    <h1>Liste des évènements</h1>
  </div>
  <div class="card">
    <div class="card__body">
      <template v-if="Object.keys(list).length">
        <template v-for="item in list" :key="item.id">
          <div class="card__item">
              <div class="card__item__label">
                  #{{ item.id }} - {{ item.title }}

                  <span
                      v-if="item.color"
                      class="color-dot"
                      :style="{ backgroundColor: item.color }"
                  ></span>
              </div>
            <div class="card__item__actions">
                <router-link :to="`/event/${item.id}`">
                    View
                </router-link>
            </div>
          </div>
        </template>
      </template>
      <div v-else>Aucun élément dans la liste, veuillez en ajouter un !</div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapState} from "pinia";
import {useMainStore} from "@/store";

export default {
  name: "ListView",
  mounted()
  {
    this.init();
  },
  computed: {
    ...mapState(useMainStore, {
      list: (state) => state.list,
    }),
  },
  methods : {
    init()
    {
      this.fetchList();
    },

    ...mapActions(useMainStore, {
      fetchList: "fetchList",
    }),
  },
};
</script>

<style lang="scss" scoped>
.color-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-left: 8px;
    border: 1px solid #ccc;
}


</style>
