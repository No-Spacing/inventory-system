<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';

defineProps({
    categories: Object,
    units: Object,
    products: Object,
})
const headers = [
    {
        title: "Code",
        key: 'code',
        headerProps: {
            style: "font-weight: 700",
        }

    },
    {
        title: "Product Description",
        key: 'description',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Product Type",
        key: 'type',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Remaining Items",
        key: 'remaining_items',
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
const addProductDialog = ref(false);
const editProductDialog = ref(false);
const deleteDialog = ref(false);
const snackbar = ref(false);

const form = useForm({
    code: '',
    description: '',
    category: '',
    unit: '',
    type: '',
    remaining_items: ''
});

const updateForm = useForm({
    id: '',
    code: '',
    description: '',
    category: '',
    unit: '',
    type: '',
    remaining_items: '',
});

function submit() {
    form.post('/store-product', {
        onSuccess: () => {
            addProductDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
        onError: () => {
        }
    })
}

function openUpdateDialog(item) {
    updateForm.id = item.id
    updateForm.code = item.code;
    updateForm.description = item.description;
    updateForm.category = item.category;
    updateForm.unit = item.unit;
    updateForm.type = item.type;
    updateForm.remaining_items = item.remaining_items
    editProductDialog.value = true;
}

function update() {
    updateForm.patch('/update-product', {
        onSuccess: () => {
           snackbar.value = true; 
           editProductDialog.value = false;
        },
    })
}

function openDeleteDialog(item){
    updateForm.id = item.id
    deleteDialog.value = true;
}

function deleteProduct() {
    updateForm.delete('/delete-product', {
        onSuccess: () => {
            deleteDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
    })
}
</script>
<template>
    <Layout title="Product">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table :headers="headers" :items="products">
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
                            <v-btn
                                @click="addProductDialog = true"
                                color="primary"
                            >
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
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <v-text-field
                                    v-model="form.code"
                                    label="Product Code"
                                    :error-messages="form.errors.code"
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.description"
                                    label="Product Description"
                                    :error-messages="form.errors.description"
                                ></v-text-field>

                                <v-select
                                    v-model="form.category"
                                    label="Category"
                                    :error-messages="form.errors.category"
                                    :items="categories"
                                    item-value="id"
                                    item-title="category_name"
                                ></v-select>

                                <v-select
                                    v-model="form.unit"
                                    :error-messages="form.errors.unit"
                                    label="Unit"
                                    :items="units"
                                    item-value="id"
                                    item-title="unit"
                                ></v-select>

                                <v-select
                                    v-model="form.type"
                                    :error-messages="form.errors.type"
                                    label="Product Type"
                                    :items="['Track by Lot', 'Track by Serial']"
                                ></v-select>

                                <!-- <v-text-field
                                    v-model="form.remaining_items"
                                    label="Remaining Items"
                                    :error-messages="form.errors.remaining_items"
                                ></v-text-field> -->
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="addProductDialog = false"
                                >
                                    close
                                </v-btn>

                                <v-btn class="ma-2 pa-2 bg-primary" type="submit" :loading="form.processing">
                                    submit
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
                <v-dialog v-model="editProductDialog" max-width="500">
                    <v-card title="Edit Product">
                        <v-form @submit.prevent="update">
                            <v-card-text>
                                <v-text-field
                                    v-model="updateForm.code"
                                    :error-messages="updateForm.errors.code"
                                    label="Product Code"
                                ></v-text-field>

                                <v-text-field
                                    v-model="updateForm.description"
                                    :error-messages="updateForm.errors.description"
                                    label="Product Description"
                                ></v-text-field>

                                <v-select
                                    v-model="updateForm.category"
                                    :error-messages="updateForm.errors.category"
                                    label="Category"
                                    :items="categories"
                                    item-value="id"
                                    item-title="category_name"
                                ></v-select>

                                <v-select
                                    v-model="updateForm.unit"
                                    :error-messages="updateForm.errors.unit"
                                    label="Unit"
                                    :items="units"
                                    item-value="id"
                                    item-title="unit"
                                ></v-select>

                                <v-select
                                    v-model="updateForm.type"
                                    :error-messages="updateForm.errors.type"
                                    label="Product Type"
                                    :items="['Track by Lot', 'Track by Serial']"
                                ></v-select>

                                <!-- <v-text-field
                                    v-model="updateForm.remaining_items"
                                    label="Remaining Items"
                                    :error-messages="updateForm.errors.remaining_items"
                                ></v-text-field> -->
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="editProductDialog = false"
                                >
                                    close
                                </v-btn>

                                <v-btn class="ma-2 pa-2 bg-blue-darken-4" type="submit" :loading="form.processing">
                                    update
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
                <v-dialog  v-model="deleteDialog"  max-width="400">
                    <v-form @submit.prevent="deleteProduct">
                        <v-card
                            title="Are you sure you want to delete?"
                            text="This action cannot be undone."
                        >
                            
                                <template v-slot:actions>
                                    <v-spacer></v-spacer>

                                    <v-btn @click="deleteDialog = false">
                                        cancel
                                    </v-btn>

                                    <v-btn type="submit" class="ma-2 pa-2 bg-red-darken-4">
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
