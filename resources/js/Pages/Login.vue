<template>
    <div class="d-flex justify-center">
        <v-card width="450px">
            <v-container >
                <v-card-title class="d-flex justify-center">Login</v-card-title>
                <v-card-text>

                    <v-text-field
                    label="E-mail"
                    variant="outlined"
                    required
                    ></v-text-field>

                    <v-text-field
                    label="Password"
                    variant="outlined"
                    required
                    ></v-text-field>

                    <v-checkbox
                    label="Remember Me"
                    type="checkbox"
                    value="1"
                    ></v-checkbox>
                    
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions class="d-flex justify-center">
                    <v-btn variant="outlined" block>Login</v-btn>
                </v-card-actions>
            </v-container>
        </v-card>
    </div>   
</template>