<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { watchDebounced } from "@vueuse/core";
import { ref, computed, watch, defineEmits } from "vue";
import { VDateInput } from 'vuetify/labs/VDateInput'

const page = usePage();

const products = computed(() => page.props.products)

const emit = defineEmits(['loading']);

const dialog = ref(false); // Dialog for adding new lot

const expand = ref(false);
const expand2 = ref(false);

let lot = ref("");
let code = ref("");

const codeInput = () => {
    emit('loading', true);
    // if (code.value === "") {
    //     expand["value"] = false;
    //     lot["value"] = "";
    // }
};

const lotInput = () => {
    if (lot.value === "") {
        expand2["value"] = false;
    }
};

const data = computed(() => {
    return {
        description: products[0]?.product_desc
            ? products[0].product_desc
            : "",
        unit: products[0]?.unit ? products[0].unit : "",
        quantity: products[0]?.quantity ? products[0].quantity : "",
    };
});

watch(code, () => {
    emit('loading', true);
});

watchDebounced(
    code,
    () => {
        router.get(
            "/in",
            {
                code: code.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    emit('loading', false);
                },
            }
        );
    },
    { debounce: 1000, maxWait: 5000 }
);
</script>
<template>
    <v-form>
        <v-row no-gutters>
            <v-col cols="12">
                <div
                    class="text-subtitle-1 text-medium-emphasis mx-2"
                >
                    Code
                </div>
                <v-autocomplete
                    v-model:search="code"
                    :items="products"
                    item-title="code"
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
                        v-model="data.description"
                        class="mx-2"
                        placeholder="Description"
                        variant="outlined"
                        required
                        readonly
                    ></v-text-field>
                </v-col>
                <v-col sm="4" cols="12">
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
                <v-col sm="4" cols="12">
                    <div
                        class="text-subtitle-1 text-medium-emphasis mx-2"
                    >
                        Total Items
                    </div>
                    <v-text-field
                        v-model="data.quantity"
                        class="mx-2"
                        placeholder="Total Items"
                        prepend-inner-icon=""
                        variant="outlined"
                        readonly
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
                                    '001',
                                    '002',
                                    '003',
                                    '004',
                                    '005',
                                    '006',
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
                                        :width="
                                            $vuetify
                                                .display
                                                .mobile
                                                ? '98.5%'
                                                : '90%'
                                        "
                                        color="green"
                                        :prepend-icon="
                                            $vuetify
                                                .display
                                                .mobile
                                                ? 'mdi-plus'
                                                : ''
                                        "
                                        v-bind="
                                            activatorProps
                                        "
                                        class="mx-2"
                                    >
                                        Add New Lot
                                    </v-btn>
                                </template>

                                <v-card
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
                        readonly
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
</template>