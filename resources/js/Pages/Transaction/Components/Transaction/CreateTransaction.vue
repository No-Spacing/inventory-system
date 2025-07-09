<script setup>
import { router, usePage, useForm, Link } from "@inertiajs/vue3";
import { watchDebounced } from "@vueuse/core";
import { ref, computed, watch } from "vue";
import { VDateInput } from 'vuetify/labs/VDateInput'
import CurrentTime from "../CurrentTime.vue"
import { useDate } from 'vuetify'

const adapter = useDate()

const page = usePage();

const products = computed(() => page.props.products)

const lots = computed(() => page.props.lots)

const preload = ref(false);

const emit = defineEmits(['loading']);

const dialog = ref(false); // Dialog for adding new lot

const transferDialog = ref(false);

const expand = ref(false);
const expandLot = ref(false);
const expandSerial = ref(false);
const showMultipleSerial = ref(false);

const searchCode = ref();

const searchLot = ref();

const searchSerial = ref();

const snackbar = ref(false);

const errorSnackbar = ref(false);

const message = ref();

const buttonLoading = ref(false);

const product_type = ref();

const form = useForm({
    code: '',
    product_type: '',
    lot: '',
    serial_no: '',
    serialStartingNo: '',
    serialEndingNo: '',
    quantity: '',
    warehouse: '',
    transactionDate: new Date().toISOString(),
    reference: '',
    remarks: '',
    document_no: '',
    action: '',
    transferWarehouse: null,
})

const codeInput = () => {
    if (searchCode.value === "") {
        expand["value"] = false;
    } 
};

const lotInput = () => {
    if (searchLot.value === "") {
        expandLot["value"] = false;
    } 
};

const serialInput = () => {
    if (searchSerial.value === "") {
        expandSerial["value"] = false;
    } 
};

const data = computed(() => {
    return {
        description: page.props.products[0]?.description,
        unit: page.props.products[0]?.unit.unit,
        remaining_items: page.props.remainingItems,
        product_type: page.props.products[0]?.type,
        totalSerialItems: page.props.products[0]?.total_items,
    };
});

const lotData = computed(() => {
    return {
        expiration_date: page.props.lots[0]?.expiration_date,
        totalLotItems: page.props.remainingLotItems,
    };
});

const serialData = computed(() => {
    return {
        totalSerialItems: page.props.remainingSerialItems,
    };
});

watch(() => [searchCode.value, searchLot.value, searchSerial.value], () => {
    emit('loading', true);
    preload['value'] = true;
    buttonLoading.value = true;
})

watchDebounced(() => [searchCode.value, searchLot.value, searchSerial.value, form.warehouse],
    ([newInput1, newInput2, newInput3, newInput4]) => {
        router.get(
            "/transaction",
            {
                code: newInput1,
                lot: newInput2,
                serial: newInput3,
                warehouse: newInput4,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    emit('loading', false);
                    preload['value'] = false;
                    buttonLoading.value = false;
                },
            }
        );
    },{ debounce: 500, maxWait: 5000 }
);

function submitTransaction (value) {
    form.action = value
    form.post('/stockcard-data', {
        preserveState: true,
        onSuccess: () => {
            emit('loading', false);
            snackbar.value = true;
            message.value = page.props.flash.message
            expand.value = false;
            transferDialog.value = false
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

watch(() => page.props.products, (newProducts) => {
    product_type.value = newProducts[0]?.type || '';
    if(product_type.value === "Track by Serial"){
        form.quantity = 1;
        form.lot = '';
        form.product_type = product_type.value;
    }else if (product_type.value === "Track by Lot"){
        form.quantity = '';
        form.serial_no = '';
        form.product_type = product_type.value;
        form.serialStartingNo = '';
        form.serialEndingNo = '';
    }
}, { deep: true });

</script> 
<template>
    <v-form @submit.prevent>
        <div class="d-flex justify-center">
            <CurrentTime></CurrentTime> 
        </div>
     
            <div
                class="text-subtitle-1 text-medium-emphasis mx-2"
            >
                Warehouse
            </div>

            <v-select
                v-model="form.warehouse"
                :error-messages="form.errors.warehouse"
                :items="page.props.warehouses"
                item-title="warehouse"
                item-value="id"
                class="mx-2"
                variant="outlined"
                placeholder="Select your warehouse location..."
                @update:modelValue="preload = true;"
                clearable
            >
                <template #append-inner>
                    <v-progress-circular
                    v-if="preload"
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
                            v-model="form.code"
                            v-model:search="searchCode"
                            :error-messages="form.errors.code"
                            :items="products"
                            item-title="code"
                            class="mx-2"
                            placeholder="Select..."
                            variant="outlined"
                            menu-icon=""
                            @update:modelValue="expand = true; form.lot = null"
                            @input="codeInput"
                        >
                            <template v-slot:no-data>
                                <v-container fluid>
                                    <span v-if="preload">Loading...</span>
                                    <span v-else>No results found</span>
                                </v-container>
                            </template>
                            <template #append-inner>
                                <v-progress-circular
                                v-if="preload"
                                indeterminate
                                size="20"
                                color="primary"
                                />
                            </template>
                        </v-autocomplete>
                    </v-col>
                </v-row>    
                <v-expand-transition>
                    <v-row no-gutters v-show="expand">
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
                                required
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
        <v-card variant="outlined" v-if="expand && data.product_type === 'Track by Serial'" class="my-5" :disabled="preload">
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
                            v-model="form.serial_no"
                            v-model:search="searchSerial"
                            :error-messages="form.errors.serial_no"
                            :items="page.props.serials"
                            item-title="serial_no"
                            class="mx-2"
                            placeholder="Select..."
                            variant="outlined"
                            menu-icon=""
                            @update:modelValue="expandSerial = true;"
                            @input="serialInput"
                        >
                            <template v-slot:no-data>
                                <v-container fluid>
                                    <span v-if="preload">Loading...</span>
                                    <span v-else>No results found</span>
                                </v-container>
                            </template>

                            <template #append-inner>
                                <v-progress-circular
                                v-if="preload"
                                indeterminate
                                size="20"
                                color="primary"
                                />
                            </template>
                        </v-autocomplete>
                    </v-col>
                    <v-col md="6" cols="12" v-show="expandSerial">
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
                            required
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
                            required
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
                            required
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-row>
        </v-card>
        </v-expand-transition>
        <v-expand-transition class="my-5">
            <v-card variant="outlined" title="Batch/Lot No." :disabled="preload" v-if="expand === true && data.product_type === 'Track by Lot'">
                <v-row no-gutters class="pa-2 ma-2">
                    <v-col md="4" cols="12">
                        <div
                            class="text-subtitle-1 text-medium-emphasis mx-2"
                        >
                            Lot/Batch No.
                        </div> 
                        <v-autocomplete
                            v-model="form.lot"
                            v-model:search="searchLot"
                            :error-messages="form.errors.lot"
                            :items="page.props.lots"
                            item-title="lot"
                            class="mx-2"
                            placeholder="Select..."
                            variant="outlined"
                            menu-icon=""
                            @update:modelValue="expandLot = true;"
                            @input="lotInput"
                        >
                            <template v-slot:no-data>
                                <v-container fluid>
                                    <span v-if="preload">Loading...</span>
                                    <span v-else>No results found</span>
                                </v-container>
                            </template>

                            <template #append-inner>
                                <v-progress-circular
                                v-if="preload"
                                indeterminate
                                size="20"
                                color="primary"
                                />
                            </template>
                            
                        </v-autocomplete>
                    </v-col>
                    <v-col md="4" cols="12" v-show="expandLot">
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
                    <v-col md="4" cols="12" v-show="expandLot">
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
            <v-col sm="6" cols="12" v-if="data.product_type === 'Track by Serial'">
                <div class="text-subtitle-1 text-medium-emphasis mx-2">
                    Quantity
                </div>
                <v-text-field
                    v-model="form.quantity"
                    type="number"
                    class="mx-2"
                    :error-messages="form.errors.quantity"
                    placeholder="Qty"
                    prepend-inner-icon=""
                    variant="outlined"
                    readonly
                ></v-text-field>
            </v-col>
            <v-col sm="6" cols="12" v-if="data.product_type === 'Track by Lot'">
                <div class="text-subtitle-1 text-medium-emphasis mx-2">
                    Quantity
                </div>
                <v-text-field
                    v-model="form.quantity"
                    type="number"
                    class="mx-2"
                    :error-messages="form.errors.quantity"
                    placeholder="Qty"
                    prepend-inner-icon=""
                    variant="outlined"
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
            <!-- <v-col sm="4" cols="6">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Warehouse
                </div>

                <v-select
                    v-model="form.warehouse"
                    :error-messages="form.errors.warehouse"
                    :items="page.props.warehouses"
                    item-title="warehouse"
                    item-value="id"
                    clearable
                    class="mx-2"
                    variant="outlined"
                    placeholder="Select warehouse transfer..."
                ></v-select>
            </v-col> -->
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
                        @click="submitTransaction('inbound')"
                        
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
                        :disabled="showMultipleSerial"
                        @click="submitTransaction('outbound')"
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
                        :disabled="showMultipleSerial"
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
                                        @click="submitTransaction('transfer')"
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
        {{ message }}
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