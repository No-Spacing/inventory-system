<script setup>
  import { ref, watch } from 'vue'

  const drawer = ref(true)
  const rail = ref(false)

  const cruds = [
    ['Create', 'mdi-plus-outline'],
    ['Read', 'mdi-file-outline'],
    ['Update', 'mdi-update'],
    ['Delete', 'mdi-delete'],
  ]

</script>
<template>
    <v-card>
        <v-layout>
        <v-navigation-drawer
            v-model="drawer"
            :rail="rail"
            >
            <v-list>
            <v-list-item
                prepend-icon="mdi-account-circle"
            >
                <v-list-item-title>{{ $page.props.user.name }}</v-list-item-title>
                <v-list-item-subtitle>{{ $page.props.user.email }}</v-list-item-subtitle>
            </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-list density="compact" nav>
                <v-list-item prepend-icon="mdi-view-dashboard" href="/dashboard" title="Dashboard"></v-list-item>
                <v-list-group>
                    <template v-slot:activator="{ props }">
                        <v-list-item
                        v-bind="props"
                        title="Transaction"
                        prepend-icon="mdi-clipboard-text-outline"
                        ></v-list-item>
                    </template>

                    <v-list-item prepend-icon="mdi-invoice-import-outline" href="/in">In</v-list-item>
                    <v-list-item prepend-icon="mdi-invoice-export-outline" href="/out">Out</v-list-item>
                </v-list-group>
                <v-list-item prepend-icon="mdi-chart-bar" title="Reports" href="/reports"></v-list-item>
                <v-list-item v-if="$page.props.user.can.view" prepend-icon="mdi-wrench" href="/maintenance" title="Maintenance"></v-list-item>
            </v-list>
            <template v-slot:append>
                <v-list density="compact" nav>
                    <v-list-item prepend-icon="mdi-logout" title="Logout" href="/logout"></v-list-item>
                </v-list>
            </template>
        </v-navigation-drawer>

        <v-app-bar :elevation="2">
            <template v-slot:prepend>
                <v-app-bar-nav-icon @click.stop="rail = !rail"></v-app-bar-nav-icon>
            </template>

            <v-app-bar-title>Application Bar</v-app-bar-title>
            </v-app-bar>

        <v-main>
            <slot />
        </v-main>
        </v-layout>
    </v-card>
</template>
