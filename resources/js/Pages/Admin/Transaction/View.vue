<script setup>
import Layout from '../../../Layout/Main.vue'
import { ref } from 'vue'

const selected = ref('');
const options = ref([
    { text: 'rear camera (default)', value: 'environment' },
    { text: 'front camera', value: 'user' },
    { text: 'force file dialog', value: null }
]);
const result = ref('');


function onDetect(detectedCodes) {
  console.log(result)
  result.value = JSON.stringify(detectedCodes.map((code) => code.rawValue))
}
</script>
<template>
    <Layout title="Testing">
        <div>
            <p>
            Capture:
            <select v-model="selected">
                <option
                v-for="option in options"
                :key="option.text"
                :value="option"
                >
                {{ option.text }}
                </option>
            </select>
            </p>

            <hr />

            <p class="decode-result">
            Last result: <p>{{ result }}</p>
            </p>

            <p>
            Google: <a :href="'result'">google</a>
            </p>

            <qrcode-capture
            @detect="onDetect"
            :capture="selected.value"
            />
        </div>
    </Layout>
</template>