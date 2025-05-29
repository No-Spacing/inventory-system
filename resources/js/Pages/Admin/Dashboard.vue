<script setup>
import { ref } from "vue";
import Layout from "../../Layout/Main.vue";

const props = defineProps({ 
    users: Object, 
    products: Object ,
    lots: Object,
});

const search = ref("");
const headers = [
    {
        align: "start",
        key: "code",
        sortable: true,
        title: "Code",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "description",
        title: "Description",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "remaining_items",
        title: "Remaining Items",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "created_at",
        title: "Created At",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];

const icons = ['mdi-rewind', 'mdi-play', 'mdi-fast-forward']
const items = [
    {
        img: 'images/inmed-website.png',
    },
    {

        img: 'images/inmed-retail.png',
    },
    {
        img: 'images/inmed-wholesale.png',
    },
]
const transparent = 'rgba(255, 255, 255, 0)'
</script>

<template>
    <Layout title="Dashboard">
        <v-container max-width="1600" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-row>
                    <!-- Small statistic cards -->
                    <v-col cols="12" md="4">
                        <v-card
                            class="rounded-lg"
                            color="primary"
                            elevation="2"
                        >
                            <template v-slot:prepend>
                                <v-icon
                                    icon="mdi-account"
                                    size="70px"
                                    class="mr-5"
                                ></v-icon>
                            </template>
                            <template v-slot:title>
                                <span class="">Users</span>
                                <br />
                                <span class="text-h4 mt-2">{{ props.users.length }}</span>
                            </template>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card
                            class="rounded-lg"
                            color="blue-lighten-1"
                            elevation="2"
                        >
                            <template v-slot:prepend>
                                <v-icon
                                    icon="mdi-shopping-outline"
                                    size="70px"
                                    class="mr-5"
                                ></v-icon>
                            </template>
                            <template v-slot:title>
                                <span class="">Products</span>
                                <br />
                                <span class="text-h4 mt-2">{{ props.products.length }}</span>
                            </template>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card class="rounded-lg" color="green" elevation="2">
                            <template v-slot:prepend>
                                <v-icon
                                    icon="mdi-package-variant-closed"
                                    size="70px"
                                    class="mr-5"
                                ></v-icon>
                            </template>
                            <template v-slot:title>
                                <span class="">Lot No.</span>
                                <br />
                                <span class="text-h4 mt-2">{{ props.lots.length }}</span>
                            </template>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card>

            <!-- Table content  -->
            <v-card elevation="2" class="pa-8 my-5">
                <v-card-title class="mb-2">
                    Websites
                    <v-divider></v-divider>
                </v-card-title>
                
                <v-row>
                    <template v-for="(item, i) in items" :key="i">
                        <v-col
                        cols="12"
                        md="4"
                        >
                            <v-hover v-slot="{ isHovering, props }">
                                <v-card
                                class="v-card-foreground"
                                :class="{ 'on-hover': isHovering }"
                                :elevation="isHovering ? 12 : 2"
                                v-bind="props"
                                >
                                    <v-img
                                        :src="item.img"
                                        height="auto"
                                        cover
                                    >
                                        <v-card-title class="text-h6 text-white d-flex flex-column">
                                        <p class="mt-4">
                                            {{ item.title }}
                                        </p>

                                        <div>
                                            <p class="ma-0 text-body-1 font-weight-bold">
                                            {{ item.text }}
                                            </p>
                                            <p class="text-caption font-weight-medium">
                                            {{ item.subtext }}
                                            </p>
                                        </div>
                                        </v-card-title>
                                        
                                    </v-img>
                                </v-card>
                            </v-hover>
                        </v-col>
                    </template>
                </v-row>
            </v-card>
            <v-card title="Products" elevation="2" class="pa-8 my-5">
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
                :items="props.products"
                :search="search"
            ></v-data-table>
            </v-card>
        </v-container>
    </Layout>
</template>
