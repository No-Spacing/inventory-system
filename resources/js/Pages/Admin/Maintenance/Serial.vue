<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";

defineProps({
    serials: Object,
});

const page = usePage();

const editSerialDialog = ref(false);

const snackbar = ref(false);
const errorSnackbar = ref(false);

const headers = [
    {
        title: "Id",
        key: "id",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Serial No.",
        key: "serial_no",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Total Items",
        key: "total_items",
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
];

const updateForm = useForm({
    id: null,
    serial_no: null,
});

function openUpdateDialog(item) {
    updateForm.id = item.id;
    updateForm.serial_no = item.serial_no;
    editSerialDialog.value = true;
}

function update() {
    updateForm.patch('/update-serial', {
        onSuccess: () => {
            editSerialDialog.value = false;
            snackbar.value = true;
        },
    })
}

</script>
<template>
    <Layout title="Serials">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table :headers="headers" :items="serials">
                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex justify-center ga-2 blue">
                            <v-btn prepend-icon="mdi-pencil"  @click="openUpdateDialog(item)" color="blue-darken-4">
                                Edit
                            </v-btn>
                            <v-btn prepend-icon="mdi-delete" @click="openDeleteDialog(item)" color="red-darken-4">
                                Delete
                            </v-btn>
                        </div>
                    </template>
                </v-data-table>
                <v-dialog v-model="editSerialDialog" max-width="500">
                    <v-card title="Edit Serial">
                        <v-form @submit.prevent="update">
                            <v-card-text>
                                <v-text-field
                                    v-model="updateForm.serial_no"
                                    :error-messages="updateForm.errors.serial_no"
                                    label="Serial No."
                                ></v-text-field>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="editSerialDialog = false"
                                >
                                    close
                                </v-btn>

                                <v-btn class="ma-2 pa-2 bg-blue-darken-4" type="submit">
                                    update
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
            </v-card>
        </v-container>
    </Layout>
</template>