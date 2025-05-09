<script setup>
import Layout from "../../Layout/Main.vue"
</script>
<template>
    <Layout title="Forgot Password">
        <v-card>
            <v-form>
                <v-text-field type="password" variant="outlined" label="Password"></v-text-field>
                <v-btn type="submit" color="primary">Change</v-btn>
            </v-form>
        </v-card>
    </Layout>
</template>