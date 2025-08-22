import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'

import colors from 'vuetify/util/colors'

const vuetify = createVuetify({
    theme: {
        themes: {
          light: {
            colors: {
              primary: colors.amber.darken4,
            }
          },
        },
      },
    icons: {
      defaultSet: 'mdi',
      aliases,
      sets: { mdi },
    },  
  })

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
    .use(plugin)
    .use(vuetify)
    .mount(el)
  },
})



