<script setup>
import Layout from "../../../Layout/Main.vue";
import InAdjustments from "./Components/In/InAdjustments.vue";
import InTable from "./Components/In/InTable.vue";
import { ref } from "vue";

const tab = ref(null);

const props = defineProps({ products: Object, stockcards: Object });

const loading = ref(false)

const handleData = (data) => {
  loading.value = data;
};

</script>
<template>
    <Layout title="Inbound" :loading="loading">
        <v-container max-width="1600" class="mt-5" fluid>
            <v-tabs v-model="tab" align-tabs="center" bg-color="primary">
                <v-tab :value="1">
                    <v-icon start icon="mdi-format-list-bulleted"></v-icon>
                    <p class="font-weight-bold">List</p>
                </v-tab>
                <v-tab :value="2">
                    <v-icon start icon="mdi-plus"></v-icon>
                    <p class="font-weight-bold">Create</p>
                </v-tab>
            </v-tabs>

            <v-card class="pa-8" elevation="2">
                <v-tabs-window v-model="tab">
                    <v-tabs-window-item :value="1">
                        <v-container fluid>
                            <InTable></InTable>
                        </v-container>
                    </v-tabs-window-item>
                    <v-tabs-window-item :value="2">
                        <v-container max-width="1500" fluid>
                            <InAdjustments @loading="handleData"></InAdjustments>
                        </v-container>
                    </v-tabs-window-item>
                </v-tabs-window>
            </v-card>
        </v-container>
    </Layout>
</template>
