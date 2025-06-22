<template>
    <!-- <v-dialog
        v-model="dialog"
        max-width="600"
    >
        <v-form @submit.prevent="addLot">
            <v-card title="Add New Lot">
                <v-card-text>
                    <v-row dense>
                        <v-col cols="12">
                            <v-autocomplete
                                v-model="formLot.code"
                                v-model:search="searchCode"
                                :items="products"
                                item-title="code"
                                item-value="id"
                                placeholder="Select..."
                                variant="outlined"
                                menu-icon=""
                            >
                                <template v-slot:no-data>
                                    <v-container fluid>
                                        <span v-if="preload">Loading...</span>
                                        <span v-else>No results found</span>
                                    </v-container>
                                </template>
                                <template #append-inner>
                                    <v-progress-circular
                                    v-if="preload"
                                    indeterminate
                                    size="20"
                                    color="primary"
                                    />
                                </template>
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                v-model="formLot.lot"
                                :error-messages="formLot.errors.lot"
                                label="Lot/Batch No."
                                variant="outlined"
                                required
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <div
                                class="text-subtitle-1 text-medium-emphasis mx-2"
                            >
                                Expiration Date
                            </div>

                            <v-date-input
                                v-model="formLot.expiration_date"
                                :error-messages="formLot.errors.expiration_date"
                                placeholder="Date from"
                                prepend-icon=""
                                prepend-inner-icon="$calendar"
                                variant="outlined"
                            ></v-date-input>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        text="Close"
                        variant="plain"
                        @click="dialog = false"
                    ></v-btn>
                    <v-btn
                        color="green"
                        text="Save"
                        variant="tonal"
                        type="submit"
                    ></v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog> -->
</template>