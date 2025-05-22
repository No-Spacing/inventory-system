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
        uniqueId: 1,
    },
    {
        key: "product_desc",
        title: "Description",
        headerProps: {
            style: "font-weight: 700",
        },
        uniqueId: 2,
    },
    {
    //    key: "quantity",
        title: "Quantity",
        headerProps: {
            style: "font-weight: 700",
        },
        uniqueId: 3,
    },
    {
        key: "expdate",
        title: "Expiration Date",
        headerProps: {
            style: "font-weight: 700",
        },
        uniqueId: 4,
    },
    {
    //    key: "reference",
        title: "Reference",
        headerProps: {
            style: "font-weight: 700",
        },
        uniqueId: 5,
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
        :filter-keys="['code']"
        :headers="headers"
        :search="search"
        :items="$page.props.stockcards"
        item-value="uniqueId"
    >
    </v-data-table>
</template>