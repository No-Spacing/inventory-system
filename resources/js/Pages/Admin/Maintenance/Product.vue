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
];
const addProductDialog = ref(false);
const snackbar = ref(false);

const form = useForm({
    code: '',
    description: '',
    category: '',
    unit: '',
    type: '',
    remaining_items: ''
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
</script>
<template>
    <Layout title="Product">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table :headers="headers" :items="products">
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

                                <v-text-field
                                    v-model="form.remaining_items"
                                    label="Remaining Items"
                                    :error-messages="form.errors.remaining_items"
                                ></v-text-field>
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
