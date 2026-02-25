<script setup>
import { ref, watch, defineAsyncComponent } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import dayjs from "dayjs"; 

import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, LinearScale, CategoryScale, LineController } from 'chart.js'
import { LineChart } from 'vue-chart-3'

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, LinearScale, CategoryScale, LineController)

import CardComponent from "./component/CardComponent.vue";
import Layout from "../../Layout/Main.vue";

const props = defineProps({
    products: Object,
    lot_count: Number,
    product_count: Number,
    serial_count: Number,
    stockcard_count: Number,
    monthlyInAndOut: Number,
    chartData: Object,
});

const months = props.chartData.map(item =>
  dayjs().month(item.month - 1).format("MMMM") // converts 1 → January, 2 → February, etc.
);
const inboundData = props.chartData.map(item => item.inbound_total);
const outboundData = props.chartData.map(item => item.outbound_total);


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

const chartData = {
  labels: months,
  datasets: [
    {
      label: 'Inbound',
      data: inboundData,
      borderColor: '#28a745',
      backgroundColor: 'rgba(40, 167, 69, 0.2)',
      tension: 0.4 // smooth curve
    },
    {
      label: 'Outbound',
      data: outboundData,
      borderColor: '#dc3545',
      backgroundColor: 'rgba(220, 53, 69, 0.2)',
      tension: 0.4
    }
  ]
}

// Chart.js options (similar to ApexCharts "chartOptions")
const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' },
    title: { display: true, text: 'Inbound vs Outbound' }
  },
  scales: {
    x: { title: { display: true, text: 'Months' } },
    y: { title: { display: true, text: 'Values' } }
  }
}


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
                :monthlyInAndOut="props.monthlyInAndOut"
            />
            <!-- ROW 2 Chart Card -->
            <v-row align="stretch">
                <v-col cols="12" md="6">
                    <v-card
                        class="pa-8 my-5 rounded-lg d-flex flex-column h-100"
                    >
                        <v-card-title>Inbound & Outbound Chart</v-card-title>
                        <div class="flex-grow-1">
                            <LineChart
                                :chart-data="chartData"
                                :chart-options="chartOptions"
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
            </v-row>
        </v-container>
    </Layout>
</template>
