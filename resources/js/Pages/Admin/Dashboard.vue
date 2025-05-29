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
        key: "name",
        sortable: true,
        title: "Name",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "email",
        title: "Email",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "created_at",
        title: "Date Created",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "role",
        title: "Roles",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];
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
            <v-card title="Users" elevation="2" class="pa-8 my-5">
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
                    :items="props.users"
                    :search="search"
                ></v-data-table>
            </v-card>
        </v-container>
    </Layout>
</template>
