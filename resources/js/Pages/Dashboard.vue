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

const series = ref([]);
const chartOptions = ref({});

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

series.value = product.map((productCode) => {
    console.log("Building series for productCode:", productCode);

    return {
        name: productCode,
        data: months.map((month) => {
            const record = page.props.monthlyOutbound.find((item) => {
                // log each comparison
                console.log(
                    "Comparing productCode:",
                    productCode,
                    "with item.product.code:",
                    item.product?.code,
                    "for month:",
                    month,
                );
                return (
                    item.product?.code === productCode && item.month === month
                );
            });

            console.log(
                "Result for productCode:",
                productCode,
                "month:",
                month,
                "record:",
                record,
            );

            return record ? record.total_outbound : 0;
        }),
    };
});

chartOptions.value = {
    chart: {
        type: "bar",
        height: 350,
        stacked: false,
    },
    xaxis: {
        categories: months.map((m) =>
            new Date(new Date().getFullYear(), m - 1).toLocaleString(
                "default",
                { month: "short" },
            ),
        ),
    },

    annotations: {
        points: months.map((month) => {
            const winner = page.props.highestPerMonth[month];
            return {
                x: new Date(new Date().getFullYear(), month - 1).toLocaleString(
                    "default",
                    { month: "short" },
                ),
                y: winner.total_outbound,
                label: {
                    text: `Highest: ${winner.product.code}`, // ✅ correct
                    style: { background: "#FF4560", color: "#fff" },
                },
            };
        }),
    },
};
</script>
<template>
    <Layout title="Dashboard">
        <v-container class="pa-10" fluid>
            <div class="">
                <v-row>
                    <!-- Small statistic cards -->
                    <v-col cols="12" md="3">
                        <v-card
                            class="rounded-lg d-flex justify-space-between align-center pa-6"
                            style="background-color:"
                            elevation="2"
                        >
                            <div>
                                <div
                                    class="text-grey"
                                    style="font-size: medium"
                                >
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
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card
                            class="rounded-lg d-flex justify-space-between align-center pa-6"
                            elevation="2"
                        >
                            <div>
                                <div
                                    class="text-grey"
                                    style="font-size: medium"
                                >
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
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card
                            class="rounded-lg d-flex justify-space-between align-center pa-6"
                            elevation="2"
                        >
                            <div>
                                <div
                                    class="text-grey"
                                    style="font-size: medium"
                                >
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
                    </v-col>

                    <v-col cols="12" md="3">
                        <v-card
                            class="rounded-lg d-flex justify-space-between align-center pa-6"
                            elevation="2"
                        >
                            <div>
                                <div
                                    class="text-grey"
                                    style="font-size: medium"
                                >
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
                    </v-col>
                </v-row>
            </div>
            <v-row>
                <v-col cols="12" md="6">
                    <v-card elevation="2" class="pa-8 my-5 rounded-lg">
                        <v-card-title> </v-card-title>
                        <apexchart
                            type="line"
                            height="350,"
                            :options="chartOptions"
                            :series="series"
                        ></apexchart>
                    </v-card>
                </v-col>

                <v-col cols="12" md="12">
                    <v-card elevation="2" class="pa-8 my-5">
                        <v-card-title> New Products </v-card-title>
                        <v-divider></v-divider>
                        <v-data-table
                            :headers="headers"
                            :items="products"
                            :search="search"
                            :hide-default-footer="true"
                        ></v-data-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </Layout>
</template>
