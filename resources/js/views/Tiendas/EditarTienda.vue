<template>
  <section class="body">
    <div class="col-lg-8">
      <section class="card mb-4">
        <header class="card-header">
          <h2 class="card-title">
            Editar tienda
            <button type="button" @click="guardarTienda()" class="btn btn-success btn-sm">Guardar tienda</button>
          </h2>
        </header>
        <div class="card-body" v-if="!loading">
          <form @submit.prevent="guardarTienda()" method="POST">
            <div class="row">
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="nombre_tienda">Nombre tienda <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="tienda.nombre_tienda" id="nombre_tienda" required />
                </div>
              </div>
              <!--  -->
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="tipo_tienda">Tipo tienda <span class="text-danger">*</span></label>
                  <select class="form-control" v-model="tienda.tipo_tienda" id="tipo_tienda" required>
                    <option value="Prestashop">Prestashop</option>
                    <option value="Amazon">Amazon</option>
                  </select>
                </div>
              </div>
              <!--  -->
              <div class="col-lg-4 container-checkbox">
                <div class="form-group form-check">
                  <input type="checkbox" v-model="tienda.debug" class="form-check-input" id="debug" />
                  <label class="form-check-label" for="debug">Modo debug</label>
                </div>
              </div>
            </div>
            <!--  -->
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="api_key">Api KEY <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="tienda.api_key" id="api_key" required />
                </div>
              </div>
              <!--  -->
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="store_root">Store root <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="tienda.store_root" id="store_root" required />
                </div>
              </div>
            </div>
            <button class="btn btn-success" type="submit">Guardar</button>
          </form>
        </div>
        <div class="card-body contenedor-carga" v-else>
          <h3 class="col-lg-12">Espere mientras se terminan de actualizar los datos de la tienda</h3>
          <div class="contenedor-carga">
            <!-- <bounce-loader color="#CD1317"></bounce-loader> -->
            <scale-loader color="#D1072E" :height="50" :width="8"></scale-loader>
          </div>
        </div>
      </section>
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
    this.loading = true;
    this.cargarDatosTienda();
  },
  data() {
    return {
      loading: false,
      tienda: {
        id_tienda: "",
        nombre_tienda: "",
        tipo_tienda: "",
        api_key: "",
        store_root: "",
        debug: false,
      },
      mensajeError: "",
      mensajeExito: "",
    };
  },
  methods: {
    cargarDatosTienda() {
      let url = "/pim/tiendas/cargar-datos-tienda/" + this.$route.params.id_tienda;

      axios
        .get(url)
        .then((respuesta) => {
          this.tienda = respuesta.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    guardarTienda() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer actualizar los datos de esta tienda?.",
      };

      let options = {
        okText: "Guardar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;
        let url = "/pim/tiendas/guardar-tienda-editada";

        axios
          .post(url, {
            tienda: this.tienda
          })
          .then((res) => {
            this.cargarDatosTienda();
            this.mensajeExito = "¡Se ha actualizado la tienda con éxito!";
            this.$refs.modal.open();
          })
          .catch((error) => {
            console.log(error);
          });
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.body {
  justify-content: center;
  width: 100%;
  display: flex;
}

.container-checkbox {
  display: flex;
  align-items: flex-end;
  padding-bottom: 9px;
}
</style>

<style lang="scss">
.container-elements {
  text-align: center;
}
</style>
