<script setup>
import { router, useForm } from "@inertiajs/vue3";
import Layout from "../../../Layout/Main.vue";

import { ref, watch, reactive } from "vue";

const tab = ref(null);

const props = defineProps({ products: Object, stockcards: Object });

const search = ref("");

const headers = [
    {
        align: "start",
        key: "id",
        sortable: true,
        title: "ID",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "code",
        title: "Code",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "quantity",
        title: "Quantity",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "expdate",
        title: "Expiration Date",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "reference",
        title: "Reference",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        key: "enduser",
        title: "End User",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];

const expand = ref(false);
const expand2 = ref(false);

let lot = ref("");
let code = ref(""); 

const form = reactive({
    code: ''
})

const codeInput = () => {
    if(code.value === ''){
        expand['value'] = false
        lot['value'] = '';
    }    
};

const lotInput = () => {
    if(lot.value === ''){
        expand2['value'] = false
    }    
};

function codeFilter() {
    router.get(
        "/in",
        {
          code: form.code
        },
        {
            preserveState: true,
        }
    );
}


// Expiration Date states
const expirationMenu = ref(false);
const expirationDate = ref(null);
const formattedExpirationDate = ref("");

// Date of Transaction states
const transactionMenu = ref(false);
const transactionDate = ref(null);
const formattedTransactionDate = ref("");

// Format dates (MM/DD/YYYY)
const formatDate = (date) => {
    const d = new Date(date);
    return d.toLocaleDateString();
};

// Handlers for date selection
const onExpirationDateSelected = (val) => {
    formattedExpirationDate.value = formatDate(val);
    expirationMenu.value = false;
};

const onTransactionDateSelected = (val) => {
    formattedTransactionDate.value = formatDate(val);
    transactionMenu.value = false;
};

// Posting Date (Disabled)
const postingDate = new Date(); // or any specific date you want
const formattedPostingDate = ref(postingDate.toLocaleDateString());
</script>

<template>
    <Layout title="In Transaction">
        <v-tabs v-model="tab" align-tabs="center" color="primary" bg-color="grey-lighten-4">
            <v-tab :value="1">
                <v-icon start icon="mdi-format-list-bulleted"></v-icon>
                <p class="font-weight-bold">List</p>
            </v-tab>
            <v-tab :value="2">
                <v-icon start icon="mdi-plus"></v-icon>
                <p class="font-weight-bold">Adjustments</p>
            </v-tab>
        </v-tabs>

        <v-tabs-window v-model="tab">
            <v-tabs-window-item :value="1">
                <v-container fluid>
                    <v-data-table
                        :headers="headers"
                        :search="search"
                        :items="stockcards"
                    ></v-data-table>
                </v-container>    
            </v-tabs-window-item>
            <v-tabs-window-item :value="2">
                <v-container max-width="1500" fluid>
                    <v-form>
                        <v-row no-gutters>
                            <v-col cols="12">
                                <div class="text-subtitle-1 text-medium-emphasis mx-2">
                                    Code
                                </div>
                                <v-autocomplete
                                    v-model:search="form.code"
                                    :items="products"
                                    item-title="code"
                                    class="mx-2"
                                    placeholder="Select..."
                                    variant="outlined"
                                    menu-icon=""
                                    @update:modelValue="expand = true"
                                    @input="codeInput"
                                    @keyup.enter="codeFilter"
                                    :hide-no-data="true"
                                ></v-autocomplete>
                            </v-col>  
                        </v-row>
                        <v-expand-transition>
                            <v-row no-gutters v-show="expand">
                                <v-col sm="4" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Description
                                    </div>
                                    <v-text-field
                                        v-model="products[0].product_desc"
                                        class="mx-2"
                                        placeholder="Description"
                                        variant="outlined"
                                        required
                                    ></v-text-field>
                                </v-col>  
                                <v-col sm="4" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Unit
                                    </div>
                                    <v-text-field
                                        v-model="products[0].unit"
                                        class="mx-2"
                                        placeholder="Unit"
                                        prepend-inner-icon=""
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                                <v-col sm="4" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Total Items
                                    </div>
                                    <v-text-field
                                        v-model="products[0].quantity"
                                        class="mx-2"
                                        placeholder="Total Items"
                                        prepend-inner-icon=""
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Lot/Batch No.
                                    </div>
                                    <v-autocomplete
                                        v-model:search="lot"
                                        :items="[
                                            '1-BR-BD7800',
                                            '9-IC-BABPA1',
                                            '0-PM-104116',
                                            '4-EP-ECGMS3',
                                            'P-MC-000006',
                                            '2-PP-KCPK24',
                                        ]"
                                        class="mx-2"
                                        placeholder="Select..."
                                        variant="outlined"
                                        menu-icon=""
                                        @update:modelValue="expand2 = true"
                                        @input="lotInput"
                                    ></v-autocomplete>
                                </v-col>
                            </v-row>
                        </v-expand-transition>
                        
                        <v-expand-transition>
                            <v-row no-gutters v-show="expand2">
                                <v-col sm="6" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Total Items per Lot
                                    </div>
                                    <v-text-field
                                        class="mx-2"
                                        placeholder="Per Lot"
                                        prepend-inner-icon=""
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                                <!-- Expiration Date -->
                                <v-col sm="6" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Expiration Date
                                    </div>

                                    <v-menu
                                        v-model="expirationMenu"
                                        :close-on-content-click="false"
                                        transition="scale-transition"
                                        offset-y
                                        class="mx-2"
                                    >
                                        <template v-slot:activator="{ props }">
                                            <v-text-field
                                                class="mx-2"
                                                v-model="formattedExpirationDate"
                                                v-bind="props"
                                                readonly
                                                variant="outlined"
                                                placeholder="Select Expiration Date"
                                            ></v-text-field>
                                        </template>

                                        <v-date-picker
                                            v-model="expirationDate"
                                            @update:model-value="onExpirationDateSelected"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-col>
                            </v-row>
                        </v-expand-transition>

                        <v-expand-transition>
                            <v-row no-gutters v-show="expand">
                                <v-col sm="4" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Serial No.
                                    </div>
                                    <v-text-field
                                        class="mx-2"
                                        placeholder="Serial No."
                                        variant="outlined"
                                        required
                                    ></v-text-field>
                                </v-col>
                                <v-col sm="4" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Reference
                                    </div>
                                    <v-text-field
                                        class="mx-2"
                                        placeholder="Ref"
                                        prepend-inner-icon=""
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                                <v-col sm="4" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Quantity
                                    </div>
                                    <v-text-field
                                        class="mx-2"
                                        placeholder="Qty"
                                        prepend-inner-icon=""
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                                <!-- Date of Transaction -->
                                <v-col sm="6" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Date of Transaction
                                    </div>

                                    <v-menu
                                        v-model="transactionMenu"
                                        :close-on-content-click="false"
                                        transition="scale-transition"
                                        offset-y
                                        class="mx-2"
                                    >
                                        <template v-slot:activator="{ props }">
                                            <v-text-field
                                                class="mx-2"
                                                v-model="formattedTransactionDate"
                                                v-bind="props"
                                                readonly
                                                variant="outlined"
                                                placeholder="Select Transaction Date"
                                            ></v-text-field>
                                        </template>

                                        <v-date-picker
                                            v-model="transactionDate"
                                            @update:model-value="
                                                onTransactionDateSelected
                                            "
                                        ></v-date-picker>
                                    </v-menu>
                                </v-col>
                                <!-- Date of Posting (Disabled) -->
                                <v-col sm="6" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Date of Posting
                                    </div>

                                    <v-text-field
                                        class="mx-2"
                                        v-model="formattedPostingDate"
                                        variant="outlined"
                                        readonly
                                        disabled
                                    ></v-text-field>
                                </v-col>
                                <v-col sm="12" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Remarks
                                    </div>

                                    <v-textarea clearable class="mx-2" placeholder="Write a comment.." variant="outlined"></v-textarea>
                                </v-col>
                            </v-row>
                       </v-expand-transition>
                        
                        <div class="d-flex justify-end" v-if="expand">
                            <v-btn append-icon="mdi-send" color="primary" class="me-2">
                                Submit
                            </v-btn>
                        </div>
                    </v-form>
                </v-container>
            </v-tabs-window-item>
        </v-tabs-window>
    </Layout>
</template>
