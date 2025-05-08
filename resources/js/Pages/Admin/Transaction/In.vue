<script setup>
import { router, useForm } from "@inertiajs/vue3";
import Layout from "../../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";

import { ref, watch, reactive, shallowRef } from "vue";

const dialog = shallowRef(false); // Dialog for adding new lot

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

const expand = ref(true);
const expand2 = ref(true);

let lot = ref("");
let code = ref("");

const form = reactive({
    code: "",
});

const codeInput = () => {
    if (code.value === "") {
        expand["value"] = false;
        lot["value"] = "";
    }
};

const lotInput = () => {
    if (lot.value === "") {
        expand2["value"] = false;
    }
};

function codeFilter() {
    router.get(
        "/in",
        {
            code: form.code,
        },
        {
            preserveState: true,
        }
    );
}

</script>
<template>
    <Layout title="In Transaction">
        <v-tabs
            v-model="tab"
            align-tabs="center"
            color="primary"
            bg-color="grey-lighten-4"
        >
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
                                <div
                                    class="text-subtitle-1 text-medium-emphasis mx-2"
                                >
                                    Code
                                </div>
                                <v-autocomplete
                                    v-model:search="form.code"
                                    :items="['Apple', 'Banana', 'Cherry']"
                                    class="mx-2"
                                    placeholder="Select..."
                                    variant="outlined"
                                    menu-icon=""
                                    @update:modelValue="expand = true"
                                    @input="codeInput"
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
                                        class="mx-2"
                                        placeholder="Total Items"
                                        prepend-inner-icon=""
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" class="mb-5">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Lot/Batch No.
                                    </div>
                                    <v-row justify="start">
                                        <!-- Autocomplete -->
                                        <v-col cols="12" lg="10">
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
                                                placeholder="Select..."
                                                variant="outlined"
                                                hide-details
                                                menu-icon=""
                                                @update:modelValue="
                                                    expand2 = true
                                                "
                                                @input="lotInput"
                                                class="mx-2"
                                            ></v-autocomplete>
                                        </v-col>

                                        <!-- Button -->
                                        <v-col
                                            cols="12"
                                            lg="2"
                                            class="d-flex align-center"
                                        >
                                            <v-dialog
                                                v-model="dialog"
                                                max-width="600"
                                                persistent
                                            >
                                                <template
                                                    v-slot:activator="{
                                                        props: activatorProps,
                                                    }"
                                                >
                                                    <v-btn
                                                        block
                                                        color="green"
                                                        prepend-icon="mdi-plus"
                                                        v-bind="activatorProps"
                                                        class="mx-n2"
                                                    >
                                                        Add New Lot
                                                    </v-btn>
                                                </template>

                                                <v-card
                                                    prepend-icon="mdi-account"
                                                    title="Add New Lot"
                                                >
                                                    <v-card-text>
                                                        <v-row dense>

                                                            <v-col
                                                                cols="12"
                                                            >
                                                                <v-text-field
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
                                                            @click="
                                                                dialog = false
                                                            "
                                                        ></v-btn>

                                                        <v-btn
                                                        color="green"
                                                            text="Save"
                                                            variant="tonal"
                                                            @click="
                                                                dialog = false
                                                            "
                                                        ></v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-dialog>
                                        </v-col>
                                    </v-row>
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

                                    <v-date-input
                                        placeholder="Date from"
                                        prepend-icon=""
                                        prepend-inner-icon="$calendar"
                                        variant="outlined"
                                        class="mx-2"
                                    ></v-date-input>
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

                                    <v-date-input
                                        placeholder="Date from"
                                        prepend-icon=""
                                        prepend-inner-icon="$calendar"
                                        variant="outlined"
                                        class="mx-2"
                                    ></v-date-input>
                                </v-col>
                                <!-- Date of Posting (Disabled) -->
                                <v-col sm="6" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Date of Posting
                                    </div>

                                    <v-date-input
                                        placeholder="Date from"
                                        prepend-icon=""
                                        prepend-inner-icon="$calendar"
                                        variant="outlined"
                                        class="mx-2"
                                        disabled
                                    ></v-date-input>
                                </v-col>
                                <v-col sm="12" cols="12">
                                    <div
                                        class="text-subtitle-1 text-medium-emphasis mx-2"
                                    >
                                        Remarks
                                    </div>

                                    <v-textarea
                                        clearable
                                        class="mx-2"
                                        placeholder="Write a comment.."
                                        variant="outlined"
                                    ></v-textarea>
                                </v-col>
                            </v-row>
                        </v-expand-transition>

                        <div class="d-flex justify-end" v-if="expand">
                            <v-btn
                                append-icon="mdi-send"
                                color="primary"
                                class="me-2"
                            >
                                Submit
                            </v-btn>
                        </div>
                    </v-form>
                </v-container>
            </v-tabs-window-item>
        </v-tabs-window>
    </Layout>
</template>
