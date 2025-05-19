<script setup>
import Layout from "../../../Layout/Main.vue"
import { ref } from "vue"
const headers = [
    { 
        title: 'Code',
        headerProps: {
            style: "font-weight: 700",
        }, 
    },
    { 
        title: 'Product Description',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Product Type',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Expiration Date',
        headerProps: {
            style: "font-weight: 700",
        },
    },
]
const addProductDialog = ref(false)
</script>
<template>
    <Layout title="Product">
        <v-data-table :headers="headers">
            <template v-slot:bottom="{ page, itemsPerPage, pageCount, setItemsPerPage, setPage }">
                <div class="d-flex align-center justify-space-between pa-4">
                <v-btn @click="addProductDialog = true" color="primary">
                    Add Product
                </v-btn>

                <v-data-table-footer
                    :page="page"
                    :items-per-page="itemsPerPage"
                    :page-count="pageCount"
                    @update:items-per-page="setItemsPerPage"
                    @update:page="setPage"
                />
                </div>
            </template>
        </v-data-table>
        <v-dialog v-model="addProductDialog" max-width="500">
            <v-card title="Add Product">
                <v-card-text>
                    <form>
                        <v-select 
                        label="Category" 
                        :items="['MEDICAL EQUIPMENT', 'MEDICAL SUPPLY', 'OFFICE SUPPLY']"
                        ></v-select>

                        <v-text-field
                        label="Product Code"
                        ></v-text-field>

                        <v-text-field
                        label="Product Name"
                        ></v-text-field>

                        <v-select 
                        label="Unit"
                        :items="['Bottle', 'Box', 'Each', 'Gallon']"
                        ></v-select>
                        <v-select 
                        label="Product Type"
                        :items="['Track by Lot', 'Serial']"
                        ></v-select>
                    </form>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn class="ma-2 pa-2" @click="addProductDialog = false">
                    close
                    </v-btn>
                    
                    <v-btn 
                    class="ma-2 pa-2 bg-primary"
                    type="submit"
                    >
                    submit
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog> 
    </Layout>
</template>