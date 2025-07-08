<script setup>
import Layout from "../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
import { ref, watch, computed, reactive } from "vue";
import { watchDebounced } from "@vueuse/core";
import { useForm, router, usePage } from "@inertiajs/vue3"
import download from 'downloadjs';
import axios from 'axios';
import { nullifyTransforms } from "vuetify/lib/util/animation.mjs";

defineProps({
    warehouses: Object,
    products: Object,
})

const page = usePage();

const loading = ref(false);
const preload = ref(false);

const errorMessages = ref();

const searchCode = ref();
const searchLot = ref();
const showDescription = ref(false);

const form = reactive({
    code: null,
})

const data = computed(() => {
    return {
        description: page.props.products[0]?.description || "",
    };
});

watch(() => searchCode.value, () => {
    loading.value = true
    preload['value'] = true;
})

watchDebounced(() => searchCode.value,
    (value) => {
        router.get(
            "/product-report",
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
        if (value) {
            showDescription.value = true;
        } else{
            showDescription['value'] = false;
        } 
    },
    { debounce: 1000, maxWait: 5000 }
);

function submit(){
    // loading.value = true
    router.post('generate-product-report', form,
        {
            onSuccess: () => {
                loading.value = false
            },
            
            onError: (error) => {
                errorMessages.value = error;
                loading.value = false;
            },
        }
        
    )
    // axios.post('view-stockcard-pdf', form, {
    //     responseType: 'blob',
    // })
    // .then((response) => {
    //     const contentType = response.headers['content-type'];
    //     download(response.data, 'invoice.pdf', contentType);
    // })
    // .catch((error) => {
    //     console.error('PDF generation failed:', error);
    // });
}
</script>
<template>
    <Layout title="Product Report" :loading="loading">
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
                                :error-messages="errorMessages?.code"
                                :items="products"
                                item-title="code"
                                item-value="id"
                                placeholder="Select..."
                                @update:modelValue="form.lot = null;"
                                variant="outlined"
                                menu-icon=""
                                clearable
                            >
                                <template v-slot:no-data>
                                    <v-container fluid>
                                        <span v-if="preload">Loading...</span>
                                        <span v-else>No results found</span>
                                    </v-container>
                                </template>
                            </v-autocomplete>
                        </v-col>
                        <v-expand-transition>
                            <v-col cols="12" v-show="showDescription">
                                <div class="text-subtitle-1 text-medium-emphasis">
                                    Description
                                </div>
                                <v-text-field
                                    v-model="data.description"
                                    placeholder="Description"
                                    variant="outlined"
                                    required
                                    disabled
                                ></v-text-field>
                            </v-col>
                        </v-expand-transition>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12" sm="6">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date From
                            </div>
                            <v-date-input
                                v-model="form.from"
                                :error-messages="errorMessages?.from"
                                placeholder="Date from"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                class="mr-2"
                            ></v-date-input>
                        </v-col>
                        <v-col cols="12" sm="6">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date to
                            </div>
                            <v-date-input
                                v-model="form.to"
                                :error-messages="errorMessages?.to"
                                placeholder="Date to"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                class="ml-2"
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
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
