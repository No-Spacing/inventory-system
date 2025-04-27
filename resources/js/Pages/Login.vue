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
    <v-container fluid class="d-flex justify-center fill-height">
        <v-card width="450px" variant="outlined" class="d-flex align-center"> 
            <v-container >
                <v-card-title class="d-flex justify-center">STOCKCARD</v-card-title>
                <v-card-subtitle class="d-flex justify-center">Login</v-card-subtitle>
                <form @submit.prevent="form.post('/submit-login')">
                    <v-card-text>
                        <v-text-field
                        v-model="form.email"
                        :error-messages="form.errors.email"
                        label="E-mail"
                        variant="outlined"
                        class="ma-1 pa-1"
                        ></v-text-field>

                        <v-text-field
                        v-model="form.password"
                        :error-messages="form.errors.password"
                        label="Password"
                        variant="outlined"
                        class="ma-1 pa-1"
                        ></v-text-field>

                        <v-checkbox
                        v-model="form.remember"
                        label="Remember Me"
                        type="checkbox"
                        ></v-checkbox> 
                    </v-card-text>

                    <v-divider></v-divider>
                    
                    <v-card-actions class="d-flex justify-center">
                        <v-btn type="submit" variant="outlined" :loading="form.processing" block>Login</v-btn>
                    </v-card-actions>
                </form>
            </v-container>
        </v-card>
    </v-container>   
</template>