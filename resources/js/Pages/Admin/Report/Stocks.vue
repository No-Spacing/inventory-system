<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref } from "vue";

const loading = ref(false);
function load() {
    loading.value = true;
    setTimeout(() => (loading.value = false), 2000);
}
</script>
<template>
    <Layout title="Stocks (Batch/Lots)">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form>
                    <v-autocomplete
                        label="Code"
                        variant="outlined"
                    ></v-autocomplete>

                    <v-row justify="center">
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn class="me-4" @click="load" :loading="loading" color="green" size="large">
                                Generate PDF
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn class="me-4" @click="load" :loading="loading" color="primary" size="large">
                                Export to Excel
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
