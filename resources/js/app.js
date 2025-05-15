import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import { VueQrcodeReader } from 'vue-qrcode-reader'

import colors from 'vuetify/util/colors'

const vuetify = createVuetify({
    components,
    directives,
    theme: {
        themes: {
          light: {
            dark: false,
            colors: {
              primary: colors.amber.darken4,
            //   secondary: colors.red.lighten4, // #FFCDD2
            }
          },
        },
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
      .use(VueQrcodeReader)
      .use(vuetify)
      .mount(el)
    },
  })



