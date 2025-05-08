<script setup>
import Layout from "../../../Layout/Main.vue";
</script>
<template>
    <Layout title="Stocks (Batch/Lots)">
        <div
            class="d-flex flex-column fill-height bg-brown-lighten-5"
            style="min-height: 94vh"
        >
            <v-container max-width="1500" fluid>
                <v-card elevation="2" class="pa-8 my-5">
                    <v-form>
                        <v-autocomplete
                            label="Code"
                            variant="outlined"
                        ></v-autocomplete>

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
