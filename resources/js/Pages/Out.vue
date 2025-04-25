<script setup>
import Layout from '../Layout/Main.vue'
</script>

<template>
    <Layout>
        <v-card>
            <v-container fluid>
                <v-card-title>Out Transaction</v-card-title>
                <form>
                    <v-text-field
                        label="Name"
                    ></v-text-field>
            
                    <v-text-field
                        label="Phone Number"
                    ></v-text-field>
                
                    <v-text-field
                        label="E-mail"
                    ></v-text-field>
                
                    <v-select
                        label="Select"
                    ></v-select>
                
                    <v-checkbox

                        label="Option"
                        type="checkbox"
                        value="1"
                    ></v-checkbox>
                
                    <v-btn
                        class="me-4"
                    >
                        submit
                    </v-btn>
                
                    <v-btn>
                        clear
                    </v-btn>
                </form>
            </v-container>
        </v-card>
    </Layout>
</template>