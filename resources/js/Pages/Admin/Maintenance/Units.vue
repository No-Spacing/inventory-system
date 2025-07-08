<script setup>
import Layout from "../../../Layout/Main.vue"
import { ref, watch } from "vue";
import { useForm, router, usePage } from '@inertiajs/vue3';

const page = usePage();

const snackbar = ref(false)
const errorSnackbar = ref(false);
const addUnitDialog = ref(false);
const editUnitDialog = ref(false);
const deleteDialog = ref(false);

const search = ref('');

const form = useForm({
    unit: ''
});

const updateForm = useForm({
    id: '', 
    unit: ''
});

const emit = defineEmits(['snackbar']);

const props = defineProps({
    units: Object
})
const headers = [
    {
        title: "ID",
        key: "id",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Unit',
        key: 'unit',
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
]

function submit() {
    form.post('/store-unit', {
        onSuccess: () => {
            addUnitDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
    })
}



function openUpdateDialog(item){
    updateForm.id = item.id;
    updateForm.unit = item.unit;
    editUnitDialog.value = true;
}

function update() {
    updateForm.patch('/update-unit', {
        onSuccess: () => {
            editUnitDialog.value = false;
            snackbar.value = true;
        },
        onError: () => {
        }
    })
}

function openDeleteDialog(item){
    updateForm.id = item.id;
    deleteDialog.value = true;
}

function deleteUnit(){
    updateForm.delete('/delete-unit', {
        onSuccess: () => {
            snackbar.value = true;
            deleteDialog.value = false;
        },
        onError: () => {
            errorSnackbar.value = true;
        }
    })
}

watch(() => search.value, (value) => {
    router.get('/units', {
        search: value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    })
})

function paginations(){
    router.get('/units', {
        search: search.value,
        page: page.props.units.current_page
    }, 
    {
        preserveState: true,
        replace: true
    })
}
</script>
<template>
    <Layout title="Units">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-text-field 
                    variant="outlined" 
                    label="Search" 
                    v-model="search"
                ></v-text-field>
                <v-card-text>
                    <v-data-table :headers="headers" :items="units.data">
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
                                    <v-btn
                                        @click="addUnitDialog = true"
                                        color="primary"
                                        class="mx-2"
                                    >
                                        Add Unit
                                    </v-btn>
                                </v-col>
                                <v-col cols="6">
                                    <v-pagination 
                                        v-model="units.current_page" 
                                        :length="units.last_page" 
                                        @update:modelValue="paginations"
                                    ></v-pagination> 
                                </v-col>
                            </v-row>
                        </template>
                    </v-data-table>
                    <v-dialog v-model="addUnitDialog" max-width="500">
                        <v-card title="Add Units">
                            <form @submit.prevent="submit">
                                <v-card-text>
                                
                                        <v-text-field
                                        v-model="form.unit"
                                        :error-messages="form.errors.unit"
                                        label="Unit"
                                        ></v-text-field>
                                    
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn class="ma-2 pa-2" @click="addUnitDialog = false">
                                    close
                                    </v-btn>
                                    
                                    <v-btn 
                                    class="ma-2 pa-2 bg-primary"
                                    type="submit"
                                    :loading="form.processing"
                                    >
                                    submit
                                    </v-btn>
                                </v-card-actions>
                            </form>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="editUnitDialog" max-width="500">
                        <v-card title="Edit Unit">
                            <form @submit.prevent="update">
                                <v-card-text>
                                    <v-text-field
                                    v-model="updateForm.unit"
                                    :error-messages="updateForm.errors.unit"
                                    label="Unit"
                                    ></v-text-field>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn class="ma-2 pa-2" @click="editUnitDialog = false">
                                    close
                                    </v-btn>
                                    
                                    <v-btn 
                                    class="ma-2 pa-2 bg-blue-darken-4"
                                    type="submit"
                                    :loading="updateForm.processing"
                                    >
                                    update
                                    </v-btn>
                                </v-card-actions>
                            </form>
                        </v-card>
                    </v-dialog>
                    <v-dialog  v-model="deleteDialog"  max-width="400">
                        <v-form @submit.prevent="deleteUnit">
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
                </v-card-text>
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
</template>