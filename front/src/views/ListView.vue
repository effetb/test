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
              <span class="event-color" :class="`event-color--${item.color}`"></span>
              #{{ item.id }} - {{ item.title }}
            </div>
            <div class="card__item__actions">
              <router-link class="btn" :to="{ name: 'EventDetail', params: { id: item.id } }">Détails</router-link>
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

<style lang="scss" scoped></style>
