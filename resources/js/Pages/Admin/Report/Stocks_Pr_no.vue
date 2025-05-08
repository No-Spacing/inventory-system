<script setup>
import Layout from "../../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
</script>
<template>
    <Layout title="Stocks with PR No.">
        <div
            class="d-flex flex-column fill-height bg-brown-lighten-5"
            style="min-height: 94vh"
        >
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
                                <v-btn class="me-4" color="green" size="large">
                                    Generate PDF
                                </v-btn>
                            </v-col>
                            <v-col cols="12" sm="auto" class="text-center">
                                <v-btn
                                    class="me-4"
                                    color="primary"
                                    size="large"
                                >
                                    Export to Excel
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-card>
            </v-container>
        </div>
    </Layout>
</template>
