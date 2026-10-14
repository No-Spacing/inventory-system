import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";

import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";
import { createRulesPlugin } from 'vuetify/labs/rules'
import { createVuetify } from "vuetify";

import colors from "vuetify/util/colors";

const vuetify = createVuetify({});

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue') 
        return pages[`./Pages/${name}.vue`]()
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(vuetify)
            .use(createRulesPlugin({ /* options */ }, vuetify.locale))
            .mount(el);
    },
});
