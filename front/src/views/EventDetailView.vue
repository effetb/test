<template>
    <div v-if="event" class="form">
        <h1>Edit Event #{{ event.id }}</h1>

        <div>
            <label>Title</label>
            <input v-model="event.title" />
        </div>

        <div>
            <label>Description</label>
            <textarea v-model="event.description"></textarea>
        </div>

        <div>
            <label>Color</label>
            <select v-model="event.color">
                <option value="rouge">rouge</option>
                <option value="vert">vert</option>
                <option value="bleu">bleu</option>
            </select>
        </div>

        <button @click="save">
            Save
        </button>

        <p v-if="message">{{ message }}</p>
    </div>

    <div v-else>
        Loading...
    </div>
</template>

<script>
import api from "@/services/api-connection.js";

export default {
    name: "EventEditView",

    data() {
        return {
            event: null,
            message: ""
        };
    },

    async mounted() {
        try {
            const id = this.$route.params.id;
            const res = await api.get(`api/events/${id}`);
            this.event = res;
        } catch (e) {
            this.$router.push("/");
        }
    },

    methods: {
        async save() {
            const allowed = ["rouge", "vert", "bleu"];

            if (!allowed.includes(this.event.color)) {
                this.message = "Invalid color";
                return;
            }

            try {
                await api.post(`api/events/${this.event.id}`, {
                    title: this.event.title,
                    description: this.event.description,
                    color: this.event.color
                });

                this.message = "Saved successfully";

                setTimeout(() => {
                    this.$router.push("/");
                }, 800);

            } catch (e) {
                this.message = "Error saving event";
            }
        }
    }
};
</script>

<style scoped>
.form {
    max-width: 500px;
}

input, textarea, select {
    width: 100%;
    margin-bottom: 10px;
    padding: 6px;
}
</style>
