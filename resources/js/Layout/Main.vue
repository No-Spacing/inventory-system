<script setup>
import { ref, watch } from "vue";

const drawer = ref(true);
const rail = ref(false);

const cruds = [
    ["Create", "mdi-plus-outline"],
    ["Read", "mdi-file-outline"],
    ["Update", "mdi-update"],
    ["Delete", "mdi-delete"],
];
</script>
<template>
    <v-card>
        <v-layout>
            <v-navigation-drawer v-model="drawer" :rail="rail">
                <!-- Company Logo -->
                <div class="pt-4 pb-2 d-flex justify-center" v-if="!rail">
                    <v-img
                        src="images/inmed-logo.png"
                        alt="Company Logo"
                        width="120"
                        height="60"
                        contain
                    ></v-img>
                </div>

                <div class="d-flex text-center" v-if="!rail">
                    <v-app-bar-title>
                        <span class="text-subtitle-1 text-grey">Stockard</span>
                    </v-app-bar-title>
                </div>

                <v-divider></v-divider>

                <v-list density="compact" nav>
                    <v-list-item
                        prepend-icon="mdi-view-dashboard"
                        href="/dashboard"
                        title="Dashboard"
                    ></v-list-item>
                    <v-list-group>
                        <template v-slot:activator="{ props }">
                            <v-list-item
                                v-bind="props"
                                title="Transaction"
                                prepend-icon="mdi-clipboard-text-outline"
                            ></v-list-item>
                        </template>

                        <v-list-item title="In" href="/in"></v-list-item>
                        <v-list-item title="Out" href="/out"></v-list-item>
                    </v-list-group>
                    <v-list-item
                        prepend-icon="mdi-chart-bar"
                        title="Reports"
                        href="/reports"
                    ></v-list-item>
                    <v-list-group v-if="$page.props.user.can.view">
                        <template v-slot:activator="{ props }">
                            <v-list-item
                                v-bind="props"
                                title="Maintenance"
                                prepend-icon="mdi-wrench"
                            ></v-list-item>
                        </template>

                        <!-- <v-list-item v-if="$page.props.user.can.view" prepend-icon="mdi-wrench" href="/maintenance"
                            title="Maintenance"></v-list-item> -->
                        <v-list-item title="Units" href="/units"></v-list-item>
                        <v-list-item
                            title="Category"
                            href="/category"
                        ></v-list-item>
                        <v-list-item
                            title="Lot Number"
                            href="/lot"
                        ></v-list-item>
                        <v-list-item
                            title="Serial Number"
                            href="/serial"
                        ></v-list-item>
                    </v-list-group>
                </v-list>

                <template v-slot:append>
                    <v-list density="compact" nav>
                        <div
                            class="d-flex justify-space-between align-center px-4 py-2"
                        >
                            <!-- User Info -->
                            <div>
                                <div class="text-subtitle-2 font-weight-medium">
                                    {{ $page.props.user.name }}
                                </div>
                                <div class="text-caption text-grey">
                                    {{ $page.props.user.email }}
                                </div>
                            </div>

                            <!-- 3 Dots Menu -->
                            <v-menu location="top start" offset="4">
                                <template #activator="{ props }">
                                    <v-btn
                                        v-bind="props"
                                        icon="mdi-dots-vertical"
                                        variant="text"
                                        size="small"
                                    ></v-btn>
                                </template>

                                <v-list>
                                    <v-list-item
                                        title="Change Password"
                                        prepend-icon="mdi-lock-reset"
                                        href="/change-password"
                                    ></v-list-item>
                                    <v-list-item
                                        title="Logout"
                                        prepend-icon="mdi-logout"
                                        href="/logout"
                                    ></v-list-item>
                                </v-list>
                            </v-menu>
                        </div>
                    </v-list>
                </template>
            </v-navigation-drawer>

            <v-app-bar :elevation="2">
                <template v-slot:prepend>
                    <v-app-bar-nav-icon
                        @click.stop="rail = !rail"
                    ></v-app-bar-nav-icon>
                </template>

                <v-app-bar-title>Application Bar</v-app-bar-title>
            </v-app-bar>

            <v-main>
                <slot />
            </v-main>
        </v-layout>
    </v-card>
</template>
