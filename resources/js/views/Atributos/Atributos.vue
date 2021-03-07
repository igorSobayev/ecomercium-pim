<template>
  <section class="body d-flex justify-content-center row">
    <div class="col-lg-10 pb-2 pl-0">
      <h2>Listado de atributos <b-button v-b-modal.modal-nuevo-tipo variant="outline-success">Añadir un tipo nuevo</b-button></h2>
    </div>
    <div class="col-lg-10 pb-4 pl-0 columna-segundaria-crear-producto" v-if="!loading">
      <div class="row">
        <div class="col-lg-6 tipo-atributo-card" v-for="(tipo, index) in atributosFinales" :key="index">
          <div class="d-flex justify-content-end mb-2">
            <b-button v-b-modal.modal-atributos variant="outline-dark">Añadir atributos</b-button>
          </div>
          <b-card header-tag="header" footer-tag="footer" class="mb-2 selector-atributos--card">
            <template #header>
              <h6 class="mb-0 titulo-atributo">
                {{ tipo.tipo_atributo }}
                <button type="button" v-b-modal.modal-editar-tipo class="btn btn-outline-primary btn-sm" @click="tipo_editar = tipo">
                  <i class="far fa-edit"></i>
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" @click="eliminarTipoAtributo(tipo.id_tipo_atributo)">
                  <i class="far fa-trash-alt"></i>
                </button>
              </h6>
            </template>
            <div class="row">
              <div class="atributo-col mb-1" v-for="(atributo, index2) in tipo.atributos" :key="index2">
                <div class="form-check">
                  <label class="form-check-label container-name-atributo mr-3" :for="tipo.tipo_atributo + '-' + index2 + '-' + atributo.id_atributo">
                    - <span v-if="tipo.tipo_atributo === 'color'" class="cuadro-color" :style="'background-color:' + atributo.color"></span> {{ atributo.valor_atributo }} |
                    <i class="far fa-edit fa-xs text-info" v-b-modal.modal-editar-atributos @click="id_atributoEditar = atributo.id_atributo"></i>
                    <i class="far fa-trash-alt fa-xs text-danger" @click="eliminarAtributo(atributo.id_atributo)"></i>
                  </label>
                </div>
              </div>
            </div>
          </b-card>
        </div>

        <!-- Fin checks -->
      </div>
    </div>

    <div class="card-body contenedor-carga" v-else>
      <h3 class="col-lg-12">Espere mientras se cargan los atributos</h3>
      <div class="contenedor-carga">
        <!-- <bounce-loader color="#CD1317"></bounce-loader> -->
        <scale-loader color="#D1072E" :height="50" :width="8"></scale-loader>
      </div>
    </div>

    <!-- Elementos ocultos -->
    <!-- Crear nuevo tipo -->
    <b-modal id="modal-nuevo-tipo" size="md" title="Añadir un nuevo tipo de atributo" :hide-footer="true">
      <div class="container">
        <form @submit.prevent="crearTipoAtributo()" method="POST">
          <div class="row">
            <p class="col-lg-12">Es recomendable que el nombre del tipo de atributo sea único para evitar conflictos. Por ejemplo, talla, color o dimensiones.</p>
            <div class="col-lg-12">
              <div class="">
                <div class="form-group">
                  <label for="tipo_atributo">Tipo atributo</label>
                  <input type="text" class="form-control" id="tipo_atributo" v-model="tipo_atributo_nuevo.tipo_atributo" placeholder="color" />
                </div>
              </div>
            </div>
            <!--  -->
            <div class="row col-lg-12">
              <div class="form-group col-lg-6 mt-3">
                <input type="submit" value="Crear" class="btn btn-success control-label text-sm-right pt-2" />
              </div>
            </div>
          </div>
        </form>
      </div>
    </b-modal>
    <!-- Editar tipo -->
    <b-modal id="modal-editar-tipo" size="md" :title="'Editando ' + tipo_editar.tipo_atributo" :hide-footer="true">
      <div class="container">
        <form @submit.prevent="guardarTipoEditado()" method="PUT">
          <div class="row">
            <p class="col-lg-12">Es recomendable que el nombre del tipo de atributo sea único para evitar conflictos. Por ejemplo, talla, color o dimensiones.</p>
            <div class="col-lg-12">
              <div class="">
                <div class="form-group">
                  <label for="tipo_atributo">Tipo atributo</label>
                  <input type="text" class="form-control" id="tipo_atributo" v-model="tipo_editar.tipo_atributo" placeholder="color" />
                </div>
              </div>
            </div>
            <!--  -->
            <div class="row col-lg-12">
              <div class="form-group col-lg-6 mt-3">
                <input type="submit" value="Guardar" class="btn btn-info control-label text-sm-right pt-2" />
              </div>
            </div>
          </div>
        </form>
      </div>
    </b-modal>
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
    <b-modal id="modal-atributos" size="lg" title="Añadir atributos" :hide-footer="true">
      <crearAtributo @atributoCreado="atributoCreado()" :atributoEditar="atributoEditar" />
    </b-modal>
    <!-- Modal editar atributo -->
    <b-modal id="modal-editar-atributos" size="lg" title="Editar atributo" :hide-footer="true">
      <form @submit.prevent="guardarAtributoEditado()" method="POST">
        <div class="row">
          <div class="col-lg-4">
            <div class="form-group">
              <label for="tipo_atributo">Tipo atributo <span class="text-danger">*</span></label>
              <select class="form-control" id="tipo_atributo" v-model="atributoEditar.tipo_atributo" required>
                <option value="" selected disabled>Seleccionar atributo</option>
                <option :value="tipo.tipo_atributo" v-for="(tipo, index) in tipos_atributos" :key="index">{{ tipo.tipo_atributo }}</option>
                <!-- <option value="color">Color</option> -->
              </select>
            </div>
          </div>
          <div class="col-lg-4" v-if="atributoEditar.tipo_atributo === 'color'">
            <div class="row-color">
              <div class="form-group">
                <label for="color_text">Color</label>
                <input type="text" class="form-control" id="color_text" v-model="atributoEditar.color" />
              </div>
              <div class="form-group col-lg-4">
                <input type="color" class="form-control" id="color" v-model="atributoEditar.color" />
              </div>
            </div>
          </div>
          <!--  -->
          <div class="col-lg-12" v-if="atributoEditar.tipo_atributo !== ''">
            <b-tabs content-class="mt-3" lazy>
              <b-tab :title="idioma.nombre" v-for="idioma in atributoEditar.idiomas" :key="idioma.id_idioma">
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
              <input type="submit" value="Guardar" class="btn btn-info control-label text-sm-right pt-2" />
            </div>
          </div>
        </div>
      </form>
    </b-modal>
    <!-- Editar atributo -->
  </section>
</template>

<script>
export default {
  mounted() {
    this.cargarTiposAtributos();
  },
  data() {
    return {
      loading: false,
      tipos_atributos: [],
      atributos: [],
      atributosFinales: [],
      tipo_atributo_nuevo: {
        id_tipo_atributo: "",
        tipo_atributo: "",
      },
      mensajeExito: "",
      mensajeError: "",
      tipo_editar: "",
      id_atributoEditar: "",
      atributoEditar: {
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
    };
  },
  watch: {
    id_atributoEditar: function (newVal, oldVal) {
      this.cargarIdiomas();
    },
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
    cargarDatosAtributoEditar() {
      let url = "/pim/atributos/datos-atributo-editar/" + this.id_atributoEditar;

      axios.get(url).then((respuesta) => {
        console.log(respuesta.data);
        this.atributoEditar.color = respuesta.data.atributo.color;
        this.atributoEditar.id_atributo = respuesta.data.atributo.id_atributo;
        this.atributoEditar.tipo_atributo = respuesta.data.atributo.tipo_atributo;

        this.todosIdiomas.forEach((idioma, i) => {
          respuesta.data.atributo.idiomas.forEach((atri_idioma) => {
            if (idioma.id_idioma === atri_idioma.id_idioma) {
              this.atributoEditar.idiomas[i].id_atributo_idioma = atri_idioma.id_atributo_idioma;
              this.atributoEditar.idiomas[i].id_atributo = atri_idioma.id_atributo;
              this.atributoEditar.idiomas[i].valor_atributo = atri_idioma.valor_atributo;
            }
          });
        });
      });
    },
    /**
     * Método para componer los datos del país para empezar a editarlo, esto hace las siguientes acciones:
     * añadir el id del país al objeto
     * añadir los idiomas al país
     */
    componerObjeto() {
      this.atributoEditar.idiomas = [];
      this.todosIdiomas.forEach((idioma) => {
        this.atributoEditar.idiomas.push({
          id_atributo_idioma: "",
          id_atributo: "",
          id_idioma: idioma.id_idioma,
          valor_atributo: "",
          nombre: idioma.nombre,
          icono_idioma: idioma.icono_idioma,
        });
      });

      this.cargarDatosAtributoEditar();
    },
    atributoCreado() {
      this.mensajeExito = "¡Se ha creado el atributo con éxito!";
      this.$refs.modal.open();
      this.cargarTiposAtributos();
      this.$bvModal.hide("modal-atributos");
    },
    cargarTiposAtributos() {
      this.loading = true;
      let url = "/pim/atributos/cargar-tipos-atributos";

      axios
        .get(url)
        .then((respuesta) => {
          this.tipos_atributos = [];
          this.tipos_atributos = [...respuesta.data];
          //   console.log(this.tipos_atributos);
          this.cargarAtributos();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    eliminarTipoAtributo(id_tipo_atributo) {
      let message = {
        title: "Confirmación",
        body:
          "¿Estas seguro de querer eliminar este tipo? Esto eliminará TODOS los atributos asociados y podría provocar errores en las combinaciones de algunos productos",
      };

      let options = {
        okText: "Eliminar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;

        let url = "/pim/atributos/eliminar-tipo-atributo/" + id_tipo_atributo;

        axios
          .delete(url)
          .then((respuesta) => {
            console.log(respuesta);
            this.mensajeExito = "¡Se ha eliminado el tipo de atributo con éxito!";
            this.$refs.modal.open();
            this.cargarTiposAtributos();
          })
          .catch((error) => {
            this.loading = false;
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al eliminar el tipo!";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
    eliminarAtributo(id_atributo) {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer eliminar este atributo? Esto podría provocar errores en las combinaciones de algunos productos",
      };

      let options = {
        okText: "Eliminar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;

        let url = "/pim/atributos/eliminar-atributo/" + id_atributo;

        axios
          .delete(url)
          .then((respuesta) => {
            console.log(respuesta);
            this.mensajeExito = "¡Se ha eliminado el atributo con éxito!";
            this.$refs.modal.open();
            this.cargarTiposAtributos();
          })
          .catch((error) => {
            this.loading = false;
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al eliminar el atributo!";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
    cargarAtributos() {
      let url = "/pim/atributos/cargar-atributos";

      axios
        .get(url)
        .then((respuesta) => {
          this.atributos = [];
          this.atributos = [...respuesta.data];
          this.componerAtributosFinales();
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    componerAtributosFinales() {
      this.atributosFinales = [];
      this.tipos_atributos.forEach((tipo_atributo, key) => {
        this.atributosFinales.push({
          id_tipo_atributo: tipo_atributo.id_tipo_atributo,
          tipo_atributo: tipo_atributo.tipo_atributo,
          atributos: [],
        });
        this.atributos.forEach((atributo) => {
          if (atributo.tipo_atributo === tipo_atributo.tipo_atributo) {
            this.atributosFinales[key].atributos.push(atributo);
          }
        });
      });
    },
    crearTipoAtributo() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer crear este tipo atributo nuevo?",
      };

      let options = {
        okText: "Crear",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;

        let url = "/pim/atributos/crear-tipo-atributo";

        axios
          .post(url, {
            tipo_atributo_nuevo: this.tipo_atributo_nuevo,
          })
          .then((respuesta) => {
            this.tipo_atributo_nuevo = {
              id_tipo_atributo: "",
              tipo_atributo: "",
            };
            this.mensajeExito = "¡Se ha creado el tipo de atributo nuevo con éxito!";
            this.$refs.modal.open();
            this.cargarTiposAtributos();
          })
          .catch((error) => {
            this.loading = false;
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al crear el atributo!";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
    guardarTipoEditado() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer guardar este tipo editado?",
      };

      let options = {
        okText: "Guardar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;

        let url = "/pim/atributos/editar-tipo-atributo";

        axios
          .put(url, {
            tipo_atributo_editado: this.tipo_editar,
          })
          .then((respuesta) => {
            this.mensajeExito = "¡Se ha editado el tipo de atributo con éxito!";
            this.$refs.modal.open();
            this.cargarTiposAtributos();
          })
          .catch((error) => {
            this.loading = false;
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al editar el atributo!";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
    guardarAtributoEditado() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer guardar este atributo?",
      };

      let options = {
        okText: "Guardar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;

        let url = "/pim/atributos/editar-atributo";

        axios
          .put(url, {
            atributo: this.atributoEditar,
          })
          .then((respuesta) => {
            this.mensajeExito = "¡Se ha editado el atributo con éxito!";
            this.$refs.modal.open();
            this.cargarTiposAtributos();
          })
          .catch((error) => {
            this.loading = false;
            this.mensajeError = "<p class='text-white mb-2'>¡Ha ocurrido un error al editar el atributo!";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.titulo-atributo {
  text-transform: capitalize;
  font-size: 24px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 12px;
}

.container-name-atributo {
  display: flex;
  align-items: center;
  gap: 5px;
  justify-content: center;
}

.row-color {
  display: flex;
  justify-content: center;
  align-items: flex-end;
}

i {
  cursor: pointer;
}
</style>
