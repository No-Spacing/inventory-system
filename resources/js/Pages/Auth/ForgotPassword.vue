<script setup>
import Layout from "../../Layout/Main.vue";
import { useForm } from "@inertiajs/vue3";
const form = useForm({
    password: "",
});
</script>
<template>
    <Layout title="Forgot Password">
        <v-container max-width="700" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form @submit.prevent="form.post('/submit-forgot-password')">
                    <v-text-field
                        type="password"
                        variant="outlined"
                        label="Password"
                    ></v-text-field>

                    <v-sheet class="d-flex align-end flex-column">
                        <v-btn type="submit" color="primary">Change pass</v-btn>
                    </v-sheet>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
