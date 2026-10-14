<script setup>
import Layout from "../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
import { ref, watch, computed, reactive } from "vue";
import { tryOnBeforeUnmount, watchDebounced } from "@vueuse/core";
import { useForm, router, usePage } from "@inertiajs/vue3"
import download from 'downloadjs';
import axios from 'axios';
import { useRules } from 'vuetify/labs/rules'

defineProps({
    stockcards: Object,
    products: Object,
    serials: Object,
    lots: Object,
    warehouses: Object,
})

const rules = useRules();

const selectedProduct = ref();
const selectedLot = ref();

const loading = ref(false);
const preload = ref(false);

const searchCode = ref('');

const errorMessages = ref();

const showDescription = ref(false);

const showProductType = ref(false);

const form = reactive({
    warehouse: null,
    code: null,
    lot: null,
    serial: null,
    from: null,
    to: null,
    action: null,
})

const data = computed(() => {
    return {
        description: selectedProduct?.value?.description,
        lots: selectedProduct?.value?.lots
    };
});

function onSearchUpdate()
{
    loading.value = true;
    selectedLot.value = null;
}

function searchUpdate()
{
   showDescription.value = true; 
}

watchDebounced(() => searchCode.value, (query) =>{
    router.get('/stockcard',
        { code: query },
        {
            preserveState:true,
            onSuccess: () => {
                loading.value = false;
                if(selectedProduct.value?.type === "Track by Lot")
                {
                    showProductType.value = true;
                }
            }
        }
    )
},{debounce: 1000})

function submit(value){
    form.code = selectedProduct.value.id;
    form.lot = selectedLot.value.id;
    loading.value = true;
    form.action = value;
    if(value == "pdf")
    {
        axios.post('generate-stockcard-report', form, {
            responseType: 'blob',
        })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
            window.open(fileURL); // Opens in a new tab
            loading.value = false
        })
        .catch((error) => {
            console.error('PDF generation failed:', error);
            loading.value = false
        });
    }
    if(value == "excel")
    {
        axios.post('generate-stockcard-report', form, {
            responseType: 'blob',
        })
        .then((response) => {
            const contentType = response.headers['content-type'];
            download(response.data, 'stockcard.xlsx', contentType);
            loading.value = false
        })
        .catch((error) => {
            console.error('Excel generation failed:', error.response);
            loading.value = false
        });
    }
}
</script>
<template>
    <Layout title="Stocks" :loading="loading">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form validate-on="submit" @submit.prevent>
                    <v-row no-gutters>
                        <v-col cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Warehouse
                            </div>
                            <v-select
                                v-model="form.warehouse"
                                :items="warehouses"
                                item-title="warehouse"
                                item-value="id"
                                variant="outlined"
                                placeholder="Select your warehouse..."
                                clearable
                                :rules="[rules.required()]"
                            ></v-select>
                        </v-col>
                        <v-col cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis">
                                Code
                            </div>
                            <v-autocomplete
                                v-model="selectedProduct"
                                v-model:search="searchCode"
                                :items="products"
                                item-title="code"
                                return-object
                                item-value="id"
                                variant="outlined"
                                @update:search="onSearchUpdate"
                                @update:model-value="searchUpdate"
                                :rules="[rules.required()]"
                            >
                                <template v-slot:no-data>
                                    <v-container fluid>
                                        <span v-if="loadingZ">Loading...</span>
                                        <span v-else>No results found</span>
                                    </v-container>
                                </template>
                            </v-autocomplete>  
                        </v-col>
                    </v-row>
                    <v-expand-transition>
                        <v-row no-gutters v-show="showDescription">
                            <v-col cols="12">
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
                        </v-row>
                    </v-expand-transition>
                    <v-expand-transition>
                        <v-row no-gutters v-show="showDescription">
                            <v-col cols="12" v-if="showProductType">
                                <div class="text-subtitle-1 text-medium-emphasis">
                                    Lot/Batch No.
                                </div>
                                <v-select
                                    v-model="selectedLot"
                                    :items="data.lots"
                                    item-title="lot"
                                    variant="outlined"
                                    placeholder="Select your warehouse..."
                                    return-object
                                    :rules="[rules.required()]"
                                    clearable
                                ></v-select>
                            </v-col>
                            <v-col cols="12" v-if="!showProductType">
                                <div class="text-subtitle-1 text-medium-emphasis">
                                    Serial No.
                                </div>
                                <v-autocomplete
                                    v-model="form.serial"
                                    :error-messages="errorMessages?.serial"
                                    :items="serials"
                                    item-title="serial_no"
                                    item-value="id"
                                    placeholder="Select..."
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
                        </v-row>
                    </v-expand-transition>
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
                                :rules="[rules.required()]"
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
                                :rules="[rules.required()]"
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
                                @click="submit('pdf')"
                            >
                                Generate PDF
                            </v-btn>
                            <v-btn
                                type="submit"
                                class="me-4"
                                color="primary"
                                :loading="loading"
                                size="large"
                                @click="submit('excel')"
                            >
                                Export Excel
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
