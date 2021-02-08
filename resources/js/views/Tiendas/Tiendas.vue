<template>
  <section class="body d-flex justify-content-center row">
    <div class="col-lg-10 pb-4 pl-0">
      <h2>Listado de todas las tiendas y sus productos</h2>
    </div>
    <table class="table table-hover col-lg-10">
      <thead>
        <tr>
          <th scope="col">Tienda</th>
          <th scope="col">Nº Productos</th>
          <th scope="col">Stock total de productos</th>
          <th scope="col">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(tienda, index) in tiendas" :key="index">
          <th scope="row">{{ tienda.nombre_tienda }}</th>
          <td>{{ tienda.num_productos }}</td>
          <td>{{ tienda.stock_total }}</td>
          <td>
            <b-dropdown text="Acciones" variant="outline-success">
              <!-- <b-dropdown-item href="#">Editar tienda </b-dropdown-item> -->
              <b-dropdown-item :to="{ name: 'gestionar-productos-tienda', params: { id_tienda: tienda.id_tienda } }">Gestionar productos tienda</b-dropdown-item>
            </b-dropdown>
          </td>
        </tr>
      </tbody>
    </table>
  </section>
</template>

<script>
export default {
  mounted() {
    this.cargarTiendas();
  },
  data() {
    return {
      tiendas: [],
    };
  },
  methods: {
    cargarTiendas() {
      let url = "/pim/tiendas/cargar-tiendas";

      axios
        .get(url)
        .then((respuesta) => {
          console.log(respuesta.data);
          this.tiendas = respuesta.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style></style>
