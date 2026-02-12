<script setup>
import { ref, watch, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import Layout from "../Layout/Main.vue";

defineProps({
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

const series2 = ref([
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
});
</script>
<template>
    <Layout title="Dashboard">
        <v-container class="pa-16" fluid>
            <!-- stat-card -->
            <div
                style="
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    grid-auto-rows: 100px;
                    gap: 16px;
                "
            >
                <v-card
                    class="rounded-lg d-flex justify-space-between align-center pa-6"
                    style="grid-row: span 2"
                    elevation="2"
                >
                    <div>
                        <div class="text-grey" style="font-size: medium">
                            DAILY In & Out
                        </div>
                        <div style="font-size: xx-large">
                            {{ product_count }}
                        </div>
                    </div>

                    <div
                        class="d-flex align-center justify-center rounded-lg"
                        style="padding: 12px; background-color: #e3f2fd"
                    >
                        <v-icon
                            icon="mdi-package-variant"
                            size="28"
                            color="#1976D2"
                            style="opacity: 0.75"
                        />
                    </div>
                </v-card>

                <v-card
                    class="rounded-lg d-flex justify-space-between align-center pa-6"
                    style="grid-row: span 2"
                    elevation="2"
                >
                    <div>
                        <div class="text-grey" style="font-size: medium">
                            DAILY Receiving
                        </div>
                        <div style="font-size: xx-large">
                            {{ product_count }}
                        </div>
                    </div>

                    <div
                        class="d-flex align-center justify-center rounded-lg"
                        style="padding: 12px; background-color: #e3f2fd"
                    >
                        <v-icon
                            icon="mdi-package-variant"
                            size="28"
                            color="#1976D2"
                            style="opacity: 0.75"
                        />
                    </div>
                </v-card>

                <v-card
                    class="rounded-lg d-flex justify-space-between align-center pa-6"
                    style="background-color:"
                    elevation="2"
                >
                    <div>
                        <div class="text-grey" style="font-size: medium">
                            Total Products
                        </div>
                        <div style="font-size: x-large">
                            {{ product_count }}
                        </div>
                    </div>

                    <div
                        class="d-flex align-center justify-center rounded-lg"
                        style="padding: 12px; background-color: #e3f2fd"
                    >
                        <v-icon
                            icon="mdi-package-variant"
                            size="28"
                            color="#1976D2"
                            style="opacity: 0.75"
                        />
                    </div>
                </v-card>

                <v-card
                    class="rounded-lg d-flex justify-space-between align-center pa-6"
                    elevation="2"
                >
                    <div>
                        <div class="text-grey" style="font-size: medium">
                            Total Lot No.
                        </div>
                        <div style="font-size: x-large">
                            {{ lot_count }}
                        </div>
                    </div>

                    <div
                        class="d-flex align-center justify-center rounded-lg"
                        style="padding: 12px; background-color: #f3e5f5"
                    >
                        <v-icon
                            icon="mdi-layers-outline"
                            size="28"
                            color="#9C27B0"
                            style="opacity: 0.75"
                        />
                    </div>
                </v-card>

                <v-card
                    class="rounded-lg d-flex justify-space-between align-center pa-6"
                    elevation="2"
                >
                    <div>
                        <div class="text-grey" style="font-size: medium">
                            Total Serial
                        </div>
                        <div style="font-size: x-large">
                            {{ serial_count }}
                        </div>
                    </div>

                    <div
                        class="d-flex align-center justify-center rounded-lg"
                        style="padding: 12px; background-color: #e8f5e9"
                    >
                        <v-icon
                            icon="mdi-barcode-scan"
                            size="28"
                            color="#388E3C"
                            style="opacity: 0.75"
                        />
                    </div>
                </v-card>

                <v-card
                    class="rounded-lg d-flex justify-space-between align-center pa-6"
                    elevation="2"
                >
                    <div>
                        <div class="text-grey" style="font-size: medium">
                            Total Stockcard
                        </div>
                        <div style="font-size: x-large">
                            {{ stockcard_count }}
                        </div>
                    </div>

                    <!-- TOTAL STOCKCARD -->
                    <div
                        class="d-flex align-center justify-center rounded-lg"
                        style="padding: 12px; background-color: #fff3e0"
                    >
                        <v-icon
                            icon="mdi-clipboard-text-outline"
                            size="28"
                            color="#F57C00"
                            style="opacity: 0.75"
                        />
                    </div>
                </v-card>
            </div>

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
