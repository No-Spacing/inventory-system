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
    <Layout title="Stockcard">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Product
                            </div>
                            <v-autocomplete
                                item-title="product"
                                placeholder="Select..."
                                variant="outlined"
                                menu-icon=""
                            ></v-autocomplete>
                        </v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Lot/Batch No.
                            </div>
                            <v-autocomplete
                                :items="['001', '002', '003', '004', '005']"
                                placeholder="Select..."
                                variant="outlined"
                                menu-icon=""
                            ></v-autocomplete>
                        </v-col>
                    </v-row>

                    <v-row no-gutters>
                        <v-col cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Description
                            </div>
                            <v-text-field
                                placeholder="Description"
                                variant="outlined"
                                required
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row no-gutters>
                        <v-col cols="12" sm="4">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Filter By
                            </div>
                            <v-select
                                placeholder="Select..."
                                :items="[
                                    'Date of Posting',
                                    'Date of Transaction',
                                ]"
                                variant="outlined"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date From
                            </div>
                            <v-date-input
                                placeholder="Date from"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                :class="$vuetify.display.mobile ? 'mx-0' : 'mx-2'"
                            ></v-date-input>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date to
                            </div>
                            <v-date-input
                                placeholder="Date to"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                            ></v-date-input>
                        </v-col>
                    </v-row>

                    <v-row justify="center">
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn class="me-4" color="green" @click="load" :loading="loading" size="large">
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
