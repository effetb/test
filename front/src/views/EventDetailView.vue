<template>
  <div class="header">
    <h1>Détails de l'évènement</h1>
    <router-link class="btn" :to="{ name: 'List' }">Retour</router-link>
  </div>
  <div class="card">
    <div class="card__body">
      <div v-if="loading">Chargement...</div>
      <div v-else-if="error" class="error">{{ error }}</div>
      <div v-else-if="event">
        <div class="card__item">
          <div class="card__item__label">
            <span class="event-color" :class="`event-color--${event.color}`"></span>
            #{{ event.id }} - {{ event.title }}
          </div>
        </div>
        <p>{{ event.description }}</p>
        <p><strong>Début :</strong> {{ event.startDate }}</p>
        <p><strong>Fin :</strong> {{ event.endDate }}</p>

        <hr />

        <h2>Modifier l'évènement</h2>
        <div v-if="formError" class="error">{{ formError }}</div>
        <div v-if="formSuccess" class="success">{{ formSuccess }}</div>
        <form @submit.prevent="save">
          <div class="form-group">
            <label class="form-label" for="title">Titre</label>
            <input id="title" v-model="form.title" class="form-control" type="text" />
          </div>
          <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" v-model="form.description" class="form-control" rows="4"></textarea>
          </div>
          <div class="form-group">
            <label class="form-label" for="color">Couleur</label>
            <select id="color" v-model="form.color" class="form-control">
              <option disabled value="">Sélectionnez une couleur</option>
              <option v-for="color in colors" :key="color" :value="color">{{ color }}</option>
            </select>
          </div>
          <button class="btn" type="submit" :disabled="saving">Enregistrer</button>
        </form>
      </div>
      <div v-else class="error">Impossible de charger l'évènement.</div>
    </div>
  </div>
</template>

<script>
import {dataManager} from "@/services/data-manager.js";
import {useMainStore} from "@/store";

export default {
  name: "EventDetailView",
  props: {
    id: {
      type: String,
      required: true,
    },
  },
  data()
  {
    return {
      event: null,
      loading: false,
      error: null,
      form: {
        title: "",
        description: "",
        color: "",
      },
      colors: ["rouge", "vert", "bleu"],
      saving: false,
      formError: null,
      formSuccess: null,
    };
  },
  mounted()
  {
    this.fetchEvent();
  },
  methods: {
    fetchEvent()
    {
      this.loading = true;
      this.error = null;
      dataManager.event(this.id)
        .then((res) =>
        {
          this.event = res;
          this.form.title = res.title || "";
          this.form.description = res.description || "";
          this.form.color = res.color || "";
        })
        .catch(() =>
        {
          this.error = "Impossible de charger l'évènement.";
        })
        .finally(() =>
        {
          this.loading = false;
        });
    },

    save()
    {
      this.formError = null;
      this.formSuccess = null;

      if (!this.form.title || !this.form.description)
      {
        this.formError = "Le titre et la description sont obligatoires.";
        return;
      }

      if (!this.colors.includes(this.form.color))
      {
        this.formError = "La couleur sélectionnée est invalide.";
        return;
      }

      this.saving = true;

      dataManager.updateEvent(this.id, {
        title: this.form.title,
        description: this.form.description,
        color: this.form.color,
      })
        .then((res) =>
        {
          this.event = res;
          this.formSuccess = "Évènement mis à jour.";
          const store = useMainStore();
          store.fetchList();
          this.$router.push({ name: "List" });
        })
        .catch((err) =>
        {
          if (err?.errors?.length)
          {
            this.formError = err.errors.map((e) => e.message).join(" ");
          }
          else
          {
            this.formError = "Impossible de mettre à jour l'évènement.";
          }
        })
        .finally(() =>
        {
          this.saving = false;
        });
    },
  },
};
</script>

<style scoped lang="scss">
.error
{
  color: #E74C3C;
}

.success
{
  color: #27AE60;
}

textarea.form-control
{
  height: auto;
  padding: 8px;
}
</style>
