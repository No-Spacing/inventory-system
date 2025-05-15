<script setup>
    import Layout from '../../../Layout/Main.vue'
    import { ref, computed, reactive } from 'vue'

    const result = ref('');
    const selectedFormat = ref('');

    const expandUpload = ref(false)
    const expandCamera = ref(false)

    const barcodeFormats = ref({
    aztec: false,
    code_128: false,
    code_39: false,
    code_93: false,
    codabar: false,
    databar: false,
    databar_expanded: false,
    data_matrix: false,
    dx_film_edge: false,
    ean_13: false,
    ean_8: false,
    itf: false,
    maxi_code: false,
    micro_qr_code: false,
    pdf417: false,
    qr_code: false,
    rm_qr_code: false,
    upc_a: false,
    upc_e: false,
    linear_codes: false,
    matrix_codes: false
    });

    function onDetect(detectedCodes) {
        result.value = JSON.stringify(detectedCodes.map((code) => code.rawValue))
    }

    function switchToUpload(){
        expandCamera.value = false
        expandUpload.value = true
    }
    function switchToCamera(){
        expandCamera.value = true
        expandUpload.value = false
    }
</script>
<template>
    <Layout title="Testing">
        <v-card class="ma-2 pa-2">
            <v-radio-group v-model="selectedFormat">
                <v-row no-gutters>
                    <v-col
                        v-for="(enabled, format) in barcodeFormats"
                        cols="12"
                        sm="4"
                    >
                        <v-sheet class="ma-2 pa-2">
                            <v-radio
                            :key="format"
                            :label="format"
                            :value="format"
                            />
                        </v-sheet>
                    </v-col>
                </v-row>
            </v-radio-group>

            <p>Selected Format: {{ selectedFormat }}</p>
            <v-expand-transition>
                <v-card-text v-show="expandUpload">
                    <div>
                        <qrcode-capture
                        :formats="[selectedFormat]"
                        @detect="onDetect"
                        />

                        <p class="decode-result">
                        Last result: <p>{{ result }}</p>
                        </p>
                    </div>
                </v-card-text>
            </v-expand-transition>
            <v-expand-transition>
                <v-card-text v-show="expandCamera">
                    <p>Selected Format: {{ selectedFormat }}</p>

                    <qrcode-stream
                    :formats="[selectedFormat]"
                    @detect="onDetect"
                    />

                    <p class="decode-result">
                    Last result: <p>{{ result }}</p>
                    </p>
                </v-card-text>
            </v-expand-transition>
            <v-card-actions>
                <v-btn @click="switchToUpload">Upload</v-btn>
                <v-btn @click="switchToCamera">Take a Photo</v-btn>
            </v-card-actions>
        </v-card>
    </Layout>
</template>