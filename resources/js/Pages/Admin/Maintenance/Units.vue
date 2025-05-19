<script setup>
import Layout from "../../../Layout/Main.vue"
import { ref } from "vue";
const headers = [
    { 
        title: 'Id',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Unit',
        headerProps: {
            style: "font-weight: 700",
        },
    },
]
const addUnitDialog = ref(false);
</script>
<template>
    <Layout title="Units">
        <v-card title="Units">
            <v-card-text>
                <v-data-table :headers="headers">
                    <template v-slot:bottom="{ page, itemsPerPage, pageCount, setItemsPerPage, setPage }">
                        <div class="d-flex align-center justify-space-between pa-4">
                        <v-btn color="primary" @click="addUnitDialog = true">
                            Add Units
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
                <v-dialog v-model="addUnitDialog" max-width="500">
                    <v-card title="Add Units">
                        <v-card-text>
                            <form @submit.prevent="submit">
                                <v-text-field
                                label="Product Code"
                                ></v-text-field>

                                <v-text-field
                                label="Unit"
                                ></v-text-field>
                            </form>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn class="ma-2 pa-2" @click="addUnitDialog = false">
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
            </v-card-text>
        </v-card>
    </Layout>
</template>