<script setup>
import Layout from "../Layout/Main.vue";

import { ref } from "vue";

const drawer = ref(true);
const rail = ref(false);

const search = ref("");
const headers = [
    {
        align: "start",
        key: "name",
        sortable: true,
        title: "Dessert (100g serving)",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "calories",
        title: "Calories",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "fat",
        title: "Fat (g)",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "carbs",
        title: "Carbs (g)",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "protein",
        title: "Protein (g)",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "iron",
        title: "Iron (%)",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];
const desserts = [
    {
        name: "Frozen Yogurt",
        calories: 159,
        fat: 6,
        carbs: 24,
        protein: 4,
        iron: 1,
    },
    {
        name: "Ice cream sandwich",
        calories: 237,
        fat: 9,
        carbs: 37,
        protein: 4.3,
        iron: 1,
    },
    {
        name: "Eclair",
        calories: 262,
        fat: 16,
        carbs: 23,
        protein: 6,
        iron: 7,
    },
    {
        name: "Cupcake",
        calories: 305,
        fat: 3.7,
        carbs: 67,
        protein: 4.3,
        iron: 8,
    },
    {
        name: "Gingerbread",
        calories: 356,
        fat: 16,
        carbs: 49,
        protein: 3.9,
        iron: 16,
    },
    {
        name: "Jelly bean",
        calories: 375,
        fat: 0,
        carbs: 94,
        protein: 0,
        iron: 0,
    },
    {
        name: "Lollipop",
        calories: 392,
        fat: 0.2,
        carbs: 98,
        protein: 0,
        iron: 2,
    },
    {
        name: "Honeycomb",
        calories: 408,
        fat: 3.2,
        carbs: 87,
        protein: 6.5,
        iron: 45,
    },
    {
        name: "Donut",
        calories: 452,
        fat: 25,
        carbs: 51,
        protein: 4.9,
        iron: 22,
    },
    {
        name: "KitKat",
        calories: 518,
        fat: 26,
        carbs: 65,
        protein: 7,
        iron: 6,
    },
];
</script>

<template>
    <layout>
        <!-- Main -->
        <v-main class="pa-6">
            <v-container fluid>
                <v-row dense>
                    <!-- Small statistic cards -->
                    <v-col cols="12" md="4">
                        <v-card
                            class="rounded-lg"
                            color="primary"
                            elevation="2"
                        >
                            <v-card-text>
                                <div class="text-h6 font-weight-medium">
                                    Users
                                </div>
                                <div class="text-h4 mt-2">12</div>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card
                            class="rounded-lg"
                            color="blue-lighten-1"
                            elevation="2"
                        >
                            <v-card-text>
                                <div class="text-h6 font-weight-medium">
                                    Products
                                </div>
                                <div class="text-h4 mt-2">23,456</div>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card
                            class="rounded-lg"
                            color="green"
                            theme="dark"
                            elevation="2"
                        >
                            <v-card-text>
                                <div class="text-h6 font-weight-medium">
                                    Lot Numbers
                                </div>
                                <div class="text-h4 mt-2">834</div>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <!-- Table content  -->
                    <v-col cols="12" class="mt-8">
                        <v-card title="Users" flat>
                            <template v-slot:text>
                                <v-text-field
                                    v-model="search"
                                    label="Search"
                                    prepend-inner-icon="mdi-magnify"
                                    variant="outlined"
                                    hide-details
                                    single-line
                                ></v-text-field>
                            </template>

                            <v-data-table
                                :headers="headers"
                                :items="desserts"
                                :search="search"
                            ></v-data-table>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </layout>
</template>
