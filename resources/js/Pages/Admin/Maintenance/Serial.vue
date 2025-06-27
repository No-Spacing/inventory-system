<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref, watch } from "vue";
import { watchDebounced } from "@vueuse/core";
import { usePage, useForm, router } from "@inertiajs/vue3";
import { nullifyTransforms } from "vuetify/lib/util/animation.mjs";
import { fa } from "vuetify/locale";

defineProps({
    serials: Object,
    products: Object,
});

const page = usePage();

const editSerialDialog = ref(false);
const deleteDialog = ref(false);

const snackbar = ref(false);
const errorSnackbar = ref(false);

const addSerialDialog = ref(false);

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
        title: "Actions",
        key: "actions",
        headerProps: {
            style: "font-weight: 700",
        },
        align: "center",
        sortable: false,
    },
];

const loading = ref(false);
const preload = ref(false);

const searchCode = ref();

watch(() => searchCode.value, () => {
    loading.value = true
    preload['value'] = true;
})

watchDebounced(() => searchCode.value,
    (value) => {
        router.get(
            "/serial",
            {
                code: value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    loading.value = false
                    preload['value'] = false;
                },
            }
        );
    },
    { debounce: 1000, maxWait: 5000 }
);

const form = useForm({
    code: null,
    serial_no: null,
    serialStarting: null,
    serialEnding: null,
})

function submit() {
    form.post('/store-serial', {
        onSuccess: () => {
            addSerialDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
        onError: () => {
            errorSnackbar.value = true;
        }
    })
}

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

function openDeleteDialog(item)
{
    updateForm.id = item.id;
    deleteDialog.value = true;
}

function deleteSerial() {
    updateForm.delete('/delete-serial', {
        onSuccess: () => {
            snackbar.value = true;
            deleteDialog.value = false;
        },
        onError: () => {
            errorSnackbar.value = true;
        }
    })
}

</script>
<template>
    <Layout title="Serials">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-table>
                    <thead>
                    <tr>
                        <th class="text-left">
                        Id
                        </th>
                        <th class="text-left">
                        Serial No.
                        </th>
                        <th class="text-left">
                        Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="item in serials"
                        :key="item.id"
                    >
                        <td>{{ item.id }}</td>
                        <td>{{ item.serial_no }}</td>
       
                        <td>
                            <v-btn prepend-icon="mdi-pencil"  @click="openUpdateDialog(item)" color="blue-darken-4">
                                Edit
                            </v-btn>
                            <v-btn prepend-icon="mdi-delete" @click="openDeleteDialog(item)" color="red-darken-4">
                                Delete
                            </v-btn>
                        </td>
                    </tr>
                    </tbody>
                </v-table>
                    <v-btn @click="addSerialDialog = true" color="primary">
                        Add Serial
                    </v-btn>
                
                <v-pagination :length="8"></v-pagination>

                <!-- <v-data-table :headers="headers" :items="serials">
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
                            <v-btn @click="addSerialDialog = true" color="primary">
                                Add Serial
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
                </v-data-table> -->
                <v-dialog v-model="addSerialDialog" max-width="500">
                    <v-card title="Add Lot/Batch">
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <v-autocomplete
                                    v-model="form.code"
                                    v-model:search="searchCode"
                                    :error-messages="form.errors.code"
                                    :items="products"
                                    item-title="code"
                                    item-value="id"
                                    placeholder="Select a product..."
                                    variant="outlined"
                                    menu-icon=""
                                    @update:modelValue="preload = true;"
                                >
                                    <template v-slot:no-data>
                                        <v-container fluid>
                                            <span v-if="preload">Loading...</span>
                                            <span v-else>No results found</span>
                                        </v-container>
                                    </template>
                                    <template #append-inner>
                                        <v-progress-circular
                                        v-if="preload"
                                        indeterminate
                                        size="20"
                                        color="primary"
                                        />
                                    </template>
                                </v-autocomplete>

                                <div class="d-flex justify-center mb-5">
                                    <v-img 
                                        cover
                                        src="https://cdn.vuetifyjs.com/images/parallax/material.jpg"
                                    ></v-img>
                                </div>
                                <v-text-field
                                    v-model="form.serial_no"
                                    :error-messages="form.errors.serial_no"
                                    label="Serial No."
                                ></v-text-field>

                                <v-row no-gutters>
                                    <v-col cols="6">
                                        <v-text-field
                                            v-model="form.serialStarting"
                                            :error-messages="form.errors.serialStarting"
                                            label="Serial Starting No."
                                            class="mr-1"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="6">
                                        <v-text-field
                                            v-model="form.serialEnding"
                                            :error-messages="form.errors.serialEnding"
                                            label="Serial Ending No."
                                            class="ml-1"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>

                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="addSerialDialog = false"
                                >
                                    close
                                </v-btn>

                                <v-btn class="ma-2 pa-2 bg-primary" :loading="form.processing" type="submit">
                                    submit
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
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

                                <v-btn class="ma-2 pa-2 bg-blue-darken-4" type="submit" :loading="updateForm.processing">
                                    update
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
                <v-dialog  v-model="deleteDialog"  max-width="400">
                        <v-form @submit.prevent="deleteSerial">
                            <v-card
                                title="Are you sure you want to delete?"
                                text="This action cannot be undone."
                            >
                                <template v-slot:actions>
                                <v-spacer></v-spacer>

                                <v-btn @click="deleteDialog = false">
                                    cancel
                                </v-btn>

                                <v-btn type="submit" class="ma-2 pa-2 bg-red-darken-4" :loading="updateForm.processing">
                                    delete
                                </v-btn>
                                </template>
                            </v-card>
                        </v-form>
                    </v-dialog>
            </v-card>
        </v-container>
        <v-snackbar
            v-model="snackbar"
            :timeout="3000"
            color="success"
            variant="elevated"
            location="top"
            class="text"
        >
            {{ $page.props.flash.message }}
        </v-snackbar>
        <v-snackbar
            v-model="errorSnackbar"
            :timeout="3000"
            color="error"
            variant="elevated"
            location="top"
            class="text"
        >
            {{ $page.props.errors.message }}
        </v-snackbar>
    </Layout>
</template>