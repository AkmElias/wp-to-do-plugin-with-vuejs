window.toDoBus = new window.toDo.Vue();

window.toDo.Vue.mixin({
    methods: {
        applyFilters: window.toDo.applyFilters,
        addFilter: window.toDo.addFilter,
        addAction: window.toDo.addFilter,
        doAction: window.toDo.doAction,
        $get: window.toDo.$get,
        $adminGet: window.toDo.$adminGet,
        $adminPost: window.toDo.$adminPost,
        $post: window.toDo.$post,
        $publicAssets: window.toDo.$publicAssets
    }
});

import {routes} from './routes';

const router = new window.toDo.Router({
    routes: window.toDo.applyFilters('toDo_global_vue_routes', routes),
    linkActiveClass: 'active'
});

import App from './AdminApp';

new window.toDo.Vue({
    el: '#to-do_app',
    render: h => h(App),
    router: router
});

