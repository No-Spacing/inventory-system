<script setup>
import Layout from '../Layout/Main.vue'
</script>

<template>
    <Layout>
        <v-card>
            <v-container fluid>
                <v-card-title>In Transaction</v-card-title>
                <form>
                    <v-autocomplete
                        label="Code"
                        :items="['1-BR-BD7800', '9-IC-BABPA1', '0-PM-104116', '4-EP-ECGMS3', 'P-MC-000006', '2-PP-KCPK24']"
                    ></v-autocomplete>

                    <v-text-field
                        label="Description"
                    ></v-text-field>
            
                    <v-text-field
                        label="Unit"
                    ></v-text-field>
                
                    <v-text-field
                        label="Total Items"
                    ></v-text-field>
                
                    <v-autocomplete
                        label="Lot/Batch No."
                        :items="['0000', '0001', '0002', '0003', '0004', '0005']"
                    ></v-autocomplete>

                    <v-text-field
                        label="Total Items Per Lot/Batch"
                    ></v-text-field>

                    <v-text-field
                        type="date"
                        label="Expiration Date"
                    ></v-text-field>

                    <v-text-field
                        label="Reference In"
                    ></v-text-field>

                    <v-textarea
                        label="Remarks"
                    ></v-textarea>

                    <v-text-field
                        type="number"
                        label="Quantity"
                    ></v-text-field>

                    <v-text-field
                        type="date"
                        label="Date of Transaction"
                    ></v-text-field>

                    <v-text-field
                        type="date"
                        label="Date of Transaction"
                    ></v-text-field>
                
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