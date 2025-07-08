<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref, watch } from "vue";
import { useForm, router } from '@inertiajs/vue3';


defineProps({
    warehouses: Object,
})
const headers = [
    {
        title: "Id",
        key: "id",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Warehouse",
        key: "warehouse",
        headerProps: {
            style: "font-weight: 700",
        },
    }
];
const addWarehouseDialog = ref(false);

const snackbar = ref(false);

const search = ref();

const form = useForm({
    warehouse: null,
});

function submit() {
    form.post('/store-warehouse', {
        onSuccess: () => {
            addWarehouseDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
        onError: (errors) => {
            
        }
    })
}

watch(() => search.value, (value) => {
    router.get('/warehouse', {
        search: value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    })
})

function paginations(){
    router.get('/warehouse', {
        search: search.value,
        page: page.props.warehouses.current_page
    }, 
    {
        preserveState: true,
        replace: true
    })
}
</script>
<template>
    <Layout title="Warehouse">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-text-field 
                    variant="outlined" 
                    label="Search" 
                    v-model="search"
                ></v-text-field>
                <v-data-table :headers="headers" :items="warehouses.data">
                    <template
                        v-slot:bottom
                    >
                        <v-row class="d-flex align-center justify-space-between mt-5">
                                <v-col cols="4">
                                    <v-btn @click="addWarehouseDialog = true" color="primary">
                                        Add Warehouse
                                    </v-btn>
                                </v-col>
                                <v-col cols="6">
                                    <v-pagination 
                                        v-model="warehouses.current_page" 
                                        :length="warehouses.last_page" 
                                        @update:modelValue="paginations"
                                    ></v-pagination> 
                                </v-col>
                            </v-row>
                    </template>
                </v-data-table>
                <v-dialog v-model="addWarehouseDialog" max-width="500">
                    <v-card title="Add Warehouse">
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <v-text-field
                                    v-model="form.warehouse"
                                    :error-messages="form.errors.warehouse"
                                    label="Warehouse"
                                ></v-text-field>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="addWarehouseDialog = false"
                                >
                                    close
                                </v-btn>

                                <v-btn class="ma-2 pa-2 bg-primary" type="submit" :loading="form.processing">
                                    submit
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
            </v-card>
        </v-container>
    </Layout>
    <v-snackbar
        v-model="snackbar" 
        :timeout="2000"
        color="success"
        variant="elevated"
        location="top"
        class="text"
    >
        {{ $page.props.flash.message }}
    </v-snackbar>
</template>
