<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref, watch } from "vue";
import { usePage, router } from '@inertiajs/vue3';

defineProps({
    auditTrail: Object
})

const page = usePage();

const headers = [
    {
        title: "Item",
        key: "code",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Transaction Type",
        key: "transaction_type",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Created By",
        key: "enduser",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Created At",
        key: "created_at",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];

const search = ref('');

watch(() => search.value, (value) => {
    router.get('/audit-trail', {
        search: value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    })
})

function paginations(){
    router.get('/audit-trail', {
        search: search.value,
        page: page.props.auditTrail.current_page
    }, 
    {
        preserveState: true,
        replace: true
    })
}

</script>
<template>
    <Layout title="Audit Trail">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-text-field 
                    variant="outlined" 
                    label="Search" 
                    v-model="search"
                ></v-text-field>
                <v-data-table 
                :headers="headers" 
                :items="auditTrail.data"
                >
                    <template v-slot:bottom>
                        <v-row
                            class="d-flex align-center justify-end mt-5"
                        >
                            <v-col cols="6">
                                <v-pagination 
                                    v-model="auditTrail.current_page" 
                                    :length="auditTrail.last_page" 
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
