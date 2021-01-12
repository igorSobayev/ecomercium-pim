<template>
  <div class="container">
    <form @submit.prevent="guardarAtributo()" method="POST">
      <div class="row">
        <div class="col-lg-4">
          <div class="form-group">
            <label for="tipo_atributo">Tipo atributo <span class="text-danger">*</span></label>
            <select class="form-control" id="tipo_atributo" v-model="atributo.tipo_atributo" required>
              <option value="" selected disabled>Seleccionar atributo</option>
              <option value="talla">Talla</option>
              <option value="color">Color</option>
            </select>
          </div>
        </div>
        <div class="col-lg-4" v-if="atributo.tipo_atributo === 'color'">
          <div class="row-color">
            <div class="form-group">
              <label for="color_text">Color</label>
              <input type="text" class="form-control" id="color_text" v-model="atributo.color" />
            </div>
            <div class="form-group col-lg-4">
              <input type="color" class="form-control" id="color" v-model="atributo.color" />
            </div>
          </div>
        </div>
        <!--  -->
        <div class="col-lg-12" v-if="atributo.tipo_atributo !== ''">
          <b-tabs content-class="mt-3" lazy>
            <b-tab :title="idioma.nombre" v-for="idioma in atributo.idiomas" :key="idioma.id_idioma">
              <div class="container-fluid">
                <h3>
                  Atributo en {{ idioma.nombre }}
                  <img :src="idioma.icono_idioma" width="30" height="30" :alt="'Bandera ' + idioma.nombre" />
                </h3>
              </div>
              <!-- Titulo y descripcion  -->
              <div class="col-lg-12 row">
                <!-- Nombre producto -->
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="control-label text-sm-right pt-2"> Valor atributo </label>
                    <input
                      type="text"
                      :name="'valor_atributo-' + idioma.id_idioma"
                      :id="'valor_atributo-' + idioma.id_idioma"
                      class="form-control"
                      v-model="idioma.valor_atributo"
                      placeholder="Talla M"
                    />
                  </div>
                </div>
              </div>
            </b-tab>
          </b-tabs>
        </div>
        <!--  -->
        <div class="row col-lg-12">
          <div class="form-group col-lg-6 mt-3">
            <input type="submit" value="Guardar atributo" class="btn btn-success control-label text-sm-right pt-2" />
          </div>
        </div>
      </div>
    </form>
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
  </div>
</template>

<script>
export default {
  data() {
    return {
      atributo: {
        id_atributo: "",
        tipo_atributo: "",
        color: "",
        idiomas: [
          {
            id_atributo_idioma: "",
            id_atributo: "",
            id_idioma: "",
            valor_atributo: "",
            nombre: "",
            icono_idioma: "",
          },
        ],
      },
      todosIdiomas: [],
      mensajeError: "",
      mensajeExito: "",
      loading: false,
    };
  },
  mounted() {
    this.cargarIdiomas();
  },
  methods: {
    /**
     * Método para cargar los datos de los idiomas del país actual
     */
    cargarIdiomas() {
      let url = "/pim/idiomas/cargar-idiomas";

      axios
        .get(url)
        .then((respuesta) => {
          this.todosIdiomas = respuesta.data;
          this.componerObjeto();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    /**
     * Método para componer los datos del país para empezar a editarlo, esto hace las siguientes acciones:
     * añadir el id del país al objeto
     * añadir los idiomas al país
     */
    componerObjeto() {
      this.atributo.idiomas = [];
      this.todosIdiomas.forEach((idioma) => {
        this.atributo.idiomas.push({
          id_atributo_idioma: "",
          id_atributo: "",
          id_idioma: idioma.id_idioma,
          valor_atributo: "",
          nombre: idioma.nombre,
          icono_idioma: idioma.icono_idioma,
        });
      });
    },
    guardarAtributo() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer crear este atributo?",
      };

      let options = {
        okText: "Crear",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;

        let url = "/pim/atributos/crear-atributo";

        axios
          .post(url, {
            atributo: this.atributo,
          })
          .then((respuesta) => {
            this.$emit("atributoCreado");
          })
          .catch((error) => {
            this.loading = false;
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al crear el atributo!";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.row-color {
  display: flex;
  justify-content: center;
  align-items: flex-end;
}
</style>
