<script setup>
import Layout from "../../../Layout/Main.vue"
</script>
<template>
    <Layout title="Stocks (Batch/Lots)">
        <v-container max-width="1500" fluid>
            <form>
                <v-autocomplete label="Code" variant="outlined"></v-autocomplete>

                <div class="d-flex justify-center">
                    <v-btn class="me-4" color="green" size="large" >Generate PDF</v-btn>
                    <v-btn class="me-4" color="primary" size="large" >Export to Excel</v-btn>
                </div>
            </form>
        </v-container>
    </Layout>
</template>