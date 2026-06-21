<template>


    <div v-if="form">
        <h1>Edit Event #{{ id }}</h1>

        <div>
            <label>Title</label>
            <input v-model="form.title" />
        </div>

        <div>
            <label>Description</label>
            <textarea v-model="form.description"></textarea>
        </div>

        <div>
            <label>Color</label>
            <select v-model="form.color">
                <option value="rouge">Rouge</option>
                <option value="vert">Vert</option>
                <option value="bleu">Bleu</option>
            </select>
        </div>

        <button @click="save">Save</button>

        <p v-if="error" style="color:red">{{ error }}</p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "EventEditView",

    data() {
        return {
            id: null,
            form: null,
            error: null,
        };
    },

    async mounted() {
        this.id = this.$route.params.id;

        try {
            const res = await axios.get(`/api/events/${this.id}`);
            this.form = res.data;
        } catch (e) {
            this.error = "Error loading event";
        }
    },

    methods: {
        validate() {
            if (!this.form.title || this.form.title.trim() === "") {
                this.error = "Title is required";
                return false;
            }

            if (!this.form.description || this.form.description.trim() === "") {
                this.error = "Description is required";
                return false;
            }

            const allowed = ["rouge", "vert", "bleu"];

            if (!allowed.includes(this.form.color)) {
                this.error = "Invalid color";
                return false;
            }

            return true;
        },

        async save() {
            this.error = null;

            if (!this.validate()) return;

            try {
                await axios.post(`/api/events/${this.id}`, this.form);

                this.$router.push("/");
            } catch (e) {
                this.error = "Error saving event";
            }
        },
    },
};
</script>
