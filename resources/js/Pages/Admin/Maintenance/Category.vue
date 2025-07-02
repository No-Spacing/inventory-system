<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref, watch, reactive } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";

const page = usePage();
const snackbar = ref(false);
const errorSnackbar = ref(false);

defineProps({
    categories: Object,
});

// Create Inertia form object
const form = useForm({
    category_name: "",
    slug: "",
});

const updateForm = useForm({
    id: "",
    category_name: "",
    slug: "",
});

const headers = [
    {
        title: "ID",
        key: "id",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Category",
        key: "category_name",
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

const addCategoryDialog = ref();
const editCategoryDialog = ref();
const deleteDialog = ref(false);

// Watch category and generate slug dynamically
watch(
    () => form.category_name,
    (value) => {
        form.slug = value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, "")
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-");
    }
);

watch(
    () => updateForm.category_name,
    (value) => {
        updateForm.slug = value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, "")
            .replace(/\s+/g, "-")
            .replace(/-+/g, "-");
    }
);


function submit() {
    form.post("/store-category", {
        onSuccess: () => {
            addCategoryDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
        onError: () => {
            // console.log(form.errors);
        },
    });
}

function openUpdateDialog (item) {
    updateForm.id = item.id;
    updateForm.category_name = item.category_name;
    editCategoryDialog.value = true;
}

function update() {
    updateForm.patch('/update-category', {
        onSuccess: () => {
            editCategoryDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
    })
}

function openDeleteDialog(item) {
    updateForm.id = item.id;
    deleteDialog.value = true;
}

function deleteCategory (item) {
    updateForm.delete('/delete-category', {
        onSuccess: () => {
            deleteDialog.value = false;
            snackbar.value = true;
        },
        onError: () => {
            errorSnackbar.value = true;
        }
    })
}
</script>
<template>
    <Layout title="Category">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table
                    :headers="headers"
                    :items="categories"
                >
                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex justify-center ga-2">
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
                                color="primary"
                                @click="addCategoryDialog = true"
                            >
                                Add Category
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
                <v-dialog v-model="addCategoryDialog" max-width="500">
                    <v-card title="Add Category">
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <v-text-field
                                    label="Category"
                                    v-model="form.category_name"
                                    :error-messages="form.errors.category_name"
                                ></v-text-field>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="addCategoryDialog = false"
                                >
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
                        </v-form>
                    </v-card>
                </v-dialog>
                <v-dialog v-model="editCategoryDialog" max-width="500">
                    <v-card title="Edit Category">
                        <v-form @submit.prevent="update">
                            <v-card-text>
                                    <v-text-field
                                        v-model="updateForm.category_name"
                                        :error-messages="updateForm.errors.category_name"
                                        label="Category"
                                    ></v-text-field>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="editCategoryDialog = false"
                                >
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
                        </v-form>
                    </v-card>
                </v-dialog>
                <v-dialog  v-model="deleteDialog"  max-width="400">
                    <v-form @submit.prevent="deleteCategory">
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
    <v-snackbar
        v-model="errorSnackbar"
        :timeout="2000"
        color="error"
        variant="elevated"
        location="top"
        class="text"
    >
        {{ $page.props.errors.message }}
    </v-snackbar>
</template>
