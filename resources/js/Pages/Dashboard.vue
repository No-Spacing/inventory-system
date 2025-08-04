<script setup>
import { ref, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3"
import Layout from "../Layout/Main.vue";

defineProps({ 
    products: Object,
    lot_count: Number,
    product_count: Number,
    serial_count: Number,
    stockcard_count: Number,
});

const page = usePage();

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
        key: "created_at",
        title: "Created At",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];

watch(() => search.value, (value) => {
    router.get('/dashboard', {
        search: value,
        page: page.props.products.current_page
    }, {
        preserveScroll: true,
        preserveState: true,
    })
})

function paginations(){
    router.get('/dashboard', {
        search: search.value,
        page: page.props.products.current_page
    }, 
    {
        preserveScroll: true,
        preserveState: true,
    })
}
</script>

<template>
    <Layout title="Dashboard">
        <v-container max-width="1600" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-row>
                    <!-- Small statistic cards -->
                    <v-col cols="12" md="3">
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
                                <span class="text-h4 mt-2">{{ product_count }}</span>
                            </template>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="3">
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
                                <span class="text-h4 mt-2">{{ lot_count }}</span>
                            </template>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card class="rounded-lg" color="primary" elevation="2">
                            <template v-slot:prepend>
                                <v-icon
                                    icon="mdi-invoice-list-outline"
                                    size="70px"
                                    class="mr-5"
                                ></v-icon>
                            </template>
                            <template v-slot:title>
                                <span class="">Serial</span>
                                <br />
                                <span class="text-h4 mt-2">{{ serial_count }}</span>
                            </template>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card
                            class="rounded-lg"
                            color="red-darken-1"
                            elevation="2"
                        >
                            <template v-slot:prepend>
                                <v-icon
                                    icon="mdi-clipboard-list-outline"
                                    size="70px"
                                    class="mr-5"
                                ></v-icon>
                            </template>
                            <template v-slot:title>
                                <span class="">Stockcard</span>
                                <br />
                                <span class="text-h4 mt-2">{{ stockcard_count }}</span>
                            </template>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card>

            <!-- Table content  -->
            <!-- <v-card elevation="2" class="pa-8 my-5">
                <v-card-title class="mb-2">
                    Statistic
                    <v-divider></v-divider>
                </v-card-title>
                
            </v-card> -->
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
                :items="products.data"
                :search="search"
            >
                <template v-slot:bottom>
                    <v-row
                        class="d-flex align-center justify-space-between mt-5"
                    >
                        <v-col cols="4">
                            <v-btn @click="addLotDialog = true" color="primary" class="mx-2">
                                Add Lot/Batch
                            </v-btn>
                            <v-btn @click="importDialog = true" color="green" class="mx-2">
                                Import Lot
                            </v-btn>
                        </v-col>
                        <v-col cols="6">
                            <v-pagination 
                                v-model="products.current_page" 
                                :length="products.last_page" 
                                @update:modelValue="paginations"
                            ></v-pagination> 
                        </v-col>
                    </v-row>
                </template>
            </v-data-table>
            </v-card>
        </v-container>
    </Layout>
</template>
