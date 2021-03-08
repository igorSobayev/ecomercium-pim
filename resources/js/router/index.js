import Vue from 'vue';
import Router from 'vue-router';

// Errores
import NotFound from './../views/404/NotFound'

// Home
import Home from './../views/Home'

// Productos
import Productos from './../views/productos/Productos'
import CrearEditarProducto from './../views/productos/CrearEditarProducto'

// Tiendas
import Tiendas from './../views/tiendas/Tiendas'
import GestionarProductosTienda from './../views/tiendas/GestionarProductosTienda'
import EditarTienda from './../views/tiendas/EditarTienda'

// Atributos
import Atributos from './../views/atributos/Atributos'

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/',
    fallback: true,
    linkActiveClass: 'active',
    routes: [{
        path: '*',
        component: NotFound
    },
    // Home
    {
        path: '/',
        name: 'home',
        component: Home,
    },
    {
        path: '/productos',
        name: 'productos',
        component: Productos,
    },
    {
        path: '/productos/crear-producto',
        name: 'crear-producto',
        component: CrearEditarProducto,
    },
    {
        path: '/productos/editar-producto/:id_producto',
        name: 'editar-producto',
        component: CrearEditarProducto
    },
    {
        path: '/tiendas',
        name: 'tiendas',
        component: Tiendas,
    },
    {
        path: '/tiendas/gestionar-productos/:id_tienda',
        name: 'gestionar-productos-tienda',
        component: GestionarProductosTienda
    },
    {
        path: '/tiendas/crear-tienda',
        name: 'crear-tienda',
        component: EditarTienda
    },
    {
        path: '/tiendas/editar-tienda/:id_tienda',
        name: 'editar-tienda',
        component: EditarTienda
    },
    {
        path: '/atributos',
        name: 'atributos',
        component: Atributos,
    },
    ]
});
