<script setup>
import Layout from "../../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
</script>
<template>
    <Layout title="Stockcard">
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
                                    class="text-subtitle-1 text-medium-emphasis"
                                >
                                    Product
                                </div>
                                <v-autocomplete
                                    v-model:search="code"
                                    :items="products"
                                    item-title="product"
                                    placeholder="Select..."
                                    variant="outlined"
                                    menu-icon=""
                                    @update:modelValue="expand = true"
                                    @input="codeInput"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                        <v-row no-gutters>
                            <v-col cols="12">
                                <div
                                    class="text-subtitle-1 text-medium-emphasis"
                                >
                                    Lot/Batch No.
                                </div>
                                <v-autocomplete
                                    v-model:search="lot"
                                    :items="[
                                        '1-BR-BD7800',
                                        '9-IC-BABPA1',
                                        '0-PM-104116',
                                        '4-EP-ECGMS3',
                                        'P-MC-000006',
                                        '2-PP-KCPK24',
                                    ]"
                                    placeholder="Select..."
                                    variant="outlined"
                                    menu-icon=""
                                    @update:modelValue="expand2 = true"
                                    @input="lotInput"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>

                        <v-row no-gutters>
                            <v-col cols="12">
                                <div
                                    class="text-subtitle-1 text-medium-emphasis"
                                >
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
                                <div
                                    class="text-subtitle-1 text-medium-emphasis"
                                >
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
                                    :class="
                                        $vuetify.display.mobile
                                            ? 'mx-0'
                                            : 'mx-2'
                                    "
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
