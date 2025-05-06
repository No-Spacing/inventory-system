<script setup>
import Layout from "../../../Layout/Main.vue"
import { VDateInput } from 'vuetify/labs/VDateInput'
</script>
<template>
    <Layout title="Stocks with PR No.">
        <v-container fluid>
            <form>

                <v-date-input label="Date from" variant="outlined"></v-date-input>

                <v-date-input label="Date to" variant="outlined"></v-date-input>

                <div class="d-flex justify-center">
                    <v-btn class="me-4" color="green" size="large" >Generate PDF</v-btn>
                    <v-btn class="me-4" color="primary" size="large" >Export to Excel</v-btn>
                </div>
            </form>
        </v-container>
    </Layout>
</template>