<script setup>
import Layout from "../../Layout/Main.vue";
import { VDateInput } from "vuetify/labs/VDateInput";
import { ref, reactive } from "vue";
import { router } from "@inertiajs/vue3"
import download from 'downloadjs'; 

const loading = ref(false);
function load() {
    loading.value = true;
    setTimeout(() => (loading.value = false), 2000);
}

const form = reactive({
    from: null,
    to: null,
    action: null,
})

const errorMessages = ref();

function submit (value) {
    form.action = value;
    
    if(value == "pdf")
    {
        router.post('/generate-summary', form,
            {
                onSuccess: () => {
                    loading.value = false
                },
                onError: (error) => {
                    errorMessages.value = error
                    console.log(error)
                    loading.value = false
                },
            }
        )

        // axios.post('generate-summary', form, {
        //     responseType: 'blob',
        // })
        // .then((response) => {
        //     const fileURL = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
        //     window.open(fileURL); // Opens in a new tab
        //     loading.value = false
        // })
        // .catch((error) => {
        //     console.error('PDF generation failed:', error);
        //     loading.value = false
        // });
    }
    if(value == "excel")
    {
        axios.post('/generate-summary', form, {
            responseType: 'blob',
        })
        .then((response) => {
            const contentType = response.headers['content-type'];
            download(response.data, 'summary.xlsx', contentType);
            loading.value = false
        })
        .catch((error) => {
            console.error('Excel generation failed:', error);
            loading.value = false
        });
    }
    // router.post('/generate-summary', form,
    //     {
    //         onSuccess: () => {
    //             loading.value = false
    //         },
    //         onError: (error) => {
    //             errorMessages.value = error
    //             console.log(error)
    //         },
    //     }
    // )
}

</script>
<template>
    <Layout title="Summary">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-form @submit.prevent>
                    <!-- <v-row no-gutters>
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
                    </v-row> -->

                    <v-row justify="center">
                        <v-col cols="12" sm="auto" class="text-center">
                            <v-btn 
                                class="me-4" 
                                color="green" 
                                size="large" 
                                type="submit" 
                                :loading="loading"
                                @click="submit('pdf')"
                            >
                                Generate PDF
                            </v-btn>
                            <v-btn
                                type="submit"
                                class="me-4"
                                color="primary"
                                :loading="loading"
                                size="large"
                                @click="submit('excel')"
                            >
                                Export Excel
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card>
        </v-container>
    </Layout>
</template>
