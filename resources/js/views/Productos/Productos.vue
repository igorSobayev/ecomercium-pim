<template>
  <section class="body d-flex justify-content-center row">
    <div class="col-lg-10 pb-4 pl-0">
      <h2>Listado de todos los productos</h2>
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
    this.cargarTodosProductos();
  },
  data() {
    return {
      productos: "",
      mensajeError: "",
      mensajeExito: "",
    };
  },
  methods: {
    cargarTodosProductos() {
      let url = "/pim/productos/cargar-productos";
      axios
        .get(url)
        .then((respuesta) => {
          this.productos = respuesta.data;
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
            console.log(respuesta);
          })
          .catch((error) => {
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al eliminar el producto!</p>";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
  },
};
</script>

<style></style>
