<script setup>
import Layout from "../../../Layout/Main.vue"
const headers = [
    { 
        title: 'Id',
        headerProps: {
            style: "font-weight: 700",
        }, 
    },
    { 
        title: 'Serial No.',
        headerProps: {
            style: "font-weight: 700",
        },
    },
]
</script>
<template>
    <Layout title="Serial Number">
        <v-card>
            <v-card-text>
                <v-data-table :headers="headers">
                    <template v-slot:bottom="{ page, itemsPerPage, pageCount, setItemsPerPage, setPage }">
                        <div class="d-flex align-center justify-space-between pa-4">
                        <v-btn color="primary">
                            Add Serial No.
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
            </v-card-text>
        </v-card>
    </Layout>
</template>