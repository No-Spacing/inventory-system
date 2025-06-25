<script setup>
import Layout from "../../Layout/Main.vue";
import { ref, watch } from "vue";
import { useForm, router } from '@inertiajs/vue3'

defineProps({
    stockcards: Object,
    warehouses: Object,
})

const headers = [
    { 
        title: 'Code',
        key: 'code',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Description',
        key: 'description',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Batch/Lot/Serian No.',
        key: 'serialLotNo',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Remarks',
        key: 'remarks',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: 'Quantity',
        key: 'quantity',
        headerProps: {
            style: "font-weight: 700",
        }, 
    },
    {
        title: "Actions",
        key: "actions",
        headerProps: {
            style: "font-weight: 700",
        },
        align: "center",
        sortable: false,
    },
]

const form = useForm({
    warehouse: null,
})

watch(() => form.warehouse, (value) => {
        router.get('/receive', {
            warehouse: value
        },
        {
            preserveState: true
        })
    }
);
</script>
<template>
    <Layout title="Receive Items">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-row>
                    <v-col cols="4">
                        <v-select
                            v-model="form.warehouse"
                            placeholder="Select warehouse..."
                            :items="warehouses"
                            item-title="warehouse"
                            item-value="id"
                            variant="outlined"
                            clearable
                        ></v-select>
                    </v-col>
                </v-row>
                <v-data-table :headers="headers" :items="stockcards">
                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex justify-center ga-2 blue">
                            <v-btn 
                                append-icon="mdi-send"
                                color="#FFEE58"
                            >
                                Received
                            </v-btn>
                            <v-btn prepend-icon="mdi-delete" color="red-darken-4">
                                Cancel
                            </v-btn>
                        </div>
                    </template>
                
                </v-data-table>
            </v-card>
        </v-container>
    </Layout>
    <!-- <v-snackbar
        v-model="snackbar" 
        :timeout="2000"
        color="success"
        variant="elevated"
        location="top"
        class="text"
    >
        {{ $page.props.flash.message }}
    </v-snackbar> -->
</template>
