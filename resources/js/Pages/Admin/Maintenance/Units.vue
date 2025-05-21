<script setup>
import Layout from "../../../Layout/Main.vue"
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';

const snackbar = ref(false)
const addUnitDialog = ref(false);

const emit = defineEmits(['snackbar']);

const props = defineProps({
    units: Object
})
const headers = [
    { 
        title: 'Id',
        key: 'id',
        headerProps: {
            style: "font-weight: 700",
        },
    },
    { 
        title: 'Unit',
        key: 'unit',
        headerProps: {
            style: "font-weight: 700",
        },
    },
]

const form = useForm({
    unit: ''
});

function submit() {
    form.post('/store-unit', {
        onSuccess: () => {
            addUnitDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
        onError: () => {
        }
    })
}

</script>
<template>
    <Layout title="Units">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-card-text>
                    <v-data-table :headers="headers" :items="units">
                        <template v-slot:bottom="{ page, itemsPerPage, pageCount, setItemsPerPage, setPage }">
                            <div class="d-flex align-center justify-space-between pa-4">
                            <v-btn color="primary" @click="addUnitDialog = true">
                                Add Units
                            </v-btn>

                            <v-data-table-footer
                                :page="page"
                                :items-per-page="itemsPerPage"
                                :page-count="pageCount"
                                @update:items-per-page="setItemsPerPage"
                                @update:page="setPage"
                            />
                            </div>
                        </template>
                    </v-data-table>
                    <v-dialog v-model="addUnitDialog" max-width="500">
                        <v-card title="Add Units">
                            <form @submit.prevent="submit">
                                <v-card-text>
                                
                                        <v-text-field
                                        v-model="form.unit"
                                        :error-messages="form.errors.unit"
                                        label="Unit"
                                        ></v-text-field>
                                    
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>

                                    <v-btn class="ma-2 pa-2" @click="addUnitDialog = false">
                                    close
                                    </v-btn>
                                    
                                    <v-btn 
                                    class="ma-2 pa-2 bg-primary"
                                    type="submit"
                                    :loading="form.processing"
                                    >
                                    submit
                                    </v-btn>
                                </v-card-actions>
                            </form>
                        </v-card>
                    </v-dialog>
                </v-card-text>
            </v-card>
        </v-container>
    </Layout>
    <v-snackbar
        v-model="snackbar"
        :timeout="2000"
        color="success"
        variant="elevated"
        location="top"
        class="text"
    >
        {{ $page.props.flash.message }}
    </v-snackbar>
</template>