<script setup>
import Layout from "../../../Layout/Main.vue"
import { VDateInput } from 'vuetify/labs/VDateInput'
</script>
<template>
    <Layout title="Stockcard">
        <v-container fluid>
            <form>
                <v-autocomplete label="Code" variant="outlined"></v-autocomplete>

                <v-text-field label="Description" variant="outlined"></v-text-field>

                <v-autocomplete label="Lot No." variant="outlined"></v-autocomplete>

                <v-select label="Filter By" :items="['Date of Posting', 'Date of Transaction']" variant="outlined"></v-select>

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