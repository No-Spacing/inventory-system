<script setup>
import Layout from "../../../Layout/Main.vue";
</script>

<template>
    <Layout title="Summary">
        <v-container max-width="1000" class="py-10">
            <v-card elevation="2" class="pa-8">
                <v-card-title class="text-h5 font-weight-bold mb-4">
                    Reports & Export Options
                </v-card-title>

                <v-divider class="mb-6" />

                <v-row align="center" justify="space-between">
                    <v-col cols="12" md="6">
                        <v-card class="pa-4" color="grey-lighten-4">
                            <v-card-title
                                class="text-subtitle-1 font-weight-bold"
                                >Generate Overall Report</v-card-title
                            >
                            <v-card-text>
                                Export a full summary of all transactions,
                                items, and product data into a consolidated
                                Excel report.
                            </v-card-text>
                            <v-card-actions>
                                <v-btn
                                    :disabled="loading"
                                    :loading="loading"
                                    class="text-none mb-4"
                                    color="orange-darken-1"
                                    size="x-large"
                                    variant="flat"
                                    block
                                    @click="loading = !loading"
                                >
                                    Export to Excel
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-card
                            class="pa-4"
                            color="grey-lighten-4"
                        >
                            <v-card-title
                                class="text-subtitle-1 font-weight-bold"
                                >Generate Products with Lot
                                Numbers</v-card-title
                            >
                            <v-card-text>
                                Export a filtered Excel report showing only
                                products that include lot or batch numbers.
                            </v-card-text>
                            <v-card-actions>
                              <v-btn
                                    :disabled="loading"
                                    :loading="loading"
                                    class="text-none mb-4"
                                    color="orange-darken-3"
                                    size="x-large"
                                    variant="flat"
                                    block
                                    @click="loading = !loading"
                                >
                                    Export to Excel
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card>
        </v-container>
    </Layout>
</template>
