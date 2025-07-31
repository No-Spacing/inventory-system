<script setup>
import Layout from "../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
import { ref, watch, computed, reactive } from "vue";
import { watchDebounced } from "@vueuse/core";
import { useForm, router, usePage } from "@inertiajs/vue3"
import download from 'downloadjs';
import axios from 'axios';

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
    warehouse: null,
    code: null,
    action: null,
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
            "/warehouse-report",
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

function submit(value){
    // loading.value = true
    form.action = value;

    if(value == 'pdf')
    {
        router.post('generate-warehouse-report', form,
        {
            onSuccess: () => {
                loading.value = false
            },
            
            onError: (error) => {
                errorMessages.value = error;
                loading.value = false;
            },
        })  
    }

    if(value == 'excel')
    {
        axios.post('generate-warehouse-report', form, {
            responseType: 'blob',
        })
        .then((response) => {
            const contentType = response.headers['content-type'];
            download(response.data, 'warehouse.xlsx', contentType);
        })
        .catch((error) => {
            console.error('Excel generation failed:', error);
        });
    }
    
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
    <Layout title="Warehouse Report" :loading="loading">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form @submit.prevent>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis"
                            >
                                Warehouse
                            </div>

                            <v-select
                                v-model="form.warehouse"
                                :items="warehouses"
                                :error-messages="errorMessages?.warehouse"
                                item-title="warehouse"
                                item-value="id"
                                variant="outlined"
                                placeholder="Select your warehouse..."
                                clearable
                            >
                            </v-select>
                        </v-col>
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
                                @click="submit('pdf')"
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
                                @click="submit('excel')"
                                type="submit"
                                class="me-4"
                                color="primary"
                                :loading="loading"
                                size="large"
                            >
                                Generate Excel
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
