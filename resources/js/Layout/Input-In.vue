<template>
    <v-container fluid>

            <v-form>
                <v-container>
                    <div class="d-flex">
                        <v-col>
                            <v-autocomplete
                                label="Code"
                                :items="['1-BR-BD7800', '9-IC-BABPA1', '0-PM-104116', '4-EP-ECGMS3', 'P-MC-000006', '2-PP-KCPK24']"
                                variant="outlined" 
                            ></v-autocomplete>
                        </v-col>
                        
                        <v-col>
                            <v-text-field
                                label="Description"
                                variant="outlined"  
                            ></v-text-field>
                        </v-col>

                        <v-col>
                            <v-text-field
                                label="Unit"
                                variant="outlined"  
                            ></v-text-field>
                        </v-col>              
                    </div>

                    <div class="d-flex">
                        <v-col>
                            <v-autocomplete
                                label="Lot/Batch No."
                                :items="['0000', '0001', '0002', '0003', '0004', '0005']"
                                variant="outlined"  
                                class="mx-1"
                            ></v-autocomplete>
                        </v-col>

                        <v-col>
                            <v-text-field
                                label="Total Items"
                                variant="outlined"  
                                class="mx-1"
                            ></v-text-field>
                        </v-col>

                        <v-col>
                            <v-text-field
                            label="Total Items Per Lot/Batch"
                            variant="outlined"
                            class="mx-1"  
                        ></v-text-field>
                        </v-col>
                    </div>
                </v-container>
            </v-form>
    </v-container>
</template>