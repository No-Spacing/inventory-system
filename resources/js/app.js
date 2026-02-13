import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";

import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";
import { createRulesPlugin } from 'vuetify/labs/rules'
import { createVuetify } from "vuetify";

import colors from "vuetify/util/colors";

import VueApexCharts from "vue3-apexcharts";

const vuetify = createVuetify({
    theme: {
        themes: {
            light: {
                colors: {
                    primary: colors.amber.darken4,
                },
            },
        },
    },
});

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(vuetify)
            .use(VueApexCharts)
            .use(createRulesPlugin({ /* options */ }, vuetify.locale))
            .mount(el);
    },
});
