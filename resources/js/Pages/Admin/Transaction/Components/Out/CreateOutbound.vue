<script setup>
import { router, usePage, useForm } from "@inertiajs/vue3";
import { watchDebounced } from "@vueuse/core";
import { ref, computed, watch } from "vue";
import { VDateInput } from 'vuetify/labs/VDateInput'
import CurrentTime from "../CurrentTime.vue"
import { errorMessages } from "vue/compiler-sfc";

const page = usePage();

const products = computed(() => page.props.products)

const lots = computed(() => page.props.lots)

const preload = ref(false);

const emit = defineEmits(['loading']);

const dialog = ref(false); // Dialog for adding new lot

const expand = ref(false);

const searchCode = ref();

const searchLot = ref();

const snackbar = ref(false);

const buttonLoading = ref(false);

const form = useForm({
    code: '',
    lot: '',
    serialNo: '',
    quantity: '',
    warehouse: '',
    expirationDate: '',
    transactionDate: new Date().toISOString(),
    reference: '',
    remarks: '',
})

const codeInput = () => {
    if (searchCode.value === "") {
        expand["value"] = false;
    }
};

const data = computed(() => {
    return {
        description: page.props.products[0]?.description || "",
        unit: page.props.products[0]?.unit.unit || "",
        remaining_items: page.props.products[0]?.remaining_items || "",
        product_type: page.props.products[0]?.type || '',
    };
});

watch(() => [searchCode.value, searchLot.value], () => {
    emit('loading', true);
    preload['value'] = true;
    buttonLoading.value = true;
})

watchDebounced(() => [searchCode.value, searchLot.value],
    ([newInput1, newInput2]) => {
        router.get(
            "/out",
            {
                code: newInput1,
                lot: newInput2,
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
    },
    { debounce: 1000, maxWait: 5000 }
);

function submit() {
    form.post('/create-stockcard-out', {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('loading', false);
            snackbar.value = true;
            form.reset();
            expand.value = false;
            router.visit('/out', { preserveState:true, preserveScroll: true });
        },
        onError: () => {
            emit('loading', false);
        }
    })
}

const formLot = useForm({
    lot: null,
})

function addLot() {
    formLot.post('/store-lot', {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('loading', false);
            snackbar.value = true;
            dialog.value = false;
            formLot.reset();
        },
        onError: () => {
            emit('loading', false);
        }
    })
}

const product_type = ref();
watch(() => page.props.products, (newProducts) => {
    product_type.value = newProducts[0]?.type || '';
    if(product_type.value === "Track by Serial"){
        form.quantity = 1;
        form.expirationDate = '';
        form.lot = '';
    }else if (product_type.value === "Track by Lot"){
        form.quantity = '';
        form.serialNo = '';
    }
}, { deep: true });
</script>
<template>
    <v-form @submit.prevent="submit">
        <div class="d-flex justify-center">
            <CurrentTime></CurrentTime> 
        </div>
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
                    :items="products"
                    item-title="code"
                    item-value="id"
                    class="mx-2"
                    placeholder="Select..."
                    variant="outlined"
                    menu-icon=""
                    @update:modelValue="expand = true;"
                    @input="codeInput"
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
                    ></v-text-field>
                </v-col>
               
            </v-row>
        </v-expand-transition>
        <v-expand-transition>
            <v-row no-gutters v-show="data.product_type === 'Track by Serial'">
                <v-col md="12" cols="12">
                    <div
                        class="text-subtitle-1 text-medium-emphasis mx-2"
                    >
                        Serial No.
                    </div>
                    <v-text-field
                        v-model="form.serialNo"
                        class="mx-2"
                        placeholder="Serial No."
                        variant="outlined"
                        required
                    ></v-text-field>
                </v-col>
            </v-row>
        </v-expand-transition>
        <v-expand-transition>
            <v-row no-gutters v-show="data.product_type === 'Track by Lot'">
                <v-col md="6" cols="12">
                    <div
                        class="text-subtitle-1 text-medium-emphasis mx-2"
                    >
                        Expiration Date
                    </div>

                    <v-date-input
                        v-model="form.expirationDate"
                        placeholder="Date from"
                        prepend-icon=""
                        prepend-inner-icon="$calendar"
                        variant="outlined"
                        class="mx-2"
                    ></v-date-input>
                </v-col>
                <v-col md="4" cols="12">
                    <div
                        class="text-subtitle-1 text-medium-emphasis mx-2"
                    >
                        Lot/Batch No.
                    </div>
                        
                    <v-autocomplete
                        v-model="form.lot"
                        v-model:search="searchLot"
                        :items="lots"
                        item-title="lot"
                        item-value="id"
                        class="mx-2"
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
                <!-- Button -->
                <v-col md="2" cols="12">
                    <v-btn
                        :width="$vuetify.display.smAndDown ? '100%' : '90%'"
                        color="green"
                        :prepend-icon="$vuetify.display.smAndDown ? 'mdi-plus': ''"
                        :class="$vuetify.display.smAndDown ? 'mb-6' : 'mx-2 mt-7'"
                        style="height:53px"
                        @click="dialog = true"
                    >
                        Add Lot
                    </v-btn>
                </v-col>
            </v-row>
        </v-expand-transition>

        <v-row no-gutters>
            <!-- Expiration Date -->
            
    
            <!-- Date of Transaction -->
            <v-col sm="6" cols="12">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Date of Transaction
                </div>

                <v-date-input
                    v-model="form.transactionDate"
                    :error-messages="form.errors.transactionDate"
                    input-format="yyyy-mm-dd"
                    placeholder="Date from"
                    prepend-icon=""
                    prepend-inner-icon="$calendar"
                    variant="outlined"
                    class="mx-2"
                ></v-date-input>
            </v-col>
            <v-col sm="6" cols="12" v-if="data.product_type === 'Track by Serial'">
                <div class="text-subtitle-1 text-medium-emphasis mx-2">
                    Quantity
                </div>
                <v-text-field
                    v-model="form.quantity"
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
                    class="mx-2"
                    :error-messages="form.errors.quantity"
                    placeholder="Qty"
                    prepend-inner-icon=""
                    variant="outlined"
                ></v-text-field>
            </v-col>
            <v-col sm="6" cols="6">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Customer Name
                </div>
                <v-text-field
                    v-model="form.reference"
                    class="mx-2"
                    placeholder="Customer Name"
                    prepend-inner-icon=""
                    variant="outlined"
                ></v-text-field>
            </v-col>
            <v-col sm="6" cols="6">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Warehouse
                </div>

                <v-select
                    v-model="form.warehouse"
                    :error-messages="form.errors.warehouse"
                    :items="['SIP', 'Calle']"
                    clearable
                    class="mx-2"
                    variant="outlined"
                    placeholder="Warehouse"
                ></v-select>
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

        <div class="d-flex justify-end" v-if="expand">
            <v-btn
                append-icon="mdi-send"
                color="primary"
                class="me-2"
                type="submit"
                :loading="form.processing || buttonLoading"
            >
                Submit
            </v-btn>
        </div>
    </v-form>
    <v-dialog
        v-model="dialog"
        max-width="600"
        persistent
    >
        <v-form @submit.prevent="addLot">
            <v-card title="Add New Lot">
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12">
                            <v-text-field
                                v-model="formLot.lot"
                                :error-messages="formLot.errors.lot"
                                label="Lot/Batch No."
                                required
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        text="Close"
                        variant="plain"
                        @click="dialog = false"
                    ></v-btn>
                    <v-btn
                        color="green"
                        text="Save"
                        variant="tonal"
                        type="submit"
                    ></v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
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