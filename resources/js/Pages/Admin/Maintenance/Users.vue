<script setup>
import Layout from "../../../Layout/Main.vue";
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';


defineProps({
    users: Object,
    roles: Object
})
const headers = [
    {
        title: "Id",
        key: "id",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Name",
        key: "name",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Email",
        key: "email",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Role",
        key: "role",
        headerProps: {
            style: "font-weight: 700",
        },
    },
    {
        title: "Created At",
        key: "created_at",
        headerProps: {
            style: "font-weight: 700",
        },
    },
];
const addUserDialog = ref(false);

const snackbar = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: null,
});

function submit() {
    form.post('/store-user', {
        onSuccess: () => {
            addUserDialog.value = false;
            snackbar.value = true;
            form.reset();
        },
        onError: () => {
        }
    })
}
</script>
<template>
    <Layout title="Users">
        <v-container max-width="1500" fluid>
            <v-card elevation="2" class="pa-8 my-5">
                <v-data-table :headers="headers" :items="users">
                    <template
                        v-slot:bottom="{
                            page,
                            itemsPerPage,
                            pageCount,
                            setItemsPerPage,
                            setPage,
                        }"
                    >
                        <div
                            class="d-flex align-center justify-space-between pa-4"
                        >
                            <v-btn @click="addUserDialog = true" color="primary">
                                Add User
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
                <v-dialog v-model="addUserDialog" max-width="500">
                    <v-card title="Add User">
                        <v-form @submit.prevent="submit">
                            <v-card-text>
                                <v-text-field
                                    v-model="form.name"
                                    :error-messages="form.errors.name"
                                    label="Name"
                                ></v-text-field>
                                <v-text-field
                                    v-model="form.email"
                                    :error-messages="form.errors.email"
                                    label="Email"
                                ></v-text-field>
                                <v-text-field
                                    type="password"
                                    v-model="form.password"
                                    :error-messages="form.errors.password"
                                    label="Password"
                                ></v-text-field>
                                <v-select
                                    v-model="form.role"
                                    :error-messages="form.errors.role"
                                    :items="roles"
                                    item-title="name"
                                    item-value="id"
                                    label="Role"
                                ></v-select>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    class="ma-2 pa-2"
                                    @click="addUserDialog = false"
                                >
                                    close
                                </v-btn>

                                <v-btn class="ma-2 pa-2 bg-primary" type="submit" :loading="form.processing">
                                    submit
                                </v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-card>
                </v-dialog>
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
