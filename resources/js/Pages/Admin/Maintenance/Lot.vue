<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref, watch } from "vue";
import { watchDebounced } from "@vueuse/core";
import { useForm, router } from '@inertiajs/vue3';
import { VDateInput } from 'vuetify/labs/VDateInput'

defineProps({
    lots: Object,
    products: Object
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
        title: "Code",
        key: "code",
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
    {
        title: "Expiration Date",
        key: "expiration_date",
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


const editLotDialog = ref(false);
const addLotDialog = ref(false);
const deleteDialog = ref(false);
const snackbar = ref(false);

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
            "/lot",
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
    lot: null,
    expiration_date: null,
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

const updateForm = useForm({
    id: '',
    lot: '',
    code: '',
    expiration_date: ''
});

function openUpdateDialog(item) {
    updateForm.id = item.id;
    updateForm.lot = item.lot;
    updateForm.code = item.product_id;
    updateForm.expiration_date = item.expiration_date;
    editLotDialog.value = true;
}

function update() {
    updateForm.patch('/update-lot', {
        onSuccess: () => {
            editLotDialog.value = false;
            snackbar.value = true;
        },
    })
}

function openDeleteDialog(item) {
    updateForm.id = item.id;
    deleteDialog.value = true;
}
function deleteLot() {
    updateForm.delete('/delete-lot', {
        onSuccess: () => {
            deleteDialog.value = false;
            snackbar.value = true;
        },
    })
}

const search = ref('');

watch(() => search.value, (value) => {
    router.get('/lot', {
        search: value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    })
})

function paginations(){
    router.get('/lot', {
        search: search.value,
        page: page.props.lots.current_page
    }, 
    {
        preserveState: true,
        replace: true
    })
}
</script>
<template>
    <Layout title="Lot/Batch">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-text-field 
                    variant="outlined" 
                    label="Search" 
                    v-model="search"
                ></v-text-field>
                <v-data-table :headers="headers" :items="lots.data">
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
                    
                    <template v-slot:bottom>
                        <v-row
                            class="d-flex align-center justify-space-between mt-5"
                        >
                            <v-col cols="4">
                                <v-btn @click="addLotDialog = true" color="primary">
                                    Add Lot/Batch
                                </v-btn>
                            </v-col>
                            <v-col cols="6">
                                <v-pagination 
                                    v-model="lots.current_page" 
                                    :length="lots.last_page" 
                                    @update:modelValue="paginations"
                                ></v-pagination> 
                            </v-col>
                        </v-row>
                    </template>
                </v-data-table>
                <v-dialog v-model="addLotDialog" max-width="500">
                    <v-card title="Add Lot/Batch">
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <v-autocomplete
                                    v-model="form.code"
                                    v-model:search="searchCode"
                                    :items="products"
                                    item-title="code"
                                    item-value="id"
                                    placeholder="Select..."
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

                                <v-text-field
                                    v-model="form.lot"
                                    :error-messages="form.errors.lot"
                                    label="Lot/Batch No."
                                    variant="outlined"
                                ></v-text-field>

                                <v-date-input
                                    v-model="form.expiration_date"
                                    :error-messages="form.errors.expiration_date"
                                    label="Expiration Date"
                                    prepend-icon=""
                                    prepend-inner-icon="$calendar"
                                    variant="outlined"
                                ></v-date-input>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="addLotDialog = false"
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
                <v-dialog v-model="editLotDialog" max-width="500">
                    <v-card title="Edit Lot/Batch">
                        <v-form @submit.prevent="update">
                            <v-card-text>
                                <v-autocomplete
                                    v-model="updateForm.code"
                                    v-model:search="searchCode"
                                    :error-messages="form.errors.code"
                                    :items="products"
                                    item-title="code"
                                    item-value="id"
                                    placeholder="Select..."
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
                                <v-text-field
                                    v-model="updateForm.lot"
                                    :error-messages="updateForm.errors.lot"
                                    label="Lot/Batch No."
                                ></v-text-field>
                                <div
                                    class="text-subtitle-1 text-medium-emphasis mx-2"
                                >
                                    Expiration Date
                                </div>

                                <v-date-input
                                    v-model="updateForm.expiration_date"
                                    :error-messages="updateForm.errors.expiration_date"
                                    placeholder="Date from"
                                    prepend-icon=""
                                    prepend-inner-icon="$calendar"
                                    variant="outlined"
                                    class="mx-2"
                                ></v-date-input>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="editLotDialog = false"
                                >
                                    close
                                </v-btn>

                                <v-btn class="ma-2 pa-2 bg-blue-darken-4" :loading="updateForm.processing" type="submit">
                                    update
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
                <v-dialog  v-model="deleteDialog"  max-width="400">
                    <v-form @submit.prevent="deleteLot">
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
