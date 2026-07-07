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
              <span class="color-dot" :style="{ backgroundColor: getDotColor(item.color) }"></span>
              #{{ item.id }} - {{ item.title }}
            </div>
            <div class="card__item__actions">
              <router-link class="btn" :to="{ name: 'Detail', params: { id: item.id } }">Détails</router-link>
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

    getDotColor(color)
    {
      const colors = {
        rouge: "red",
        vert : "green",
        bleu : "blue",
      };

      return colors[color] || "transparent";
    },

    ...mapActions(useMainStore, {
      fetchList: "fetchList",
    }),
  },
};
</script>

<style lang="scss" scoped>
.color-dot
{
	border-radius : 50%;
	display       : inline-block;
	height        : 10px;
	margin-right  : 8px;
	width         : 10px;
}
</style>
