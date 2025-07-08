<script setup>
import Layout from "../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
import { ref, reactive } from "vue";
import { router } from "@inertiajs/vue3"
 
const loading = ref(false);
function load() {
    loading.value = true;
    setTimeout(() => (loading.value = false), 2000);
}

const form = reactive({
    from: null,
    to: null,
})

const errorMessages = ref();

function submit () {
    router.post('/generate-summary', form,
        {
            onSuccess: () => {
            },
            onError: (error) => {
                errorMessages.value = error
                console.log(error)
            },
        }
    )
}

</script>
<template>
    <Layout title="Summary">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form @submit.prevent="submit">
                    <v-row no-gutters>
                        <v-col md="6" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date From
                            </div>
                            <v-date-input
                                v-model="form.from"
                                :error-messages="errorMessages?.from"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                class="mx-2"
                            ></v-date-input>
                        </v-col>
                        <v-col md="6" cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Date to
                            </div>
                            <v-date-input
                                v-model="form.to"
                                :error-messages="errorMessages?.to"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                                class="mx-2"
                            ></v-date-input>
                        </v-col>
                    </v-row>

                    <v-row justify="center">
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn class="me-4" color="green" size="large" type="submit">
                                Generate PDF
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
