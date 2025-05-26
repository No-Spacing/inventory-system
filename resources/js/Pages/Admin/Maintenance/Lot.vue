<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';


defineProps({
    lots: Object,
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
        title: "Lot",
        key: "lot",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];
const addLotDialog = ref(false);

const snackbar = ref(false);

const form = useForm({
    lot: ''
});

function submit() {
    form.post('/store-lot', {
        onSuccess: () => {
            addLotDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
        onError: () => {
        }
    })
}
</script>
<template>
    <Layout title="Lot/Batch">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table :headers="headers" :items="lots">
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
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <form>
                                    <v-text-field
                                        v-model="form.lot"
                                        :error-messages="form.errors.lot"
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
