<script setup>
import { ref, watch } from "vue";
import { useDisplay } from 'vuetify'
import { Head } from '@inertiajs/vue3';

defineProps({
    title: String,
})

const { mobile } = useDisplay()

const drawer = ref(true);
const rail = ref(false);
const openGroup = ref(['']);

function navigationRail () {
    this.drawer = !this.drawer;
    this.rail = false;
}

const cruds = [
    ["Create", "mdi-plus-outline"],
    ["Read", "mdi-file-outline"],
    ["Update", "mdi-update"],
    ["Delete", "mdi-delete"],
];
</script>
<template>
    <Head title="StockCard" />
    <v-card>
        <v-layout>
            <v-navigation-drawer v-model="drawer" :rail="rail" @click="rail = false">
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
                        <span class="text-subtitle-1 text-grey">StockCard</span>
                    </v-app-bar-title>
                </div>

                <v-divider></v-divider>

                <v-list v-model:opened="openGroup" density="compact" nav>
                    <v-list-item
                        prepend-icon="mdi-view-dashboard"
                        href="/dashboard"
                        title="Dashboard"
                    ></v-list-item>
                    <v-list-group value="Transaction">
                        <template v-slot:activator="{ props }">
                            <v-list-item
                                v-bind="props"
                                title="Transaction"
                                prepend-icon="mdi-clipboard-text-outline"
                                @click="openGroup[0] != 'Transaction'; rail = false"
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
                        
                    <v-list-group value="Maintenance" v-if="$page.props.user.can.view">
                        <template v-slot:activator="{ props }">
                            <v-list-item
                                v-bind="props"
                                title="Maintenance"
                                prepend-icon="mdi-wrench"
                                @click="openGroup[0] != 'Maintenance'; rail = false"
                            ></v-list-item>
                        </template>
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
                        <!-- User Info -->
                        <v-list-item
                            prepend-icon="mdi-account-circle"
                        >
                            <div class="d-flex align-center px-4">
                                <v-row class="mr-5">
                                    <v-list-item-title>{{ $page.props.user.name }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ $page.props.user.email }}</v-list-item-subtitle>
                                </v-row>
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
                        </v-list-item>
                    </v-list>
                </template>
            </v-navigation-drawer>

            <v-app-bar :elevation="2">
                <template v-slot:prepend>
                    <!-- <v-app-bar-nav-icon  @click.stop="rail = !rail; openGroup = ''"/> -->
                    <v-app-bar-nav-icon variant="text" @click="rail = !rail; openGroup = ''" v-if="mobile === false"></v-app-bar-nav-icon>
                    <v-app-bar-nav-icon variant="text" @click="navigationRail();" v-if="mobile"></v-app-bar-nav-icon> 
                </template>

                <template v-slot:title>
                    <span class="text-primary font-weight-bold">
                        {{ title ? title : "StockCard" }}
                    </span>
                </template>
            </v-app-bar>

            <v-main>
                <slot />
            </v-main>
        </v-layout>
    </v-card>
</template>
