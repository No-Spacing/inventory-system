<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref } from "vue";
const headers = [
    {
        title: "Id",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Lot",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];
const addLotDialog = ref(false);
</script>
<template>
    <Layout title="Lot/Batch">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table :headers="headers">
                    <template
                        v-slot:bottom="{
                            page,
                            itemsPerPage,
                            pageCount,
                            setItemsPerPage,
                            setPage,
                        }"
                    >
                        <div
                            class="d-flex align-center justify-space-between pa-4"
                        >
                            <v-btn @click="addLotDialog = true" color="primary">
                                Add Lot/Batch
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
                <v-dialog v-model="addLotDialog" max-width="500">
                    <v-card title="Add Lot/Batch">
                        <v-card-text>
                            <form>
                                <v-text-field
                                    label="Lot/Batch No."
                                ></v-text-field>
                            </form>
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn
                                class="ma-2 pa-2"
                                @click="addLotDialog = false"
                            >
                                close
                            </v-btn>

                            <v-btn class="ma-2 pa-2 bg-primary" type="submit">
                                submit
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-card>
        </v-container>
    </Layout>
</template>
