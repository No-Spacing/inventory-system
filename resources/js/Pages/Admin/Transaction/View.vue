<script setup>
import Layout from '../../../Layout/Main.vue'
</script>

<template>
    <Layout title="View">
        <v-card class="ma-2 pa-2">
            <v-card-text>
                Message
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions class="d-flex justify-end">
                <v-btn class="bg-primary">Submit</v-btn>
            </v-card-actions>
        </v-card>
    </Layout>
</template>