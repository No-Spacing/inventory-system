<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref, watch, reactive } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";

const page = usePage();
const snackbar = ref(false);
const timeout = ref(2000);
const message = ref("");

defineProps({
    categories: Object,
});

// Create Inertia form object
const form = useForm({
    product_code: "",
    category_name: "",
    slug: "",
});

const updateForm = useForm({
    id: "",
    product_code: "",
    category_name: "",
    slug: "",
});

const headers = [
    {
        title: "Category",
        key: "category_name",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Slug",
        key: "slug",
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
        align: "end",
        sortable: false,
    },
];

const addCategoryDialog = ref();
const editCategoryDialog = ref();

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

function update() {
    // form.post('/category/update', {
    //     onSuccess: () => {
    //         editCategoryDialog.value = false;
    //         snackbar.value = true;
    //         form.reset();
    //     },
    //     onError: () => {
    //         // console.log(form.errors);
    //     }
    // })

    console.log("update");
}
</script>
<template>
    <Layout title="Category">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table :headers="headers" :items="categories">
                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex ga-2 justify-end">
                            <v-icon
                                color="medium-emphasis"
                                icon="mdi-pencil"
                                size="small"
                                @click="editCategoryDialog = true"
                            ></v-icon>

                            <v-icon
                                color="medium-emphasis"
                                icon="mdi-delete"
                                size="small"
                                @click="remove(item.id)"
                            ></v-icon>
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
                                <form>
                                    <v-text-field
                                        label="Product Code"
                                        v-model="form.product_code"
                                        :error-messages="
                                            form.errors.product_code
                                        "
                                    ></v-text-field>

                                    <v-text-field
                                        label="Category"
                                        v-model="form.category_name"
                                        :error-messages="
                                            form.errors.category_name
                                        "
                                    ></v-text-field>

                                    <v-text-field
                                        label="Slug"
                                        v-model="form.slug"
                                        disabled
                                    ></v-text-field>
                                </form>
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
                                <form>
                                    <v-text-field
                                        label="Product Code"
                                        v-model="updateForm.product_code"
                                        :error-messages="
                                            form.errors.product_code
                                        "
                                    ></v-text-field>

                                    <v-text-field
                                        label="Category"
                                        v-model="updateForm.category_name"
                                        :error-messages="
                                            form.errors.category_name
                                        "
                                    ></v-text-field>

                                    <v-text-field
                                        label="Slug"
                                        v-model="updateForm.slug"
                                        outlined
                                        disabled
                                    ></v-text-field>
                                </form>
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
                                    class="ma-2 pa-2 bg-primary"
                                    type="submit"
                                >
                                    submit
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>

                <v-snackbar v-model="snackbar" :timeout="timeout">
                    {{ $page.props.flash.message }}
                    <template v-slot:actions>
                        <v-btn
                            color="blue"
                            variant="text"
                            @click="snackbar = false"
                        >
                            Close
                        </v-btn>
                    </template>
                </v-snackbar>
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
