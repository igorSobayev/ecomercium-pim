<template>
  <section class="body">
    <div class="d-flex justify-content-center row" v-if="!loading">
      <div class="col-lg-10 pb-4 pl-0">
        <h2>Listado de todos los productos</h2>
      </div>
      <div class="col-lg-10 mb-2">
        <label for="filtroSeleccionado">Buscar por nombre:</label>
        <form class="row" @submit.prevent="cargarTodosProductos(1)">
          <div class="mr-2">
            <button type="button" title="Buscar por nombre" class="btn btn-info rounded text-white" @click="cargarTodosProductos(1)">
              <i class="fas fa-search"></i>
            </button>
          </div>
          <div class="mr-2">
            <input type="text" v-model="filtroSeleccionado" name="filtroSeleccionado" placeholder="Maceta" class="form-control" id="filtroSeleccionado" />
          </div>
          <div class="" v-if="filtroSeleccionado != ''">
            <button type="button" title="Reiniciar" class="btn btn-sm btn-danger rounded text-white" @click="reiniciarFiltro()">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </form>
      </div>
      <table class="table table-hover col-lg-10">
        <thead>
          <tr>
            <th scope="col">ID Producto</th>
            <th scope="col">Nombre</th>
            <th scope="col">Referencia</th>
            <th scope="col">Precio</th>
            <th scope="col">Stock</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(producto, index) in productos" :key="index">
            <th scope="row">{{ producto.id_producto }}</th>
            <td>{{ producto.nombre_producto }}</td>
            <td>{{ producto.referencia }}</td>
            <td>{{ producto.precio_sin_iva }} €</td>
            <td>{{ producto.cantidad }}</td>
            <td>
              <b-dropdown text="Acciones" variant="outline-success">
                <b-dropdown-item :to="{ name: 'editar-producto', params: { id_producto: producto.id_producto } }">Editar producto </b-dropdown-item>
                <b-dropdown-divider></b-dropdown-divider>
                <b-dropdown-item @click="eliminarProducto(producto.id_producto)">Eliminar producto</b-dropdown-item>
              </b-dropdown>
            </td>
          </tr>
        </tbody>
      </table>
      <nav class="col-md-6 row contenedor-num-paginas">
        <ul class="pagination">
          <li class="page-item" v-if="pagination.current_page > 1">
            <a class="page-link" href @click.prevent="changePage(pagination.current_page - 1)">
              <span>Atras</span>
            </a>
          </li>
          <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
          </li>

          <li class="page-item" v-if="pagination.current_page < pagination.last_page">
            <a class="page-link" href @click.prevent="changePage(pagination.current_page + 1)">
              <span>Siguiente</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>
    <div class="d-flex justify-content-center row container-elements" v-else>
      <h3 class="col-lg-12">Espere mientras se están cargando los productos</h3>
      <div class="contenedor-carga">
        <!-- <bounce-loader color="#CD1317"></bounce-loader> -->
        <scale-loader color="#D1072E" :height="50" :width="8"></scale-loader>
      </div>
    </div>
    <!-- Mensajes de éxito y error -->
    <!-- Error -->
    <sweet-modal ref="modalError" modal-theme="dark" icon="error">
      <div v-html="mensajeError"></div>
      <b-button slot="button" variant="success" color="green" v-on:click="$refs.modalError.close()">Aceptar</b-button>
    </sweet-modal>
    <!-- Todo correcto -->
    <sweet-modal ref="modal" modal-theme="dark" icon="success">
      <div v-html="mensajeExito"></div>
      <b-button slot="button" variant="success" v-on:click="$refs.modal.close()">Aceptar</b-button>
    </sweet-modal>
  </section>
</template>

<script>
export default {
  mounted() {
    this.cargarTodosProductos(1);
  },
  data() {
    return {
      loading: false,
      productos: "",
      mensajeError: "",
      mensajeExito: "",
      // Gestión filtro
      filtroSeleccionado: "",
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      offset: 50,
    };
  },
  computed: {
    // Variables para trabajar con la paginación
    isActived() {
      // nos devuelve la página actual
      return this.pagination.current_page;
    },
    pagesNumber() {
      // Si no hay más páginas se devuelve un array vacío
      if (!this.pagination.to) {
        return [];
      }

      // Establecemos la página de inicio
      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      // Establecemos la página final
      var to = from + this.offset * 2;
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }

      // Calculamos el número de páginas totales
      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }

      return pagesArray;
    },
  },
  methods: {
    reiniciarFiltro() {
      this.filtroSeleccionado = "";
      this.cargarTodosProductos(1);
    },
    cargarTodosProductos(page) {
      this.loading = true;
      let url = "/pim/productos/cargar-productos?page=" + page + "&filtro=" + this.filtroSeleccionado;
      axios
        .get(url)
        .then((respuesta) => {
          this.productos = respuesta.data.productos.data;
          this.pagination = respuesta.data.pagination;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    eliminarProducto(id_producto) {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer eliminar este producto?",
      };

      let options = {
        okText: "Eliminar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        let url = "/pim/productos/eliminar-producto/" + id_producto;

        axios
          .delete(url)
          .then((respuesta) => {
            this.cargarTodosProductos();
            this.mensajeExito = "¡Se ha eliminado el producto con éxito!";
            this.$refs.modal.open();
          })
          .catch((error) => {
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al eliminar el producto!</p>";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
    changePage(page) {
      // Función para el cambio de página
      this.pagination.current_page = page;
      this.cargarTodosProductos(page);
    },
  },
};
</script>

<style>
.container-elements {
  text-align: center;
}
</style>
