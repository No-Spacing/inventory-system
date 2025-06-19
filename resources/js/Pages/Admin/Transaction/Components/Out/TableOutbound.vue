<script setup>
import { ref } from 'vue';

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
        key: "serialLotNo",
        title: "Serial/Lot No.",
        headerProps: {
            style: "font-weight: 700",
        },
    },    
    {
        key: "quantity",
        title: "Quantity",
        headerProps: {
            style: "font-weight: 700",
        },

    },
    {
        key: "warehouse",
        title: "Warehouse",
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
</script>
<template>
    <v-text-field
        v-model="search"
        variant="outlined"
        label="Search"
        hide-details
        single-line
    ></v-text-field>
    <v-data-table
        :filter-keys="['code', 'description', 'serialLotNo']"
        :headers="headers"
        :search="search"
        :items="$page.props.stockcards"
    ></v-data-table>
</template>