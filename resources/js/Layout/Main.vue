<script setup>
  import { ref, watch } from 'vue'

  const drawer = ref(true)
  const rail = ref(true)

</script>
<template>
    <v-card>
        <v-layout>
        <v-navigation-drawer
            expand-on-hover
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
                <v-list-item prepend-icon="mdi-view-dashboard" href="/maintenance" title="Maintenance"></v-list-item>
                <v-list-item>
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-title>
                                Transactions
                            </v-expansion-panel-title>
                            <v-expansion-panel-text>
                                <v-list-item prepend-icon="mdi-import" href="/in">In</v-list-item>
                                <v-list-item prepend-icon="mdi-export" href="/out">Out</v-list-item>
                            </v-expansion-panel-text>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-list-item>
                <v-list-item prepend-icon="mdi-chart-bar" title="Reports"></v-list-item>
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