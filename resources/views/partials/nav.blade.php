<div>
    <b-navbar toggleable="lg" type="dark" variant="dark">
        <b-navbar-brand :to="{name: 'home'}">Ecomercium PIM</b-navbar-brand>

        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse id="nav-collapse" is-nav>
            <b-navbar-nav>
                <b-nav-item :to="{name: 'home'}">Inicio</b-nav-item>
                <b-nav-item :to="{name: 'productos'}">Todos los productos</b-nav-item>
                <b-nav-item :to="{name: 'crear-producto'}">Añadir producto</b-nav-item>
                <b-nav-item :to="{name: 'tiendas'}">Tiendas</b-nav-item>
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</div>
