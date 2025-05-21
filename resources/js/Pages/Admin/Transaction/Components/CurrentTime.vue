<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const currentTime = ref(new Date());
let intervalId;

const updateTime = () => {
  currentTime.value = new Date();
};

onMounted(() => {
  intervalId = setInterval(updateTime, 1000); // Update every second
});

onUnmounted(() => {
  clearInterval(intervalId); // Clear the interval when the component is unmounted
});
</script>
<template>
    <v-card variant="outlined" class="d-flex justify-center" :width="500">
        <v-container>
            <v-card-title>Current Date and Time</v-card-title>
            <v-card-text>
                <p>{{ currentTime }}</p>
            </v-card-text>
        </v-container>
    </v-card>
</template>