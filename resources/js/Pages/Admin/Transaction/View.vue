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

    const selectedConstraints = ref({ facingMode: 'environment' })
const defaultConstraintOptions = [
  { label: 'rear camera', constraints: { facingMode: 'environment' } },
  { label: 'front camera', constraints: { facingMode: 'user' } }
]
const constraintOptions = ref(defaultConstraintOptions)

async function onCameraReady() {
  // NOTE: on iOS we can't invoke `enumerateDevices` before the user has given
  // camera access permission. `QrcodeStream` internally takes care of
  // requesting the permissions. The `camera-on` event should guarantee that this
  // has happened.
  const devices = await navigator.mediaDevices.enumerateDevices()
  const videoDevices = devices.filter(({ kind }) => kind === 'videoinput')

  constraintOptions.value = [
    ...defaultConstraintOptions,
    ...videoDevices.map(({ deviceId, label }) => ({
      label: `${label} (ID: ${deviceId})`,
      constraints: { deviceId }
    }))
  ]

  error.value = ''
}

/*** track functons ***/

function paintOutline(detectedCodes, ctx) {
  for (const detectedCode of detectedCodes) {
    const [firstPoint, ...otherPoints] = detectedCode.cornerPoints

    ctx.strokeStyle = 'red'

    ctx.beginPath()
    ctx.moveTo(firstPoint.x, firstPoint.y)
    for (const { x, y } of otherPoints) {
      ctx.lineTo(x, y)
    }
    ctx.lineTo(firstPoint.x, firstPoint.y)
    ctx.closePath()
    ctx.stroke()
  }
}
function paintBoundingBox(detectedCodes, ctx) {
  for (const detectedCode of detectedCodes) {
    const {
      boundingBox: { x, y, width, height }
    } = detectedCode

    ctx.lineWidth = 2
    ctx.strokeStyle = '#007bff'
    ctx.strokeRect(x, y, width, height)
  }
}
function paintCenterText(detectedCodes, ctx) {
  for (const detectedCode of detectedCodes) {
    const { boundingBox, rawValue } = detectedCode

    const centerX = boundingBox.x + boundingBox.width / 2
    const centerY = boundingBox.y + boundingBox.height / 2

    const fontSize = Math.max(12, (50 * boundingBox.width) / ctx.canvas.width)

    ctx.font = `bold ${fontSize}px sans-serif`
    ctx.textAlign = 'center'

    ctx.lineWidth = 3
    ctx.strokeStyle = '#35495e'
    ctx.strokeText(detectedCode.rawValue, centerX, centerY)

    ctx.fillStyle = '#5cb984'
    ctx.fillText(rawValue, centerX, centerY)
  }
}
const trackFunctionOptions = [
  { text: 'nothing (default)', value: undefined },
  { text: 'outline', value: paintOutline },
  { text: 'centered text', value: paintCenterText },
  { text: 'bounding box', value: paintBoundingBox }
]
const trackFunctionSelected = ref(trackFunctionOptions[1])

const error = ref('')

function onError(err) {
  error.value = `[${err.name}]: `

  if (err.name === 'NotAllowedError') {
    error.value += 'you need to grant camera access permission'
  } else if (err.name === 'NotFoundError') {
    error.value += 'no camera on this device'
  } else if (err.name === 'NotSupportedError') {
    error.value += 'secure context required (HTTPS, localhost)'
  } else if (err.name === 'NotReadableError') {
    error.value += 'is the camera already in use?'
  } else if (err.name === 'OverconstrainedError') {
    error.value += 'installed cameras are not suitable'
  } else if (err.name === 'StreamApiNotSupportedError') {
    error.value += 'Stream API is not supported in this browser'
  } else if (err.name === 'InsecureContextError') {
    error.value +=
      'Camera access is only permitted in secure context. Use HTTPS or localhost rather than HTTP.'
  } else {
    error.value += err.message
  }
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
            <v-expand-transition v-if="expandUpload">
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
            <v-expand-transition v-if="expandCamera">
                <v-card-text v-show="expandCamera">
                    <p>Selected Format: {{ selectedFormat }}</p>

                    <qrcode-stream
                        :constraints="selectedConstraints"
                        :track="trackFunctionSelected.value"
                        :formats="[selectedFormat]"
                        @error="onError"
                        @detect="onDetect"
                        @camera-on="onCameraReady"
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
<style scoped>
.error {
  font-weight: bold;
  color: red;
}
.barcode-format-checkbox {
  margin-right: 10px;
  white-space: nowrap;
  display: inline-block;
}
</style>