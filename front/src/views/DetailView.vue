<template>
  <div class="header">
    <h1>Détails de l'évènement</h1>
    <router-link class="btn" :to="{ name: 'List' }">Retour</router-link>
  </div>
  <div class="card">
    <div class="card__body">
      <template v-if="event">
        <form @submit.prevent="save">
          <div class="form-group">
            <label class="form-label" for="title">Titre</label>
            <input id="title" v-model="form.title" class="form-control" type="text" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" v-model="form.description" class="form-control textarea" required></textarea>
          </div>

          <div class="form-group">
            <label class="form-label" for="color">Couleur</label>
            <div class="color-select">
              <span class="color-dot" :style="{ backgroundColor: getDotColor(form.color) }"></span>
              <select id="color" v-model="form.color" class="form-control" required>
                <option v-for="color in colorOptions" :key="color" :value="color">{{ color }}</option>
              </select>
            </div>
          </div>

          <div class="detail__row">
            <span class="detail__label">Date de début</span>
            <span class="detail__value">{{ formatDate(event.startDate) }}</span>
          </div>
          <div class="detail__row">
            <span class="detail__label">Date de fin</span>
            <span class="detail__value">{{ formatDate(event.endDate) }}</span>
          </div>
          <div v-if="event.creator" class="detail__row">
            <span class="detail__label">Créateur</span>
            <span class="detail__value">{{ event.creator.firstName }} {{ event.creator.lastName }}</span>
          </div>

          <p v-if="error" class="form-message form-message--error">{{ error }}</p>
          <p v-if="saved" class="form-message form-message--success">Modifications enregistrées.</p>

          <button type="submit" class="btn" :disabled="saving">{{ saving ? "Enregistrement..." : "Enregistrer" }}</button>
        </form>
      </template>
      <div v-else>Chargement...</div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapState} from "pinia";
import {useMainStore} from "@/store";

const COLOR_OPTIONS = ["rouge", "vert", "bleu"];

export default {
  name: "DetailView",
  props: {
    id: {
      type    : [String, Number],
      required: true,
    },
  },
  data()
  {
    return {
      form: {
        title      : "",
        description: "",
        color      : "",
      },
      colorOptions: COLOR_OPTIONS,
      saving      : false,
      saved       : false,
      error       : "",
    };
  },
  mounted()
  {
    this.init();
  },
  computed: {
    ...mapState(useMainStore, {
      event: (state) => state.current,
    }),
  },
  methods : {
    init()
    {
      this.fetchEvent(this.id).then(() =>
      {
        this.syncForm();
      });
    },

    syncForm()
    {
      if (!this.event) return;

      this.form.title       = this.event.title;
      this.form.description = this.event.description;
      this.form.color       = this.event.color;
    },

    save()
    {
      this.error = "";
      this.saved = false;

      // client-side guard: only ever send one of the 3 allowed colors
      if (!this.colorOptions.includes(this.form.color))
      {
        this.error = "La couleur sélectionnée n'est pas valide.";

        return;
      }

      this.saving = true;

      this.updateEvent(this.id, {...this.form})
        .then(() =>
        {
          this.saved = true;

          return this.fetchList();
        })
        .catch(() =>
        {
          this.error = "Une erreur est survenue lors de l'enregistrement.";
        })
        .finally(() =>
        {
          this.saving = false;
        });
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

    formatDate(value)
    {
      if (!value) return "";

      return new Date(value).toLocaleDateString("fr-FR");
    },

    ...mapActions(useMainStore, {
      fetchEvent  : "fetchEvent",
      updateEvent : "updateEvent",
      fetchList   : "fetchList",
    }),
  },
};
</script>

<style lang="scss" scoped>
.color-dot
{
	border-radius : 50%;
	display       : inline-block;
	flex-shrink   : 0;
	height        : 10px;
	width         : 10px;
}

.color-select
{
	align-items : center;
	display     : flex;
	gap         : 8px;
}

.detail__row
{
	display         : flex;
	justify-content : space-between;
	padding         : 8px 0;
}

.detail__row + .detail__row
{
	border-top : 1px dashed #26474E;
}

.detail__label
{
	font-weight : 700;
}

.textarea
{
	height     : auto;
	min-height : 100px;
	padding    : 8px;
	resize     : vertical;
}

.form-message
{
	margin : 10px 0;
}

.form-message--error
{
	color : #c0392b;
}

.form-message--success
{
	color : #27ae60;
}
</style>
