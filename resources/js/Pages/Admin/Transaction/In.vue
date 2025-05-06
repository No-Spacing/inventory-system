<script setup>
import {  router, useForm } from '@inertiajs/vue3'
import { reactive } from 'vue'
import Layout from "../../../Layout/Main.vue"

import { ref, watch } from 'vue'

const tab = ref(null)

const props = defineProps({ users: Object, products: Object})

const search = ref("");

const headers = [
    {
        align: "start",
        key: "name",
        sortable: true,
        title: "Name",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "email",
        title: "Email",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "created_at",
        title: "Date Created",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "role.name",
        title: "Roles",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];

let code = ref('');

const form = reactive({
    description: '',
})

watch(code, (newCode) => {
    router.get('/in',
        {
            code: newCode,
        },
        {
            preserveState: true,   
        }
    )
})
    
</script>

<template>
    <Layout title="In Transaction">
        <v-tabs
        v-model="tab"
        align-tabs="center"
        color="primary"
        >
            <v-tab :value="1">
                <v-icon icon="mdi-plus"></v-icon>
            </v-tab>
            <v-tab :value="2">
                <v-icon icon="mdi-format-list-bulleted"></v-icon>
            </v-tab>
        </v-tabs>

        <v-tabs-window v-model="tab">
            <v-tabs-window-item :value="1">
                <v-container fluid>
                    <v-form>
                        <v-row>
                            <v-col>
                                <div class="text-subtitle-1 text-medium-emphasis">Code</div>
                                <v-autocomplete
                                    :items="['1-BR-BD7800', '9-IC-BABPA1', '0-PM-104116', '4-EP-ECGMS3', 'P-MC-000006', '2-PP-KCPK24']"
                                    placeholder="Select..."
                                    variant="outlined"
                                    required
                                ></v-autocomplete>
                            </v-col>
                            <v-col>
                                <div class="text-subtitle-1 text-medium-emphasis">Unit</div>
                                <v-text-field
                                placeholder="Unit"
                                prepend-inner-icon=""
                                variant="outlined"
                                ></v-text-field>
                            </v-col>
                            <v-col>
                                <div class="text-subtitle-1 text-medium-emphasis">Total Items</div>
                                <v-text-field
                                placeholder="Total Items"
                                prepend-inner-icon=""
                                variant="outlined"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-form> 
                </v-container>
            </v-tabs-window-item>
            <v-tabs-window-item :value="2">
                <v-container fluid>
                    <v-data-table
                        :headers="headers"
                        :items="users"
                        :search="search"
                    ></v-data-table>
                </v-container>
            </v-tabs-window-item>
        </v-tabs-window>
    <!-- <v-card>
            <v-container fluid>
                <v-card-title>In Transaction</v-card-title>
              
                <v-form v-model="valid">
                    <v-container>
                       
                        <div class="d-flex">
                            <v-col>
                                <v-autocomplete
                                    label="Code"
                                    :items="['1-BR-BD7800', '9-IC-BABPA1', '0-PM-104116', '4-EP-ECGMS3', 'P-MC-000006', '2-PP-KCPK24']"
                                    variant="outlined" 
                                    :rules="['error']"  
                                ></v-autocomplete>
                            </v-col>
                            
                            <v-col>
                                <v-text-field
                                    label="Description"
                                    variant="outlined"  
                                ></v-text-field>
                            </v-col>

                            <v-col>
                                <v-text-field
                                    label="Unit"
                                    variant="outlined"  
                                ></v-text-field>
                            </v-col>              
                        </div>

                        <div class="d-flex">
                            <v-col>
                                <v-autocomplete
                                    label="Lot/Batch No."
                                    :items="['0000', '0001', '0002', '0003', '0004', '0005']"
                                    variant="outlined"  
                                    class="mx-1"
                                ></v-autocomplete>
                            </v-col>

                            <v-col>
                                <v-text-field
                                    label="Total Items"
                                    variant="outlined"  
                                    class="mx-1"
                                ></v-text-field>
                            </v-col>

                            <v-col>
                                <v-text-field
                                label="Total Items Per Lot/Batch"
                                variant="outlined"
                                class="mx-1"  
                            ></v-text-field>
                            </v-col>
                        </div>
                        
                    </v-container>
                </v-form>

                <v-text-field
                    type="date"
                    label="Expiration Date"
                ></v-text-field>

                <v-text-field
                    label="Reference In"
                ></v-text-field>

                <v-textarea
                    label="Remarks"
                ></v-textarea>

                <v-text-field
                    type="number"
                    label="Quantity"
                ></v-text-field>

                <v-text-field
                    type="date"
                    label="Date of Transaction"
                ></v-text-field>

                <v-text-field
                    type="date"
                    label="Date of Transaction"
                ></v-text-field>
            
                <v-btn
                    class="me-4"
                >
                    submit
                </v-btn>
            
                <v-btn>
                    clear
                </v-btn>

            </v-container>
        </v-card> -->
    </Layout>
</template>