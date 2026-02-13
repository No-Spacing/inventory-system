<script setup>
import { router, usePage, useForm, Link } from "@inertiajs/vue3";
import { watchDebounced } from "@vueuse/core";
import { ref, computed, watch, reactive } from "vue";
import { VDateInput } from 'vuetify/labs/VDateInput'
import CurrentTime from "../CurrentTime.vue"
import { useRules } from 'vuetify/labs/rules'

// const rules = useRules();
const page = usePage();

const props = defineProps({
    warehouses: {
        type: Object,
        required: true
    },

    products: {
        type: Object,
        required: true
    },

    lots: {
        type: Object,
        required: true
    },

    serials: {
        type: Object,
        required: true
    }
})

const formRef = ref(null)

const transferDialog = ref(false);

const expandProductDetails = ref(false);
const expandLotDetails = ref(false);
const expandSerial = ref(false);
const showMultipleSerial = ref(false);

const emit = defineEmits(['loading']);

const snackbar = ref(false);

const errorSnackbar = ref(false);

const showProductType = ref(false);

const buttonLoading = ref(false);

const rules = useRules();

const selectedProduct = ref();
const selectedLot = ref();
const selectedSerial = ref();

const codeValue = ref('');
const lotName = ref('');
const serialNo = ref('');

const warehouseValue = ref('');
const productValue = ref('');
const lotValue = ref('');
const serialValue = ref('');

const searchCode = ref('');
const searchLot = ref('');
const searchSerial = ref();

const loading = ref(false);

const form = useForm({
    warehouse_id: null,
    product_id: null,
    product_type: null,
    lot_id: null,
    serial_id: null,
    serial_no: null,
    serialStartingNo: null,
    serialEndingNo: null,
    quantity: null,
    transactionDate: null,
    document_no: null,
    reference: null,
    remarks: null,
    transferWarehouse: null,
    action: null,
})

const data = computed(() => {
    return {
        description: selectedProduct?.value?.description,
        lots: selectedProduct?.value?.lots,
        unit: selectedProduct?.value?.unit.unit,
        product_type: selectedProduct?.value?.type,
        remaining_items: page.props?.remainingItems
    };
});

const lotData = computed(() => {
    return {
        expiration_date: selectedLot?.value?.expiration_date,
        totalLotItems: page.props?.remainingLotItems,
    };
});

const serialData = computed(() => {
    return {
        totalSerialItems: page.props?.remainingSerialItems,
    };
});

function onSearchProduct () {
    loading.value = true;
    selectedLot.value = null;
}

function onSearchLot () {
    loading.value = true;
}

function onSearchSerial () {
    loading.value = true;
}

watch(() => [showMultipleSerial.value], ([value]) => {
    if(value){
        selectedSerial.value = null
        serialValue.value = null;
        form.serial_id = null;   
    }else{
        form.serial_no = null;
        form.serialStartingNo = null;
        form.serialEndingNo = null;
    }
})

watchDebounced(() => [searchCode.value, searchLot.value, searchSerial.value], ([searchCodeValue, searchLotValue, searchSerialValue]) => {
    codeValue.value = searchCodeValue;
    lotName.value = searchLotValue;
    serialNo.value = searchSerialValue
    fetchTransaction();
}, { debounce: 1000 })

function updateWarehouseValue(value) {
    warehouseValue.value = value
    fetchTransaction();
}

function updateProductValue(value) {
    productValue.value = value?.id
    expandProductDetails.value = true;
    if (selectedProduct?.value?.type === 'Track by Serial') {
        form.quantity = 1;
        lotValue.value = null;
        form.lot_id = null;
    }else{
        form.quantity = 0;
        serialValue.value = null;
        form.serial_id = null;
        form.serial_no = null;
        form.serialStartingNo = null;
        form.serialEndingNo = null;
    }

    fetchTransaction();
}

function updateLotValue(value) {
    lotValue.value = value?.id
    if(selectedProduct.value)
    fetchTransaction();
}

function updateSerialValue(value) {
    serialValue.value = value?.id;
    fetchTransaction();
}

function fetchTransaction() {
    router.get('/transaction',
        { 
            warehouse_id: warehouseValue.value,
            code: codeValue.value,
            product_id: productValue.value,
            lotName: lotName.value,
            lot_id: lotValue.value,
            serial_no: serialNo.value,
            serial_id: serialValue.value,
        },
        {
            preserveState:true,
            preserveScroll: true,
            preserveUrl: true,
            onSuccess: () => {
                loading.value = false
                if(selectedProduct.value?.type === "Track by Lot") {
                    showProductType.value = true
                }
            }
        }
    )
}

function submitInbound (value) {
    form.action = value;
    form.product_id = productValue.value;
    form.lot_id = lotValue.value;
    form.product_type = selectedProduct?.value?.type;
    form.serial_id = serialValue.value;
    form.post('/submit-inbound', {
        preserveState: true,
        onSuccess: () => {
            emit('loading', false);
            snackbar.value = true;
            expandLotDetails.value = false;
            transferDialog.value = false
            selectedProduct.value = null;
            if(selectedProduct?.value?.type === "Track by Lot"){
                selectedLot.value = null;
            }else{
                selectedSerial.value = null;
            }
            form.reset();
        },
        onError: () => {
            emit('loading', false);
            if(page.props.errors.message){
                errorSnackbar.value = true;
            }
        }
    })
}

function submitOutbound (value) {
    form.action = value;
    form.product_id = productValue.value;
    form.lot_id = lotValue.value;
    form.product_type = selectedProduct?.value?.type;
    form.serial_id = serialValue.value;
    form.post('/submit-outbound', {
        preserveState: true,
        onSuccess: () => {
            emit('loading', false);
            snackbar.value = true;
            expandLotDetails.value = false;
            transferDialog.value = false
            selectedProduct.value = null;
            if(selectedProduct?.value?.type === "Track by Lot"){
                selectedLot.value = null;
            }else{
                selectedSerial.value = null;
            }
            form.reset();
        },
        onError: () => {
            emit('loading', false);
            if(page.props.errors.message){
                errorSnackbar.value = true;
            }
        }
    })
}

function submitTransfer (value) {
    form.action = value;
    form.product_id = productValue.value;
    form.lot_id = lotValue.value;
    form.product_type = selectedProduct?.value?.type;
    form.serial_id = serialValue.value;
    form.post('/submit-transfer', {
        preserveState: true,
        onSuccess: () => {
            emit('loading', false);
            snackbar.value = true;
            expandLotDetails.value = false;
            transferDialog.value = false
            selectedProduct.value = null;
            if(selectedProduct?.value?.type === "Track by Lot"){
                selectedLot.value = null;
            }else{
                selectedSerial.value = null;
            }
            form.reset();
        },
        onError: () => {
            emit('loading', false);
            if(page.props.errors.message){
                errorSnackbar.value = true;
            }
        }
    })
}
</script> 
<template>
    <v-form @submit.prevent @keypress.enter.prevent ref="formRef">
        <div class="d-flex justify-center">
            <CurrentTime></CurrentTime> 
        </div>
        <div
            class="text-subtitle-1 text-medium-emphasis mx-2"
        >
            Warehouse
        </div>
        <v-select
            v-model="form.warehouse_id"
            :items="props.warehouses"
            item-title="warehouse"
            item-value="id"
            class="mx-2"
            variant="outlined"
            placeholder="Select your warehouse location..."
            @update:modelValue="updateWarehouseValue"
            clearable
        >
            <template #append-inner>
                <v-progress-circular
                v-if="loading"
                indeterminate
                size="20"
                color="primary"
                />
            </template>
        </v-select>
 
        <v-card variant="outlined" title="Product Details" class="my-5">
            <v-container>
                <v-row no-gutters>
                    <v-col cols="12">
                        <div
                            class="text-subtitle-1 text-medium-emphasis mx-2"
                        >
                            Code
                        </div>
                        <v-autocomplete
                            v-model="selectedProduct"
                            v-model:search="searchCode"
                            :items="props.products"
                            item-title="code"
                            return-object
                            item-value="id"
                            variant="outlined"
                            menu-icon=""
                            @update:search="onSearchProduct"
                            @update:modelValue="updateProductValue"
                            :rules="[rules.required()]"
                        >
                            <template #append-inner>
                                <v-progress-circular
                                    v-if="loading"
                                    indeterminate
                                    size="20"
                                    color="primary"
                                />
                            </template>
                            <template #no-data>
                                <v-container fluid>
                                    <span v-if="loading">Searching...</span>
                                    <span v-else-if="searchCode">No results found.</span>   
                                    <!-- nothing shown if searchCode is empty -->
                                </v-container>
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>    
                <v-expand-transition>
                    <v-row no-gutters v-show="expandProductDetails">
                        <v-col sm="3" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Description
                            </div>
                            <v-text-field
                                v-model="data.description"
                                class="mx-2"
                                placeholder="Description"
                                variant="outlined"
                                readonly
                                disabled
                            ></v-text-field>
                        </v-col>
                        <v-col sm="3" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Unit
                            </div>
                            <v-text-field
                                v-model="data.unit"
                                class="mx-2"
                                placeholder="Unit"
                                prepend-inner-icon=""
                                variant="outlined"
                                readonly
                                disabled
                            ></v-text-field>
                        </v-col>
                        <v-col sm="3" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Remaining Items
                            </div>
                            <v-text-field
                                v-model="data.remaining_items"
                                class="mx-2"
                                placeholder="Total Items"
                                prepend-inner-icon=""
                                variant="outlined"
                                readonly
                                disabled
                            ></v-text-field>
                        </v-col>
                        <v-col sm="3" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Product Type
                            </div>
                            <v-text-field
                                v-model="data.product_type"
                                class="mx-2"
                                placeholder="Total Items"
                                prepend-inner-icon=""
                                variant="outlined"
                                readonly
                                disabled
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-expand-transition>
            </v-container>
        </v-card>
        <v-expand-transition>
            <v-card variant="outlined" v-if="expandProductDetails && data.product_type === 'Track by Serial'" class="my-5">
                <v-card-title>
                    <v-row no-gutters>
                        <v-col>
                            Serial No.
                            <v-checkbox v-model="showMultipleSerial" color="primary" label="Insert Multiple Serials No."></v-checkbox>
                        </v-col>
                    </v-row>
                </v-card-title>
                <v-row no-gutters class="ma-2 pa-2"> 
                    <v-row no-gutters v-if="showMultipleSerial === false">
                        <v-col md="6" cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis mx-2">
                                Serial No.
                            </div>
                            <v-autocomplete
                                v-model="selectedSerial"
                                v-model:search="searchSerial"
                                :items="props.serials"
                                item-title="serial_no"
                                return-object
                                item-value="id"
                                variant="outlined"
                                menu-icon=""
                                @update:search="onSearchSerial"
                                @update:modelValue="updateSerialValue"
                                :rules="[rules.required()]"
                                class="mx-2"
                            >
                                <template v-slot:no-data>
                                    <v-container fluid>
                                        <span v-if="loading">Loading...</span>
                                        <span v-else>No results found</span>
                                    </v-container>
                                </template>

                                <template #append-inner>
                                    <v-progress-circular
                                    v-if="loading"
                                    indeterminate
                                    size="20"
                                    color="primary"
                                    />
                                </template>
                            </v-autocomplete>
                        </v-col>
                        <v-col md="6" cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis mx-2">
                                Total Items in Serial
                            </div>
                            <v-text-field
                                v-model="serialData.totalSerialItems"
                                type="number"
                                class="mx-2"
                                variant="outlined"
                                readonly
                                disabled
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row no-gutters v-if="showMultipleSerial === true">
                        <v-col md="4" cols="12">
                            <div class="text-subtitle-1 text-medium-emphasis mx-2">
                                Model Series.
                            </div>
                            <v-text-field
                                v-model="form.serial_no"
                                :error-messages="form.errors.serial_no"
                                class="mx-2"
                                placeholder="Serial No."
                                variant="outlined"
                                counter
                            ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Series of Starting No.
                            </div>
                            <v-text-field
                                v-model="form.serialStartingNo"
                                :error-messages="form.errors.serialStartingNo"
                                type="number"
                                class="mx-2"
                                variant="outlined"
                            ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Series of Ending No.
                            </div>
                            <v-text-field
                                v-model="form.serialEndingNo"
                                :error-messages="form.errors.serialEndingNo"
                                type="number"
                                class="mx-2"
                                variant="outlined"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-row>
            </v-card>
        </v-expand-transition>
        <v-expand-transition class="my-5">
            <v-card variant="outlined" title="Batch/Lot No." v-if="expandProductDetails === true && data.product_type === 'Track by Lot'">
                <v-row no-gutters class="pa-2 ma-2">
                    <v-col md="4" cols="12">
                        <div
                            class="text-subtitle-1 text-medium-emphasis mx-2"
                        >
                            Lot/Batch No.
                        </div> 
                        <v-autocomplete
                            v-model="selectedLot"
                            v-model:search="searchLot"
                            :items="props.lots"
                            item-title="lot"
                            class="mx-2"
                            placeholder="Select..."
                            variant="outlined"
                            menu-icon=""
                            @update:modelValue="updateLotValue"
                            @update:search="onSearchLot"
                            return-object
                            :rules="[rules.required()]"
                        >
                            <template #append-inner>
                                <v-progress-circular
                                    v-if="loading"
                                    indeterminate
                                    size="20"
                                    color="primary"
                                />
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col md="4" cols="12">
                        <div
                            class="text-subtitle-1 text-medium-emphasis mx-2"
                        >
                            Total Items in Lot
                        </div>

                        <v-text-field
                            v-model="lotData.totalLotItems"
                            prepend-icon=""
                            variant="outlined"
                            class="mx-2"
                            readonly
                            disabled
                        ></v-text-field>
                    </v-col>
                    <v-col md="4" cols="12">
                        <div
                            class="text-subtitle-1 text-medium-emphasis mx-2"
                        >
                            Expiration Date
                        </div>

                        <v-date-input
                            v-model="lotData.expiration_date"
                            placeholder="Date from"
                            prepend-icon=""
                            prepend-inner-icon="$calendar"
                            variant="outlined"
                            class="mx-2"
                            readonly
                            disabled
                        ></v-date-input>
                    </v-col>
                </v-row>
            </v-card>
        </v-expand-transition>
        <v-row no-gutters>
           <v-col sm="6" cols="12">
            <div class="text-subtitle-1 text-medium-emphasis mx-2">
                Quantity
            </div>
                <v-text-field
                    v-model="form.quantity"
                    type="number"
                    class="mx-2"
                    placeholder="Qty"
                    prepend-inner-icon=""
                    variant="outlined"
                    :readonly="data.product_type === 'Track by Serial'"
                    :error-messages="data.product_type === 'Track by Lot' ? form.errors.quantity : []"
                ></v-text-field>
            </v-col>
            <v-col sm="6" cols="12">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Date of Transaction
                </div>

                <v-date-input
                    v-model="form.transactionDate"
                    input-format="yyyy-mm-dd"
                    placeholder="Date from"
                    prepend-icon=""
                    prepend-inner-icon="$calendar"
                    variant="outlined"
                    class="mx-2"
                ></v-date-input>
            </v-col>

            <v-col sm="6" cols="6">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Customer/Supplier Name
                </div>
                <v-text-field
                    v-model="form.reference"
                    class="mx-2"
                    placeholder="Customer/Supplier Name"
                    prepend-inner-icon=""
                    variant="outlined"
                ></v-text-field>
            </v-col>
            <v-col sm="6" cols="6">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Document No.
                </div>
                <v-text-field
                    v-model="form.document_no"
                    class="mx-2"
                    placeholder="Document No."
                    prepend-inner-icon=""
                    variant="outlined"
                ></v-text-field>
            </v-col>
            <v-col sm="12" cols="12">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Remarks
                </div>

                <v-textarea
                    v-model="form.remarks"
                    clearable
                    class="mx-2"
                    placeholder="Write a comment.."
                    variant="outlined"
                ></v-textarea>
            </v-col>
        </v-row>
        <v-container>
            <v-expand-transition>
                <div class="d-flex justify-center">
                    <v-btn
                        append-icon="mdi-send"
                        color="green"
                        size="large"
                        type="submit"
                        class="mx-4"
                        :loading="form.processing || buttonLoading"
                        @click="submitInbound('inbound')"
                        
                    >
                        Inbound
                    </v-btn>
                    <v-btn
                        append-icon="mdi-send"
                        color="primary"
                        size="large"
                        type="submit"
                        class="mx-4"
                        :loading="form.processing || buttonLoading"
                        @click="submitOutbound('outbound')"
                    >
                        Outbound
                    </v-btn>
                    <v-btn
                        append-icon="mdi-send"
                        color="#039BE5"
                        size="large"
                        class="mx-4"
                        @click="transferDialog = true"
                        :loading="form.processing || buttonLoading"
                    >
                        Transfer
                    </v-btn>

                    <v-dialog v-model="transferDialog" max-width="500">
                        <v-card title="Transfer items" >
                            <v-form @submit.prevent>
                                <v-card-text>
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis"
                                    >
                                        Select the warehouse you want to transfer this items.
                                    </div>
                                    <v-select
                                        v-model="form.transferWarehouse"
                                        :items="page.props.warehouses"
                                        :error-messages="form.errors.transferWarehouse"
                                        item-value="id"
                                        item-title="warehouse"
                                        variant="outlined"
                                        placeholder="Select your warehouse..."
                                    ></v-select>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn
                                        class="ma-2 pa-2"
                                        @click="transferDialog = false"
                                    >
                                        close
                                    </v-btn>

                                    <v-btn 
                                        class="ma-2 pa-2 bg-light-blue-darken-1" 
                                        type="submit" 
                                        :loading="form.processing"
                                        @click="submitTransfer('transfer')"
                                    >
                                        transfer
                                    </v-btn>

                                </v-card-actions>
                            </v-form>
                        </v-card>
                    </v-dialog>
                </div>
            </v-expand-transition>
        </v-container>
    </v-form>

    <v-snackbar
        v-model="snackbar" 
        :timeout="3000"
        color="success"
        variant="elevated"
        location="top"
        class="text"
    >
        {{ page.props.flash.message }}
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