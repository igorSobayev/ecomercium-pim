<template>
  <section class="body">
    <div class="col-md-12">
      <section class="card mb-4">
        <header class="card-header">
          <h2 class="card-title">
            <span v-if="!editando">Crear</span> <span v-else>Editando</span> producto
            <button type="button" class="btn btn-success btn-sm">Guardar producto</button>
          </h2>
        </header>
        <div class="card-body" v-if="!loading">
          <form @submit.prevent="guardarProducto()" method="POST">
            <div class="container-crear-producto">
              <div class="row col-lg-12 d-flex container-principal">
                <b-tabs card pills content-class="mt-3" class="border" lazy>
                  <b-tab title="Ajustas básicos">
                    <div class="row col-lg-12 d-flex container-principal">
                      <div class="col-lg-9 columna-principal-crear-producto">
                        <!--  -->
                        <label for="img_original" class="container-add-imagenes-producto mb-3">
                          <p>Añade tus imagenes aquí <i class="far fa-images"></i></p>
                          <input type="file" name="img_original" id="img_original" ref="files" class="form-control" @change="actualizarVariosArchivos()" multiple hidden />
                        </label>
                        <!--  -->
                        <div class="container-media-preview col-lg-12 mb-4 mt-1" v-if="media_multiple.length > 0">
                          <p class="w-100">
                            Actualmente tienes añadidas <span class="text-success">{{ media_multiple.length }}</span> imágenes. Puedes añadir más desde el apartado
                            principal o eliminar las actuales pulsando la X en cada foto.
                          </p>
                          <div v-for="(media, index) in media_multiple" :key="index" class="media-preview">
                            <i class="fas fa-times" @click="removeMedia(index)"></i>
                            <img :src="urlPreviewArchivo(media.url_img)" />
                          </div>
                        </div>
                        <!--  -->
                        <div class="container-media-preview col-lg-12 mb-4 mt-1" v-if="media_editar_preview.length > 0">
                          <hr class="w-100" />
                          <p class="w-100">
                            Actualmente tienes cargadas <span class="text-success">{{ media_editar_preview.length }}</span> imágenes en este producto. Puedes añadir más
                            desde el apartado principal o eliminar las actuales pulsando la X en cada foto (estas fotos no se eliminaran hasta que se guarde el producto
                            actualizado).
                          </p>
                          <div v-for="(media, index) in media_editar_preview" :key="index" class="media-preview">
                            <i class="fas fa-times" @click="removeMediaEdited(index)"></i>
                            <img :src="media.url_img" />
                          </div>
                        </div>
                        <!--  -->
                        <b-tabs content-class="mt-3" class="border" lazy>
                          <b-tab :title="idioma.nombre" v-for="idioma in producto.idiomas" :key="idioma.id_idioma">
                            <div class="container-fluid">
                              <h3>
                                Producto en {{ idioma.nombre }}
                                <img :src="idioma.icono_idioma" width="30" height="30" :alt="'Bandera ' + idioma.nombre" />
                              </h3>
                            </div>
                            <!-- Titulo y descripcion  -->
                            <div class="col-lg-12 row">
                              <!-- Nombre producto -->
                              <div class="col-lg-12">
                                <div class="form-group">
                                  <label class="control-label text-sm-right pt-2"> Nombre producto <span class="text-danger">*</span> </label>
                                  <input
                                    type="text"
                                    :name="'nombre_producto-' + idioma.id_idioma"
                                    :id="'nombre_producto-' + idioma.id_idioma"
                                    class="form-control"
                                    v-model="idioma.nombre_producto"
                                    placeholder="Tinte para zapatos de cuero"
                                  />
                                </div>
                              </div>
                              <div class="col-lg-12">
                                <b-tabs pills card vertical>
                                  <b-tab title="Descripción corta" active>
                                    <vue-editor v-model="idioma.descr_corta" :editorToolbar="customToolbar"></vue-editor>
                                  </b-tab>
                                  <b-tab title="Descripción larga">
                                    <vue-editor v-model="idioma.descr_larga" :editorToolbar="customToolbar"></vue-editor>
                                  </b-tab>
                                </b-tabs>
                              </div>
                              <!-- Fin Nombre producto -->
                            </div>
                            <!-- fin tit y descri -->
                            <hr />
                            <div class="container-fluid">
                              <h3>Datos SEO</h3>
                            </div>
                            <!-- contenedor datos seo  -->
                            <div class="col-lg-12 row">
                              <!-- Título seo -->
                              <div class="col-lg-6">
                                <div class="form-group">
                                  <label class="control-label text-sm-right pt-2"> Título SEO </label>
                                  <input
                                    type="text"
                                    :name="'tit_seo-' + idioma.id_idioma"
                                    :id="'tit_seo-' + idioma.id_idioma"
                                    class="form-control"
                                    v-model="idioma.tit_seo"
                                    placeholder="Tinte para zapatos"
                                  />
                                </div>
                              </div>
                              <!-- Fin título seo -->
                              <!-- Slug -->
                              <div class="col-lg-6">
                                <div class="form-group">
                                  <label class="control-label text-sm-right pt-2"> URL amigable <span class="text-danger">*</span> </label>
                                  <input
                                    type="text"
                                    :name="'slug-' + idioma.id_idioma"
                                    :id="'slug-' + idioma.id_idioma"
                                    class="form-control"
                                    v-model="idioma.slug"
                                    placeholder="tinte-zapatos"
                                  />
                                </div>
                              </div>
                              <!-- Fin slug -->
                              <!-- Descripcion seo -->
                              <div class="col-lg-6">
                                <div class="form-group">
                                  <label class="control-label text-sm-right pt-2"> Descripción seo </label>

                                  <textarea
                                    :name="'descr_seo-' + idioma.id_idioma"
                                    :id="'descr_seo-' + idioma.id_idioma"
                                    v-model="idioma.descr_seo"
                                    class="form-control"
                                    cols="15"
                                    rows="4"
                                    placeholder="Descripción SEO"
                                  ></textarea>
                                </div>
                              </div>
                              <!-- Fin descripcion seo-->
                            </div>
                            <!-- fin datos seo -->
                          </b-tab>
                        </b-tabs>
                      </div>
                      <div class="col-lg-3 columna-segundaria-crear-producto">
                        <div class="col-lg-12 row">
                          <!-- Checks -->
                          <div class="col-lg-12">
                            <h5>Combinaciones</h5>
                            <div class="form-check">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="producto_combinacion"
                                id="producto_simple"
                                v-model="producto.producto_combinacion"
                                :value="0"
                              />
                              <label class="form-check-label" for="producto_simple"> Producto simple </label>
                            </div>
                            <div class="form-check">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="producto_combinacion"
                                id="producto_combinado"
                                v-model="producto.producto_combinacion"
                                :value="1"
                              />
                              <label class="form-check-label" for="producto_combinado"> Producto con combinaciones </label>
                            </div>
                          </div>

                          <hr class="col-lg-12" />

                          <div>
                            <div class="input-group flex-nowrap mb-2">
                              <span class="input-group-text" id="addon-wrapping">Referencia</span>
                              <input type="text" class="form-control" placeholder="RDSW1233SW" v-model="producto.referencia" />
                            </div>
                            <div class="input-group flex-nowrap mb-2">
                              <span class="input-group-text" id="addon-wrapping">Marca</span>
                              <input type="text" class="form-control" placeholder="Grease" v-model="producto.marca" />
                            </div>
                            <div class="input-group flex-nowrap mb-2">
                              <span class="input-group-text" id="addon-wrapping">EAN13</span>
                              <input type="text" class="form-control" placeholder="123125590392" v-model="producto.ean13" />
                            </div>
                            <div class="input-group flex-nowrap mb-2">
                              <span class="input-group-text" id="addon-wrapping">Código arancel</span>
                              <input type="text" class="form-control" placeholder="123125590392" v-model="producto.cod_arancel" />
                            </div>
                            <div class="input-group flex-nowrap mb-2">
                              <span class="input-group-text" id="addon-wrapping">Peso</span>
                              <input type="text" class="form-control" placeholder="125ml" v-model="producto.peso" />
                            </div>
                            <div class="input-group flex-nowrap mb-2">
                              <span class="input-group-text" id="addon-wrapping">Precio sin iva</span>
                              <input type="number" min="0" step=".01" v-model="producto.precio_sin_iva" class="form-control" />
                            </div>
                            <div class="input-group flex-nowrap mb-2">
                              <span class="input-group-text" id="addon-wrapping">Precio coste</span>
                              <input type="number" min="0" step=".01" v-model="producto.precio_coste" class="form-control" />
                            </div>
                            <div class="input-group flex-nowrap">
                              <span class="input-group-text" id="addon-wrapping">Stock</span>
                              <input type="number" min="0" v-model="producto.cantidad" class="form-control" />
                            </div>
                            <hr class="col-lg-12" />
                            <div class="col-lg-12">
                              <div class="form-check form-check-inline mr-4">
                                <input class="form-check-input" type="checkbox" id="producto_activo" v-model="producto.activo" />
                                <label class="form-check-label" for="producto_activo">Producto activo</label>
                              </div>
                            </div>
                            <hr class="col-lg-12" />
                          </div>
                          <!-- Fin checks -->
                          <!-- Boton guardar -->
                          <div class="row col-lg-12">
                            <div class="form-group col-lg-6 mt-3">
                              <input type="submit" value="Guardar producto" class="btn btn-success control-label text-sm-right pt-2" />
                            </div>
                          </div>
                          <!-- Fin boton guardar -->
                        </div>
                      </div>
                    </div>
                  </b-tab>
                  <b-tab title="Combinaciones" v-if="producto.producto_combinacion">
                    <div class="row col-lg-12 d-flex container-principal">
                      <!-- Combinaciones -->
                      <div class="col-lg-9 columna-principal-crear-producto">
                        <div class="row">
                          <div class="col-lg-12">
                            <b-card title="Administración de combinaciones del producto">
                              <b-card-text>
                                Para añadir combinaciones, primero necesitas crear los atributos y valores apropiados desde el panel lateral derecho localizado en esta
                                misma página. Cuando hayas terminado, puedes seleccionar los atributos deseados (tales como "talla" o "color") y sus valores respectivos
                                ("XS", "rojo", "todas", etc.) en la columna derecha. A continuación, haz clic en "Generar combinaciones": ¡automáticamente se crearán todas
                                las combinaciones para ti! Solo faltará que edites los datos de cada una de ellas, por defecto la referencia y el ean13 de la combinación
                                será la del producto con un número añadido al final. La foto será la principal del producto.
                              </b-card-text>
                            </b-card>
                          </div>
                          <!--  -->
                          <div class="col-lg-12 d-flex mt-3">
                            <div class="col-lg-8">
                              <div class="col-lg-12">
                                <div class="form-group">
                                  <!-- <input type="text" name="combinaciones" id="combinaciones" class="form-control" placeholder="Tinte para zapatos" /> -->
                                  <p class="mb-0">
                                    <u><strong>Atributos seleccionados actualmente:</strong></u>
                                  </p>
                                  <div class="container-atributos-seleccionados">
                                    <b-badge
                                      variant="primary"
                                      class="mr-1"
                                      v-for="(sel_talla, index) in atributos_talla_seleccionados"
                                      :key="index + '-' + sel_talla.id_atributo"
                                    >
                                      {{ sel_talla.valor_atributo }} &nbsp; <i class="fas fa-times" @click="quitarTallaSeleccionada(index)"></i>
                                    </b-badge>
                                    <b-badge
                                      variant="success"
                                      class="mr-1"
                                      v-for="(sel_color, index) in atributos_color_seleccionados"
                                      :key="index + '-' + sel_color.id_atributo"
                                    >
                                      {{ sel_color.valor_atributo }} &nbsp; <i class="fas fa-times" @click="quitarColorSeleccionado(index)"></i>
                                    </b-badge>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-4"><button type="button" class="btn btn-outline-success" @click="generarCombinaciones">Generar combinaciones</button></div>
                          </div>
                          <!--  -->
                          <div class="col-lg-12 mt-3">
                            <table class="table">
                              <thead>
                                <tr>
                                  <th scope="col">Nombre</th>
                                  <th scope="col">Precio</th>
                                  <th scope="col">Referencia</th>
                                  <th scope="col">Stock</th>
                                  <th scope="col">EAN13</th>
                                  <th scope="col">Cod. arancel</th>
                                  <th scope="col" class="col-table-right">Peso</th>
                                  <th scope="col" class="col-table-right">Acciones</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="(combinacion, index) in combinaciones_actuales" :key="index">
                                  <td class="nombre-combinacion">{{ combinacion.nombre_combinacion }}</td>
                                  <td><input type="number" min="0" step=".01" name="precio_combinacion" v-model="combinacion.precio" class="form-control" /></td>
                                  <td><input type="text" name="referencia" v-model="combinacion.referencia" class="form-control" /></td>
                                  <td><input type="number" min="0" name="stock" v-model="combinacion.stock" class="form-control" /></td>
                                  <td><input type="text" name="ean13" v-model="combinacion.ean13" class="form-control" /></td>
                                  <td><input type="text" name="ean13" v-model="combinacion.cod_arancel" class="form-control" /></td>
                                  <td><input type="text" name="peso" v-model="combinacion.peso" class="form-control" /></td>
                                  <td><button type="button" class="btn btn-danger" @click="eliminarCombinacion(index)">X</button></td>
                                </tr>
                              </tbody>
                            </table>
                            <!--  -->
                            <div v-if="editando && this.combinaciones_producto_nuevas.length > 0"><strong>Combinaciones nuevas</strong></div>
                            <table class="table">
                              <tbody>
                                <tr v-for="(combinacion, index) in combinaciones_producto_nuevas" :key="index">
                                  <td class="nombre-combinacion">{{ combinacion.nombre_combinacion }}</td>
                                  <td><input type="number" min="0" step=".01" name="precio_combinacion" v-model="combinacion.precio" class="form-control" /></td>
                                  <td><input type="text" name="referencia" v-model="combinacion.referencia" class="form-control" /></td>
                                  <td><input type="number" min="0" name="stock" v-model="combinacion.stock" class="form-control" /></td>
                                  <td><input type="text" name="ean13" v-model="combinacion.ean13" class="form-control" /></td>
                                  <td><input type="text" name="ean13" v-model="combinacion.cod_arancel" class="form-control" /></td>
                                  <td><input type="text" name="peso" v-model="combinacion.peso" class="form-control" /></td>
                                  <td><button type="button" class="btn btn-danger" @click="eliminarCombinacionNueva(index)">X</button></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <!-- Atributos a escoger -->
                      <div class="col-lg-3 columna-segundaria-crear-producto">
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="d-flex justify-content-end mb-2">
                              <b-button v-b-modal.modal-atributos variant="outline-dark">Añadir atributos</b-button>
                              <b-modal id="modal-atributos" size="lg" title="Añadir atributos" :hide-footer="true">
                                <crearAtributo @atributoCreado="atributoCreado()" />
                              </b-modal>
                            </div>
                            <b-card-group deck class="selector-atributos">
                              <b-card header-tag="header" footer-tag="footer" class="mb-2 selector-atributos--tallas-card selector-atributos--card">
                                <template #header>
                                  <h6 class="mb-0">Tallas</h6>
                                </template>
                                <div class="row">
                                  <div class="col-lg-6 atributo-col atributo-col--talla mb-1" v-for="(talla, index) in atributos_tallas" :key="index">
                                    <div class="form-check">
                                      <input
                                        class="form-check-input"
                                        type="checkbox"
                                        v-model="atributos_talla_seleccionados"
                                        :value="{ id_atributo: talla.id_atributo, valor_atributo: talla.valor_atributo }"
                                        :id="'talla-' + index + '-' + talla.id_atributo"
                                      />
                                      <label class="form-check-label" :for="'talla-' + index + '-' + talla.id_atributo"> {{ talla.valor_atributo }} </label>
                                    </div>
                                  </div>
                                </div>
                              </b-card>

                              <b-card header-tag="header" footer-tag="footer" class="selector-atributos--colores-card selector-atributos--card">
                                <template #header>
                                  <h6 class="mb-0">Colores</h6>
                                </template>
                                <div class="row">
                                  <div class="col-lg-6 atributo-col atributo-col--color mb-1" v-for="(color, index) in atributos_colores" :key="index">
                                    <div class="form-check">
                                      <input
                                        class="form-check-input"
                                        type="checkbox"
                                        v-model="atributos_color_seleccionados"
                                        :value="{ id_atributo: color.id_atributo, valor_atributo: color.valor_atributo }"
                                        :id="'color-' + index + '-' + color.id_atributo"
                                      />
                                      <label class="form-check-label" :for="'color-' + index + '-' + color.id_atributo">
                                        <span class="cuadro-color" :style="'background-color:' + color.color"></span> {{ color.valor_atributo }}
                                      </label>
                                    </div>
                                  </div>
                                </div>
                              </b-card>
                            </b-card-group>
                          </div>

                          <!-- Fin checks -->
                          <!-- Boton guardar -->
                          <div class="row col-lg-12">
                            <div class="form-group col-lg-6 mt-3">
                              <input type="submit" value="Guardar producto" class="btn btn-success control-label text-sm-right pt-2" />
                            </div>
                          </div>
                          <!-- Fin boton guardar -->
                        </div>
                      </div>
                    </div></b-tab
                  >
                </b-tabs>
                <!-- Boton guardar -->
                <div class="row col-lg-12">
                  <div class="form-group col-lg-6 mt-3">
                    <input type="submit" value="Guardar producto" class="btn btn-success control-label text-sm-right pt-2" />
                  </div>
                </div>
                <!-- Fin boton guardar -->
              </div>
            </div>
          </form>
        </div>
        <div class="card-body contenedor-carga" v-else>
          <h3 class="col-lg-12">Espere mientras termina de cargar su producto</h3>
          <div class="contenedor-carga">
            <!-- <bounce-loader color="#CD1317"></bounce-loader> -->
            <scale-loader color="#D1072E" :height="50" :width="8"></scale-loader>
          </div>
        </div>
      </section>
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
import { VueEditor } from "vue2-editor";

export default {
  components: {
    VueEditor,
  },
  mounted() {
    this.startView();
  },
  props: {},
  data() {
    return {
      customToolbar: [
        ["bold", "italic", "underline", "strike"],
        [{ list: "ordered" }, { list: "bullet" }],
        [
          {
            align: "",
          },
          {
            align: "center",
          },
          {
            align: "right",
          },
          {
            align: "justify",
          },
        ],
        ["blockquote"],
        ["link", "code-block"],
        [
          {
            color: [],
          },
          {
            background: [],
          },
        ],
      ],
      producto: {
        id_producto: "",
        activo: true,
        referencia: "",
        marca: "",
        precio_sin_iva: "",
        precio_coste: "",
        cantidad: "",
        producto_combinacion: 0,
        ean13: "",
        peso: "",
        cod_arancel: "",
        idiomas: [
          {
            id_producto_idioma: "",
            id_producto: "",
            id_idioma: "",
            nombre_producto: "",
            descr_corta: "",
            descr_larga: "",
            tit_seo: "",
            descr_seo: "",
            slug: "",
            nombre: "",
            icono_idioma: "",
          },
        ],
      },
      // variables
      todosIdiomas: [],
      productoBruto: [],
      archivos_pesados: [],
      media_multiple: [],
      media_editar_preview: [],
      media_editar_preview_eliminadas: [],
      atributos_tallas: [],
      atributos_colores: [],
      atributos_talla_seleccionados: [],
      atributos_color_seleccionados: [],
      combinaciones_producto_nuevas: [],
      combinaciones_actuales: [],
      combinaciones_eliminadas: [],
      // Mensajes y arrays
      mensajeError: "",
      mensajeExito: "",
      loading: false,
      editando: false,
    };
  },
  methods: {
    startView() {
      if (this.$route.params.id_producto != undefined) {
        this.loading = true;
        this.editando = true;
      }
      this.cargarIdiomas();
      this.cargarAtributos();
    },
    eliminarCombinacion(index) {
      this.combinaciones_eliminadas.push(this.combinaciones_actuales[index].id_combinacion);
      this.combinaciones_actuales.splice(index, 1);
    },
    eliminarCombinacionNueva(index) {
      this.combinaciones_producto_nuevas.splice(index, 1);
    },
    generarCombinaciones() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer generar estas combinaciones?",
      };

      let options = {
        okText: "Generar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        // Se añaden las combinaciones dependiendo de lo que tengamos seleccionado
        // el primero es para tallas y colores
        // el segundo para tallas
        // el tercero para colores
        if (this.atributos_talla_seleccionados.length > 0 && this.atributos_color_seleccionados.length > 0) {
          this.atributos_talla_seleccionados.forEach((talla) => {
            this.atributos_color_seleccionados.forEach((color) => {
              this.combinaciones_producto_nuevas.push({
                nombre_combinacion: "Color - " + color.valor_atributo + ", Talla - " + talla.valor_atributo,
                precio: this.producto.precio_sin_iva === "" ? "" : this.producto.precio_sin_iva,
                referencia: "",
                stock: this.producto.cantidad === "" ? 1 : this.producto.cantidad,
                ean13: "",
                cod_arancel: "",
                peso: this.producto.peso === "" ? "" : this.producto.peso,
                id_atributo: [talla.id_atributo, color.id_atributo],
              });
            });
          });
        } else if (this.atributos_talla_seleccionados.length > 0 && this.atributos_color_seleccionados.length === 0) {
          this.atributos_talla_seleccionados.forEach((talla) => {
            this.combinaciones_producto_nuevas.push({
              nombre_combinacion: "Talla - " + talla.valor_atributo,
              precio: this.producto.precio_sin_iva === "" ? "" : this.producto.precio_sin_iva,
              referencia: "",
              stock: this.producto.cantidad === "" ? 1 : this.producto.cantidad,
              ean13: "",
              cod_arancel: "",
              peso: this.producto.peso === "" ? "" : this.producto.peso,
              id_atributo: [talla.id_atributo],
            });
          });
        } else if (this.atributos_color_seleccionados.length > 0 && this.atributos_talla_seleccionados.length === 0) {
          this.atributos_color_seleccionados.forEach((color) => {
            this.combinaciones_producto_nuevas.push({
              nombre_combinacion: "Color - " + color.valor_atributo,
              precio: this.producto.precio_sin_iva === "" ? "" : this.producto.precio_sin_iva,
              referencia: "",
              stock: this.producto.cantidad === "" ? 1 : this.producto.cantidad,
              ean13: "",
              cod_arancel: "",
              peso: this.producto.peso === "" ? "" : this.producto.peso,
              id_atributo: [color.id_atributo],
            });
          });
        }

        this.atributos_color_seleccionados = [];
        this.atributos_talla_seleccionados = [];
      });
    },
    quitarTallaSeleccionada(index) {
      this.atributos_talla_seleccionados.splice(index, 1);
    },
    quitarColorSeleccionado(index) {
      this.atributos_color_seleccionados.splice(index, 1);
    },
    atributoCreado() {
      this.cargarAtributos();
      this.loading = false;
      this.mensajeExito = "¡Se ha creado el atributo con éxito!";
      this.$refs.modal.open();
      this.$bvModal.hide("modal-atributos");
    },
    cargarAtributos() {
      let url = "/pim/atributos/cargar-atributos";

      axios
        .get(url)
        .then((respuesta) => {
          this.atributos_colores = respuesta.data.filter((atributo) => atributo.tipo_atributo === "color");
          this.atributos_tallas = respuesta.data.filter((atributo) => atributo.tipo_atributo === "talla");
          console.log(respuesta.data);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    /**
     * Método para crear varios archivos, envía la petición a otro método
     */
    crearVariosArchivos(id_producto) {
      let url = "/pim/media/add-media-producto";

      // Creo el formData
      let file = new FormData();

      // añado los datos al formdata

      this.media_multiple.forEach((media) => {
        file.append("file[]", media.url_img);
        file.append("id_producto[]", id_producto);
      });

      axios
        .post(url, file)
        .then((respuesta) => {
          this.borrarDatos();
          this.loading = false;
          this.mensajeExito = "¡Se ha guardado el producto con éxito!";
          this.$refs.modal.open();
        })
        .catch((error) => {
          this.loading = false;
          this.mensajeError = "¡Se ha producido un error al guardar el archivo!";
          this.$refs.modalError.open();
          console.log(error);
        });
    },
    removeMedia(id) {
      this.media_multiple.splice(id, 1);
    },
    removeMediaEdited(id) {
      this.media_editar_preview_eliminadas.push(this.media_editar_preview[id].id_producto_img);
      this.media_editar_preview.splice(id, 1);
    },
    /**
     * Método para cargar varios archivos y dejarlos preparados para enviar
     */
    actualizarVariosArchivos() {
      let todosArchivos = this.$refs.files.files;

      // El tipo de archivo será el del primer elemento

      for (let i = 0; i < todosArchivos.length; i++) {
        var file_extension = "." + todosArchivos[i].name.slice(((todosArchivos[i].name.lastIndexOf(".") - 1) >>> 0) + 2);

        if (
          todosArchivos[i].type == "image/png" ||
          todosArchivos[i].type == "image/jpeg" ||
          todosArchivos[i].type == "image/gif" ||
          todosArchivos[i].type == "image/svg+xml"
        ) {
          if (todosArchivos[i].size >= 2000000) {
            this.archivos_pesados.push(todosArchivos[i]);
          } else {
            this.media_multiple.push({
              id_producto_img: "",
              id_producto: "",
              url_img: todosArchivos[i],
              img_principal: "",
            });
          }
        }
      }
    },
    /**
     *  Preview archivo
     */
    urlPreviewArchivo(file) {
      return URL.createObjectURL(file);
    },
    /**
     * Método para cargar los datos del producto que vamos a editar
     */
    cargarProductoEditar(id_producto) {
      let url = "/pim/productos/cargar-datos-producto-editar/" + this.$route.params.id_producto;

      axios
        .get(url)
        .then((respuesta) => {
          console.log(respuesta);
          this.productoBruto = respuesta.data.producto;
          this.componerProductoEditar();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    /**
     * Método encargado de añadir los datos al objeto pagina actual para que sean editados
     */
    componerProductoEditar() {
      this.producto.id_producto = this.productoBruto.id_producto;
      this.producto.referencia = this.productoBruto.referencia;
      this.producto.precio_sin_iva = this.productoBruto.precio_sin_iva;
      this.producto.precio_coste = this.productoBruto.precio_coste;
      this.producto.cantidad = this.productoBruto.cantidad;
      this.producto.producto_combinacion = this.productoBruto.producto_combinacion;
      this.producto.marca = this.productoBruto.marca;
      this.producto.ean13 = this.productoBruto.ean13;
      this.producto.cod_arancel = this.productoBruto.cod_arancel;
      this.producto.peso = this.productoBruto.peso;
      this.producto.activo = this.productoBruto.activo;

      this.combinaciones_actuales = this.productoBruto.combinaciones;
      this.media_editar_preview = this.productoBruto.imagenes;

      // Recorro array actual de idiomas (ya formado con los que hay actualmente en la web)
      this.producto.idiomas.forEach((idioma, i) => {
        // Recorro array con los datos que tenemos
        this.productoBruto.productos_idiomas.forEach((proBruto, j) => {
          // COmpruebo que el idioma coincide y añado los datos
          if (idioma.id_idioma == proBruto.id_idioma) {
            this.producto.idiomas[i].id_producto_idioma = proBruto.id_producto_idioma;
            this.producto.idiomas[i].id_producto = proBruto.id_producto;
            this.producto.idiomas[i].nombre_producto = proBruto.nombre_producto;
            this.producto.idiomas[i].slug = proBruto.slug;
            this.producto.idiomas[i].descr_corta = proBruto.descr_corta;
            this.producto.idiomas[i].descr_larga = proBruto.descr_larga;
            this.producto.idiomas[i].tit_seo = proBruto.tit_seo;
            this.producto.idiomas[i].descr_seo = proBruto.descr_seo;
          }
        });
      });

      this.loading = false;
    },
    guardarProducto() {
      if (!this.editando) {
        let message = {
          title: "Confirmación",
          body: "¿Estas seguro de querer crear este producto?",
        };

        let options = {
          okText: "Crear",
          cancelText: "Cancelar",
          backgropClose: true,
        };

        this.$dialog.confirm(message, options).then((dialog) => {
          this.loading = true;

          let url = "/pim/productos/crear-producto";

          axios
            .post(url, {
              producto: this.producto,
              combinaciones: this.combinaciones_producto_nuevas,
            })
            .then((respuesta) => {
              if (this.media_multiple.length > 0) {
                this.crearVariosArchivos(respuesta.data.id_producto);
              } else {
                // this.$router.push({ name: "editar-producto", params: { id_producto: respuesta.data.id_producto } });
                // this.startView();
                this.borrarDatos();
                this.mensajeExito = "¡Se ha guardado el producto con éxito!";
                this.$refs.modal.open();
              }
            })
            .catch((error) => {
              let mensaje_error = "";

              if (error.response.data.mensaje != "") {
                mensaje_error = error.response.data.mensaje;
              } else {
                mensaje_error = error;
              }

              this.loading = false;
              this.mensajeError =
                "<p class='text-white mb-2'>¡Ha ocurrido un error al crear el producto!</p><p class='mb-1 text-white'>Mensaje del error: </p> <p><code>" +
                mensaje_error +
                "</code></p>";
              this.$refs.modalError.open();
              console.log(error);
            });
        });
      } else {
        this.guardarProductoEditado();
      }
    },
    /**
     * Método para guardar los datos de una producto ya editada
     */
    guardarProductoEditado() {
      let message = {
        title: "Confirmación",
        body: "¿Estas seguro de querer guardar este producto?",
      };

      let options = {
        okText: "Guardar",
        cancelText: "Cancelar",
        backgropClose: true,
      };

      this.$dialog.confirm(message, options).then((dialog) => {
        this.loading = true;

        let url = "/pim/productos/guardar-producto-editado";

        axios
          .put(url, {
            producto: this.producto,
            combinaciones_actualizadas: this.combinaciones_actuales,
            combinaciones_nuevas: this.combinaciones_producto_nuevas,
            combinaciones_eliminadas: this.combinaciones_eliminadas,
            media_eliminada: this.media_editar_preview_eliminadas,
          })
          .then((respuesta) => {
            if (this.media_multiple.length > 0) {
              this.crearVariosArchivos(this.producto.id_producto);
            } else {
              this.loading = false;
              this.mensajeExito = "¡Se ha guardado el producto con éxito!";
              this.$refs.modal.open();
            }
          })
          .catch((error) => {
            this.loading = false;
            this.mensajeError =
              "<p class='text-white mb-2'>¡Ha ocurrido un error al guardar el producto!</p><p class='mb-1 text-white'>Mensaje del error: </p> <p><code>" +
              error +
              "</code></p>";
            this.$refs.modalError.open();
            console.log(error);
          });
      });
    },
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
      this.producto.idiomas = [];
      this.todosIdiomas.forEach((idioma) => {
        this.producto.idiomas.push({
          id_producto_idioma: "",
          id_producto: "",
          id_idioma: idioma.id_idioma,
          nombre_producto: "",
          descr_corta: "",
          descr_larga: "",
          tit_seo: "",
          descr_seo: "",
          slug: "",
          nombre: idioma.nombre,
          icono_idioma: idioma.icono_idioma,
        });
      });

      if (this.editando) {
        this.cargarProductoEditar(this.$route.params.id_producto);
      } else {
        this.loading = false;
      }
    },
    /**
     * Método para inicializar el objeto producto
     */
    borrarDatos() {
      this.producto = {
        id_producto: "",
        activo: true,
        referencia: "",
        marca: "",
        precio_sin_iva: "",
        precio_coste: "",
        cantidad: "",
        producto_combinacion: 0,
        ean13: "",
        cod_arancel: "",
        peso: "",
        idiomas: [
          {
            id_producto_idioma: "",
            id_producto: "",
            id_idioma: "",
            nombre_producto: "",
            descr_corta: "",
            descr_larga: "",
            tit_seo: "",
            descr_seo: "",
            slug: "",
            nombre: "",
            icono_idioma: "",
          },
        ],
      };
      this.archivos_pesados = [];
      this.media_multiple = [];
      this.media_editar_preview = [];
      this.media_editar_preview_eliminadas = [];

      this.componerObjeto();
    },
  },
  computed: {},
  watch: {},
};
</script>

<style lang="scss" scoped>
.container-crear-producto {
  width: 85%;
  display: flex;
  flex-wrap: wrap;
  margin: auto;

  .container-principal {
    justify-content: space-between;

    .tabs {
      width: 100%;
    }
  }
}

.container-add-imagenes-producto {
  width: 100%;
  border: 1px solid black;
  height: 150px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  transition: all 0.1s;
  flex-direction: column;

  &:hover {
    transform: scale(0.99);
  }
}
</style>

<style lang="scss">
.contenedor-carga {
  width: 40%;
  margin: auto;
}

.container-media-preview {
  display: flex;
  justify-content: flex-start;
  flex-wrap: wrap;
  gap: 7px;

  .media-preview {
    padding-top: 15px;
    position: relative;

    i {
      position: absolute;
      right: 0;
      color: red;
      top: 0;
      cursor: pointer;
    }

    img {
      max-width: 100px !important;
      height: auto;
    }
  }
}

.selector-atributos {
  flex-direction: column !important;

  &--card .card-body {
    max-height: 150px;
    overflow: hidden;
    overflow-y: scroll;
  }
}

.nombre-combinacion {
  font-size: 12px;
}

.atributo-col {
  .form-check {
    display: flex;
    align-items: center;

    input {
      margin-top: 0;
    }
  }
}

.cuadro-color {
  width: 13px;
  height: 13px;
  border: 1px solid black;
  display: inline-block;
}

.container-atributos-seleccionados .badge i {
  cursor: pointer;
}

.col-table-right {
  text-align: right;
}
</style>
