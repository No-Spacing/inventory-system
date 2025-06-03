<script setup>
import { ref, onMounted } from "vue";
import { useDisplay } from "vuetify";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    title: String,
    loading: Boolean,
});

const { mobile } = useDisplay();

const drawer = ref(true);
const rail = ref(false);
const openGroup = ref([""]);

function navigationRail() {
    this.drawer = !this.drawer;
    this.rail = false;
}

onMounted(() => {
    if(mobile.value == true){
        drawer.value = false
    }
})
</script>
<template>
    <Head title="StockCard" />
    <v-layout>
        <v-navigation-drawer
            v-model="drawer"
            :rail="rail"
            @click="rail = false"
        >
            <v-list>
                <v-list-item href="/dashboard">
                    <div v-if="rail">
                        <v-img
                            src="images/head-icon.png"
                            alt="Company Logo"
                            contain
                        ></v-img>
                    </div>
                    <div v-if="!rail">
                        <!-- Company Logo -->
                        <div class="pt-4 pb-2 d-flex justify-center">
                                <v-img
                                    src="images/inmed-logo.png"
                                    alt="Company Logo"
                                    width="180"
                                    height="60"
                                    contain
                                ></v-img>
                        </div>

                        <div class="d-flex text-center">
                            <v-app-bar-title>
                                <span class="text-subtitle-1 text-grey"
                                    >StockCard</span
                                >
                            </v-app-bar-title>
                        </div>
                    </div>
                </v-list-item>
            </v-list>

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
                            @click="
                                openGroup[0] != 'Transaction';
                                rail = false;
                            "
                        ></v-list-item>
                    </template>

                    <v-list-item title="In" href="/in"></v-list-item>
                    <v-list-item title="Out" href="/out"></v-list-item>
                </v-list-group>

                <v-list-group value="Reports">
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            prepend-icon="mdi-chart-bar"
                            title="Reports"
                            @click="
                                openGroup[0] != 'Reports';
                                rail = false;
                            "
                        ></v-list-item>
                    </template>
                    <v-list-item
                        title="Stockcard"
                        href="stockcard"
                    ></v-list-item>
                    <v-list-item
                        title="Stocks (Batch/Lots)"
                        href="stocks"
                    ></v-list-item>
                    <v-list-item
                        title="Stocks with PR No."
                        href="/stocks_with_pr"
                    ></v-list-item>
                    <v-list-item
                        title="Product Expiry"
                        href="/product_expiry"
                    ></v-list-item>
                    <v-list-item title="Summary" href="/summary"></v-list-item>
                </v-list-group>

                <v-list-group
                    value="Maintenance"
                    v-if="$page.props.user.can.view"
                >
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            title="Maintenance"
                            prepend-icon="mdi-wrench"
                            @click="
                                openGroup[0] != 'Maintenance';
                                rail = false;
                            "
                        ></v-list-item>
                    </template>
                    <v-list-item title="Units" href="/units"></v-list-item>
                    <v-list-item title="Category" href="/category"></v-list-item>
                    <v-list-item title="Product" href="/product"></v-list-item>
                    <v-list-item title="Lot Number" href="/lot"></v-list-item>
                    <v-list-item title="Serial Number" href="/serial"></v-list-item>
                    <v-list-item v-if="$page.props.user.can.create" title="Users" href="/users"></v-list-item>
                </v-list-group>
            </v-list>

            <template v-slot:append>
                <v-divider></v-divider>
                <v-list density="compact" nav>
                    <!-- User Info -->
                    <v-list-item
                        :title="$page.props.user.name"
                        :subtitle="$page.props.user.email"
                    >
                        <template v-slot:prepend>
                            <v-icon icon="mdi-account-circle"></v-icon>
                        </template>
                        <template v-slot:append>
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
                                        href="/forgot-password"
                                    ></v-list-item>
                                    <v-list-item
                                        title="Logout"
                                        prepend-icon="mdi-logout"
                                        href="/logout"
                                    ></v-list-item>
                                </v-list>
                            </v-menu>
                        </template>
                    </v-list-item>
                </v-list>
            </template>
        </v-navigation-drawer>

        <v-app-bar :elevation="2" app :scroll-behavior="$vuetify.display.mobile ? 'hide' : ''" scroll-threshold="1">
            <template v-slot:prepend>
                <!-- <v-app-bar-nav-icon  @click.stop="rail = !rail; openGroup = ''"/> -->
                <v-app-bar-nav-icon
                    variant="text"
                    @click="
                        rail = !rail;
                        openGroup = '';
                    "
                    v-if="mobile === false"
                ></v-app-bar-nav-icon>
                <v-app-bar-nav-icon
                    variant="text"
                    @click="navigationRail()"
                    v-if="mobile"
                ></v-app-bar-nav-icon>
            </template>

            <template v-slot:title>
                <span class="text-primary font-weight-bold">
                    {{ title ? title : "StockCard" }}
                </span>
            </template>

            <v-menu :close-on-content-click="false">
                <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" icon>
                        <v-badge
                            color="primary"
                            dot
                        >
                            <v-icon>mdi-bell-outline</v-icon>
                        </v-badge>
                    </v-btn>
                </template>

                <v-card width="500">
                    <v-card-title>Notifications</v-card-title>

                    <v-divider></v-divider>

                    <v-list>
                        <v-list-item title="The Supervisor wants to update an Item."></v-list-item>
                        <v-list-item title="The Encoder wants to update an Item."></v-list-item>
                        <v-list-item title="The Admin wants to update password."></v-list-item>
                    </v-list>

                    <v-card-actions class="d-flex justify-end">
                        <v-btn size="small">Mark as read</v-btn>
                        <v-btn size="small">Mark as unread</v-btn>
                    </v-card-actions>
                </v-card>
            </v-menu>

            <v-progress-linear
                v-if="props.loading"
                :active="true"
                :indeterminate="true"
                color="primary"
                location="bottom"
                absolute
            ></v-progress-linear>
        </v-app-bar>

        <v-main>
            <div
                class="d-flex flex-column fill-height bg-grey-lighten-4"
                style="min-height: 94vh"
            >
                <slot />
            </div>
        </v-main>
    </v-layout>
</template>
