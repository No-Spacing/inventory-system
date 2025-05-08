<script setup>
import Layout from "../../../Layout/Main.vue"
import { VDateInput } from 'vuetify/labs/VDateInput'
</script>
<template>
    <Layout title="Product Expiry">
        <v-container max-width="1500" fluid>
            <v-form>
                <v-row no-gutters>
                    <v-col cols="12">
                        <div class="text-subtitle-1 text-medium-emphasis mx-2">
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
                        <div class="text-subtitle-1 text-medium-emphasis mx-2">
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

                <div class="d-flex justify-center">
                    <v-btn class="me-4" color="green" size="large" >Generate PDF</v-btn>
                    <v-btn class="me-4" color="primary" size="large" >Export to Excel</v-btn>
                </div>
            </v-form>
        </v-container>
    </Layout>
</template>