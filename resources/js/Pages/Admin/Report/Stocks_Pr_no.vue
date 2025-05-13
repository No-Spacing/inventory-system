<script setup>
import Layout from "../../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
import { ref } from "vue";

const loading = ref(false);
function load() {
    loading.value = true;
    setTimeout(() => (loading.value = false), 2000);
}
</script>
<template>
    <Layout title="Stocks with PR No.">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date From
                            </div>
                            <v-date-input
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                class="mx-2"
                            ></v-date-input>
                        </v-col>
                        <v-col cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date to
                            </div>
                            <v-date-input
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                class="mx-2"
                            ></v-date-input>
                        </v-col>
                    </v-row>

                    <v-row justify="center">
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn class="me-4" @click="load" :loading="loading" color="green" size="large">
                                Generate PDF
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn class="me-4" color="primary" :loading="loading" size="large">
                                Export to Excel
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
