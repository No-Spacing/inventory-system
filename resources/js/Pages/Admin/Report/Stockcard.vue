<script setup>
import Layout from "../../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
import { ref, watch } from "vue";
import { watchDebounced } from "@vueuse/core";
import { useForm, router } from "@inertiajs/vue3"

defineProps({
    products: Object,
    lots: Object
})

const loading = ref(false);

const searchCode = ref();
const searchLot = ref();

const preload = ref(false);

const form = useForm({
    code: null,
    lot: null,
})

watch(() => searchCode.value, () => {
    loading.value = true
    preload['value'] = true;
})

watchDebounced(() => searchCode.value,
    (value) => {
        router.get(
            "/stockcard",
            {
                code: value,
                lot: searchLot.value
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

watch(() => searchLot.value, () => {
    loading.value = true;
    preload['value'] = true;
})

watchDebounced(() => searchLot.value,
    (value) => {
        router.get(
            "/stockcard",
            {
                code: searchCode.value,
                lot: value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    loading.value = false;
                    preload['value'] = false;
                },
            }
        );
    },
    { debounce: 1000, maxWait: 5000 }
);

function submit(){
    form.post('index-pdf')
}
</script>
<template>
    <Layout title="Stockcard" :loading="loading">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form @submit.prevent="submit">
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis"
                            >
                                Code
                            </div>
                            <v-autocomplete
                                v-model="form.code"
                                v-model:search="searchCode"
                                :items="products"
                                item-title="code"
                                item-value="id"
                                placeholder="Select..."
                                variant="outlined"
                                menu-icon=""
                            >
                                <template v-slot:no-data>
                                    <v-container fluid>
                                        <span v-if="preload">Loading...</span>
                                        <span v-else>No results found</span>
                                    </v-container>
                                </template>
                            </v-autocomplete>
                        </v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Lot/Batch No.
                            </div>
                            <v-autocomplete
                                v-model="form.lot"
                                v-model:search="searchLot"
                                :items="lots"
                                item-title="lot"
                                item-value="id"
                                placeholder="Select..."
                                variant="outlined"
                                menu-icon=""
                            >
                            <template v-slot:no-data>
                                <v-container fluid>
                                    <span v-if="preload">Loading...</span>
                                    <span v-else>No results found</span>
                                </v-container>
                            </template>
                            </v-autocomplete>
                        </v-col>
                    </v-row>

                    <v-row no-gutters>
                        <v-col cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Description
                            </div>
                            <v-text-field
                                placeholder="Description"
                                variant="outlined"
                                required
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row no-gutters>
                        <v-col cols="12" sm="4">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Filter By
                            </div>
                            <v-select
                                placeholder="Select..."
                                :items="[
                                    'Date of Posting',
                                    'Date of Transaction',
                                ]"
                                variant="outlined"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date From
                            </div>
                            <v-date-input
                                placeholder="Date from"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                :class="$vuetify.display.mobile ? 'mx-0' : 'mx-2'"
                            ></v-date-input>
                        </v-col>
                        <v-col cols="12" sm="4">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date to
                            </div>
                            <v-date-input
                                placeholder="Date to"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                            ></v-date-input>
                        </v-col>
                    </v-row>

                    <v-row justify="center">
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn
                                type="submit"
                                class="me-4"
                                color="green"
                                :loading="loading"
                                size="large"
                            >
                                Generate PDF
                            </v-btn>
                        </v-col>
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn
                                class="me-4"
                                color="primary"
                                :loading="loading"
                                size="large"
                            >
                                Export to Excel
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
