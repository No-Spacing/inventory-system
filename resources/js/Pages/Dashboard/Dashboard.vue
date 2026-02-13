<script setup>
import { ref, watch, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import CardComponent from "./component/CardComponent.vue";
import Layout from "../../Layout/Main.vue";

const props = defineProps({
    products: Object,
    lot_count: Number,
    product_count: Number,
    serial_count: Number,
    stockcard_count: Number,
    monthlyOutbound: Object,
    highestPerMonth: Object,
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

const product = [
    ...new Set(page.props.monthlyOutbound.map((item) => item.product.name)),
];
const months = [
    ...new Set(page.props.monthlyOutbound.map((item) => item.month)),
];

watch(
    () => search.value,
    (value) => {
        router.get(
            "/dashboard",
            {
                search: value,
                page: page.props.products.current_page,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    },
);
const series1 = ref([
    {
        name: "Sales",
        data: [10, 20, 15, 30, 25, 40],
    },
]);

const chartOptions1 = ref({
    chart: {
        zoom: {
            enabled: false,
        },
        type: "line",
        fontFamily: "Rethinksans, sans-serif",
        toolbar: { show: false },
        redrawOnParentResize: true,
        redrawOnWindowResize: true,
    },
    stroke: { curve: "smooth" },
    xaxis: { categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"] },
});

/* const series2 = ref([
    {
        name: "Revenue",
        data: [100, 150, 120, 170, 140, 200],
    },
]);

const chartOptions2 = ref({
    chart: {
        zoom: {
            enabled: false,
        },
        type: "line",
        fontFamily: "Rethinksans, sans-serif",
        toolbar: { show: false },
        redrawOnParentResize: true,
        redrawOnWindowResize: true,
    },
    stroke: { curve: "smooth" },
    xaxis: { categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"] },
}); */
</script>
<template>
    <Layout title="Dashboard">
        <v-container class="pa-16" fluid>
            <!-- stat-card -->
            <CardComponent
                :productCount="props.product_count"
                :lotCount="props.lot_count"
                :serialCount="props.serial_count"
                :stockCard="props.stockcard_count"
            />
            <!-- ROW 2 Chart Card -->
            <v-row align="stretch">
                <v-col cols="12" md="6">
                    <v-card
                        class="pa-8 my-5 rounded-lg d-flex flex-column h-100"
                    >
                        <v-card-title>Sample Chart</v-card-title>
                        <div class="flex-grow-1">
                            <apexchart
                                width="100%"
                                height="100%"
                                type="line"
                                :options="chartOptions1"
                                :series="series1"
                            />
                        </div>
                    </v-card>
                </v-col>

                <!-- Table Card -->
                <v-col cols="12" md="6">
                    <v-card
                        class="pa-8 my-5 rounded-lg d-flex flex-column h-100"
                    >
                        <v-card-title>New Products</v-card-title>

                        <v-data-table
                            class="grow"
                            :headers="headers"
                            :items="products"
                            :search="search"
                            :hide-default-footer="true"
                        />
                    </v-card>
                </v-col>
                <!-- <v-col cols="12" md="4">
                    <v-card
                        class="pa-8 my-5 rounded-lg d-flex flex-column h-100"
                    >
                        <v-card-title>Sample Chart</v-card-title>
                        <div class="flex-grow-1">
                            <apexchart
                                width="100%"
                                height="100%"
                                type="line"
                                :options="chartOptions2"
                                :series="series2"
                            />
                        </div>
                    </v-card>
                </v-col> -->
            </v-row>
        </v-container>
    </Layout>
</template>
