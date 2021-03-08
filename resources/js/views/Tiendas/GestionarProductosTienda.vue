<template>
  <section class="body">
    <div class="container-elements d-flex justify-content-center row" v-if="!loading">
      <div class="col-lg-5">
        <div class="container-fluid border container-columna-listado">
          <h3>Productos en la tienda</h3>
          <hr />
          <!--  -->
          <div class="container-filtro-datos d-flex">
            <div class="input-group input-group-sm col-lg-5 mb-3">
              <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fas fa-filter"></i></span>
              <input type="text" class="form-control" v-model="filtroProductosTienda" placeholder="Filtro" />
            </div>
            <div class="col">
              <b-button variant="success" disabled size="sm">
                Seleccionados <b-badge variant="light">{{ numProductosTiendaSeleccionados }}</b-badge>
              </b-button>
              <b-button v-if="numProductosTiendaSeleccionados > 0" @click="limpiarProductosTiendaSeleccionados" variant="danger" title="Deseleccionar elementos" size="sm">
                <i class="fas fa-times"></i>
              </b-button>
            </div>
          </div>
          <!--  -->
          <div class="container-listado-elementos">
            <!-- En caso de que no estemos filtrando se enseñan todos los productos -->
            <b-form-group v-slot="{ ariaDescribedby }" v-if="productosTiendaFiltrados.length == 0">
              <b-form-checkbox-group
                v-model="productosTiendaSeleccionados"
                :options="productosTienda"
                button-variant="light"
                :aria-describedby="ariaDescribedby"
                stacked
                buttons
              ></b-form-checkbox-group>
            </b-form-group>
            <!-- En caso de estar filtrando productos -->
            <b-form-group v-slot="{ ariaDescribedby }" v-else>
              <b-form-checkbox-group
                v-model="productosTiendaSeleccionados"
                :options="productosTiendaFiltrados"
                button-variant="light"
                :aria-describedby="ariaDescribedby"
                stacked
                buttons
              ></b-form-checkbox-group>
            </b-form-group>
          </div>
          <!--  -->
        </div>
      </div>
      <div class="col-lg-1">
        <div class="container-fluid border container-botones-medio">
          <b-button variant="outline-primary" :disabled="numTodosProductosSeleccionados === 0" @click="addProductosTienda"><i class="fas fa-arrow-left"></i></b-button>
          <b-button variant="outline-info" :disabled="numProductosTiendaSeleccionados === 0" @click="removeProductosTienda"><i class="fas fa-arrow-right"></i></b-button>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="container-fluid border container-columna-listado">
          <h3>Todos los productos</h3>
          <hr />
          <div class="container-filtro-datos d-flex">
            <div class="input-group input-group-sm col-lg-5 mb-3">
              <span class="input-group-text" id="inputGroup-sizing-sm"><i class="fas fa-filter"></i></span>
              <input type="text" class="form-control" v-model="filtroTodosProductos" placeholder="Filtro" />
            </div>
            <div class="col">
              <b-button variant="success" disabled size="sm">
                Seleccionados <b-badge variant="light">{{ numTodosProductosSeleccionados }}</b-badge>
              </b-button>
              <b-button v-if="numTodosProductosSeleccionados > 0" @click="limpiarTodosProductosSeleccionados" variant="danger" title="Deseleccionar elementos" size="sm">
                <i class="fas fa-times"></i>
              </b-button>
            </div>
          </div>
          <div class="container-listado-elementos">
            <b-form-group v-slot="{ ariaDescribedby }" v-if="todosProductosFiltrados.length == 0">
              <b-form-checkbox-group
                v-model="todosProductosSeleccionados"
                :options="todosProductos"
                button-variant="light"
                :aria-describedby="ariaDescribedby"
                stacked
                buttons
              ></b-form-checkbox-group>
            </b-form-group>
            <b-form-group v-slot="{ ariaDescribedby }" v-else>
              <b-form-checkbox-group
                v-model="todosProductosSeleccionados"
                :options="todosProductosFiltrados"
                button-variant="light"
                :aria-describedby="ariaDescribedby"
                stacked
                buttons
              ></b-form-checkbox-group>
            </b-form-group>
          </div>
        </div>
      </div>
    </div>
    <div class="container-elements" v-else>
      <h3 class="col-lg-12">Espere mientras se están sincronizando los productos</h3>
      <div class="contenedor-carga">
        <!-- <bounce-loader color="#CD1317"></bounce-loader> -->
        <scale-loader color="#D1072E" :height="50" :width="8"></scale-loader>
      </div>
    </div>
    <!--  -->
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
    this.cargarProductosTienda();
    this.cargarTodosProductos();
  },
  data() {
    return {
      filtroProductosTienda: "",
      filtroTodosProductos: "",
      loading: false,
      productosTienda: [],
      productosTiendaFiltrados: [],
      productosTiendaSeleccionados: [],
      todosProductos: [],
      todosProductosFiltrados: [],
      todosProductosSeleccionados: [],
      mensajeError: "",
      mensajeExito: "",
    };
  },
  computed: {
    numProductosTiendaSeleccionados() {
      return this.productosTiendaSeleccionados.length;
    },
    numTodosProductosSeleccionados() {
      return this.todosProductosSeleccionados.length;
    },
  },
  watch: {
    filtroProductosTienda: function (newOne, oldOne) {
      this.productosTiendaFiltrados = this.productosTienda.filter((producto) => {
        return producto.text.includes(newOne);
      });
    },
    filtroTodosProductos: function (newOne, oldOne) {
      this.todosProductosFiltrados = this.todosProductos.filter((producto) => {
        return producto.text.includes(newOne);
      });
    },
  },
  methods: {
    limpiarProductosTiendaSeleccionados() {
      this.productosTiendaSeleccionados = [];
      this.mensajeExito = "¡Se han deseleccionado los productos con éxito!";
      this.$refs.modal.open();
    },
    limpiarTodosProductosSeleccionados() {
      this.todosProductosSeleccionados = [];
      this.mensajeExito = "¡Se han deseleccionado los productos con éxito!";
      this.$refs.modal.open();
    },
    addProductosTienda() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer añadir estos productos a esta tienda? Este proceso puede llevar un tiempo.",
      };

      let options = {
        okText: "Añadir",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        let url = "/pim/tiendas/add-productos-tienda/" + this.$route.params.id_tienda;
        this.loading = true;
        axios
          .post(url, {
            productos: this.todosProductosSeleccionados,
          })
          .then((res) => {
            this.cargarTodosProductos();
            this.cargarProductosTienda();
            this.mensajeExito = "¡Se han añadido los productos con éxito!";
            this.$refs.modal.open();
            this.loading = false;
          })
          .catch((error) => {
            console.log(error);
          });
      });
    },
    removeProductosTienda() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer quitar estos productos de esta tienda? Este proceso puede llevar un tiempo.",
      };

      let options = {
        okText: "Quitar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        let url = "/pim/tiendas/remove-productos-tienda/" + this.$route.params.id_tienda;

        axios
          .post(url, {
            productos: this.productosTiendaSeleccionados,
          })
          .then((res) => {
            this.cargarProductosTienda();
            this.cargarTodosProductos();
            this.mensajeExito = "¡Se han quitado los productos con éxito!";
            this.$refs.modal.open();
          })
          .catch((error) => {
            console.log(error);
          });
      });
    },
    cargarProductosTienda() {
      let url = "/pim/tiendas/cargar-productos-tienda/" + this.$route.params.id_tienda;

      axios
        .get(url)
        .then((respuesta) => {
          this.productosTienda = [];
          this.productosTiendaSeleccionados = [];
          this.productosTiendaFiltrados = [];

          respuesta.data.forEach((prod) => {
            this.productosTienda.push({
              text: prod.nombre_producto + " - " + prod.referencia,
              value: prod.id_tienda_producto,
              // activo: prod.activo,
            });
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
    cargarTodosProductos() {
      let url = "/pim/tiendas/cargar-todos-productos/" + this.$route.params.id_tienda;

      axios
        .get(url)
        .then((respuesta) => {
          this.todosProductos = [];
          this.todosProductosSeleccionados = [];
          this.todosProductosFiltrados = [];

          respuesta.data.forEach((prod) => {
            this.todosProductos.push({
              text: prod.nombre_producto + " - " + prod.referencia,
              value: prod.id_producto,
              // activo: prod.activo,
            });
          });
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.container-botones-medio {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  height: 100%;
  gap: 20px;
}

.container-columna-listado {
  padding-bottom: 30px;
}
</style>

<style lang="scss">
.container-listado-elementos {
  max-height: 600px;
  min-height: 500px;
  overflow: scroll;
  overflow-x: hidden;
  scrollbar-width: thin;

  fieldset div .btn-group-toggle {
    width: 100% !important;

    label {
      display: flex;
      justify-content: flex-start;
      border: 1px solid black;
    }

    .active {
      border: 1px solid black !important;
    }
  }

  &::-webkit-scrollbar {
    width: 12px;
  }

  &::-webkit-scrollbar-track {
    background: #f0f0f0; /* color of the tracking area */
  }

  &::-webkit-scrollbar-thumb {
    background-color: #a6a6a6; /* color of the scroll thumb */
    border-radius: 20px; /* roundness of the scroll thumb */
    border: 3px solid #f0f0f0; /* creates padding around scroll thumb */
  }
}

.container-elements {
  text-align: center;
}
</style>
