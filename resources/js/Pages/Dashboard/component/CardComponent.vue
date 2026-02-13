<script setup>
const stats = defineProps({
    productCount: {
        type: Object,
        required: true,
    },
    lotCount: {
        type: Object,
        required: true,
    },
    serialCount: {
        type: Object,
        required: true,
    },
    stockCard: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <v-row>
        <v-col>
            <v-row>
                <v-col cols="12" sm="6">
                    <v-card
                        class="rounded-lg pa-6 d-flex flex-column justify-space-between"
                        elevation="2"
                        height="240px"
                    >
                        <div class="d-flex align-center justify-space-between">
                            <div class="text-grey" style="font-size: larger">
                                In & Out
                            </div>
                            <div
                                class="d-flex align-center justify-center rounded-lg"
                                style="padding: 12px; background-color: #e3f2fd"
                            >
                                <v-icon
                                    icon="mdi mdi-truck-fast-outline"
                                    size="30"
                                    color="#1976D2"
                                    style="opacity: 0.65"
                                />
                            </div>
                        </div>

                        <div>
                            <div style="font-size: xx-large">
                                {{ stats.productCount }}
                            </div>
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6">
                    <v-card
                        class="rounded-lg d-flex flex-column justify-space-between pa-6"
                        elevation="2"
                        height="240px"
                    >
                        <div class="d-flex align-center justify-space-between">
                            <div class="text-grey" style="font-size: larger">
                                Receiving
                            </div>
                            <div
                                class="d-flex align-center justify-center rounded-lg"
                                style="padding: 12px; background-color: #e3f2fd"
                            >
                                <v-icon
                                    icon=" mdi mdi-invoice-text-send-outline"
                                    size="30"
                                    color="#1976D2"
                                    style="opacity: 0.65"
                                />
                            </div>
                        </div>
                        <div>
                            <div style="font-size: xx-large">
                                {{ stats.productCount }}
                            </div>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>
        <v-col>
            <v-row>
                <v-col cols="12" sm="6">
                    <v-card
                        class="rounded-lg d-flex justify-space-between align-center pa-6"
                        elevation="2"
                    >
                        <div>
                            <div class="text-grey" style="font-size: medium">
                                Total Products
                            </div>
                            <div style="font-size: x-large">
                                {{ stats.productCount }}
                            </div>
                        </div>

                        <div
                            class="d-flex align-center justify-center rounded-lg"
                            style="padding: 12px; background-color: #f5f5f5"
                        >
                            <v-icon
                                icon="mdi-package-variant"
                                size="28"
                                color="grey"
                                style="opacity: 0.75"
                            />
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6">
                    <v-card
                        class="rounded-lg d-flex justify-space-between align-center pa-6"
                        elevation="2"
                    >
                        <div>
                            <div class="text-grey" style="font-size: medium">
                                Total Lot No.
                            </div>
                            <div style="font-size: x-large">
                                {{ stats.lotCount }}
                            </div>
                        </div>

                        <div
                            class="d-flex align-center justify-center rounded-lg"
                            style="padding: 12px; background-color: #f3e5f5"
                        >
                            <v-icon
                                icon="mdi-layers-outline"
                                size="28"
                                color="#9C27B0"
                                style="opacity: 0.75"
                            />
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6">
                    <v-card
                        class="rounded-lg d-flex justify-space-between align-center pa-6"
                        elevation="2"
                    >
                        <div>
                            <div class="text-grey" style="font-size: medium">
                                Total Serial
                            </div>
                            <div style="font-size: x-large">
                                {{ stats.serialCount }}
                            </div>
                        </div>

                        <div
                            class="d-flex align-center justify-center rounded-lg"
                            style="padding: 12px; background-color: #e8f5e9"
                        >
                            <v-icon
                                icon="mdi-barcode-scan"
                                size="28"
                                color="#388E3C"
                                style="opacity: 0.75"
                            />
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6">
                    <v-card
                        class="rounded-lg d-flex justify-space-between align-center pa-6"
                        elevation="2"
                    >
                        <div>
                            <div class="text-grey" style="font-size: medium">
                                Total Stockcard
                            </div>
                            <div style="font-size: x-large">
                                {{ stats.stockCard }}
                            </div>
                        </div>

                        <!-- TOTAL STOCKCARD -->
                        <div
                            class="d-flex align-center justify-center rounded-lg"
                            style="padding: 12px; background-color: #fff3e0"
                        >
                            <v-icon
                                icon="mdi-clipboard-text-outline"
                                size="28"
                                color="#F57C00"
                                style="opacity: 0.75"
                            />
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>
