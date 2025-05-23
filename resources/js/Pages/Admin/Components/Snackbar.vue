<script setup>
    const props = defineProps({
        snackbar: Boolean,
        color: String,
    })
    </script>
<template>
    <v-snackbar
        v-model="props.snackbar"
        :timeout="2000"
        :color="props.color"
        variant="elevated"
        location="top"
        class="text"
    >
        {{ $page.props.flash.message }}
    </v-snackbar>
</template>