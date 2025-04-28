<script setup>
import { useForm } from '@inertiajs/vue3'
import { errorMessages } from 'vue/compiler-sfc';

const form = useForm({
  email: null,
  password: null,
  remember: false,
})
</script>

<template>
    <v-container fluid class="login-background d-flex justify-center align-center fill-height">
      <v-card class="pa-10" :elevation="8" max-width="450" width="100%" rounded="2xl">

        <!-- Logo added here -->
      <div class="text-center mb-4">
        <v-img
          src="images/inmed-logo.png"
          alt="Logo"
          max-width="150"
          contain
          class="mx-auto"
        />
      </div>

        <v-card-subtitle class="text-h6 text-medium-emphasis text-center mb-2">
            Stockcard
        </v-card-subtitle>

        <v-form v-model="formValid" @submit.prevent="form.post('/submit-login')">
          <v-text-field
            v-model="form.email"
            :error-messages="form.errors.email"
            label="Email"
            prepend-inner-icon="mdi-email"
            variant="outlined"
            density="comfortable"
            class="mb-2"
            required
          />

          <v-text-field
            v-model="form.password"
            :error-messages="form.errors.password"
            label="Password"
            prepend-inner-icon="mdi-lock"
            variant="outlined"
            type="password"
            density="comfortable"
            required
          />

          <v-checkbox
          label="Remember me"
          color="amber-darken-4"
          ></v-checkbox>

          <v-btn
            type="submit"
            color="primary"
            block
            size="large"
            rounded="xl"

          >
            Sign In
          </v-btn>

        </v-form>
      </v-card>
    </v-container>
  </template>

  <style scoped>
  .fill-height {
    min-height: 100vh;
  }

  /* Background image */
  .login-background {
    background: url('images/inmed.jpg') no-repeat center center;
    background-size: cover;
  }

  /* Card custom width (optional, already using max-width) */
  .v-card {
    backdrop-filter: blur(10px); /* optional: glass effect */
    background-color: rgba(255, 255, 255, 0.85); /* slight transparency */
  }
  </style>

