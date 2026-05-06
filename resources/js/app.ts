import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createApp, DefineComponent, h } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
       const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue', {eager: true});
       return pages[`./pages/${name}.vue`];
    },
    
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props)})
        .use(plugin)
        .use(ZiggyVue)
        .mount(el as Element);
    },
    progress: {
        color: '#4B5563',
    },
});
