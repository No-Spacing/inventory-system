<script setup>
import { ref, watch } from 'vue';
import { router, usePage } from "@inertiajs/vue3";

const page = usePage();

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
        key: "transaction_type",
        title: "Transaction Type",
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

const search = ref('');

watch(() => search.value, (value) => {
    router.get('/transaction', {
        search: value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    })
})

function paginations(){
    router.get('/transaction', {
        search: search.value,
        page: page.props.stockcards.current_page
    }, {
        preserveState: true,
        replace: true
    })
}

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
        :headers="headers"
        :items="$page.props.stockcards.data"
    >
        <template v-slot:bottom>
            <v-row
                class="d-flex align-center justify-end mt-5"
            >
                <v-col cols="6">
                    <v-pagination 
                        v-model="$page.props.stockcards.current_page" 
                        :length="$page.props.stockcards.last_page" 
                        @update:modelValue="paginations"
                    ></v-pagination> 
                </v-col>
            </v-row>
        </template>
    </v-data-table>
</template>