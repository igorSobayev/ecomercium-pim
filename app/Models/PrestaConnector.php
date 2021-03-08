<?php

namespace App\Models;

use CURLFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use PrestaShopWebservice;
use PrestaShopWebserviceException;

class PrestaConnector extends Model
{
    use HasFactory;

    /**
     * Función para crear la conexión a la tienda via webservices
     * Devuelve el ws ya con la conexión creada
     */
    public function createConnection(int $id_tienda)
    {
        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);

        // $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, true);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);
        return $ws;
    }

    /**
     * Se comprueba que el producto exista en la tienda, si existe devuelve el id del producto
     * en caso contrario devuelve false
     */
    public function checkProductInTienda(int $id_tienda, $ref_product)
    {

        $ws = $this->createConnection($id_tienda);
        // Configuro los datos de busqueda para obtener el id del producto por referencia
        $opt = [
            'resource' => 'products',
            'filter[reference]' => $ref_product
        ];

        $xml = $ws->get($opt);

        // Se comprueba que existe el producto, en caso contrario devuelve false
        if (isset($xml->products->product)) {
            return (int) $xml->products->children()->product->attributes()->id;
        } else {
            return false;
        }
    }

    /**
     * Función para añadir una imagen a la tienda indicada
     * Las imagenes deben de ser un array que contenga las rutas de las imagenes en la aplicación
     */
    public function addImageToProduct(int $id_tienda, int $id_created_product, $images_data)
    {
        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);

        $urlImage = $con_data->store_root . 'api/images/products/' . $id_created_product . '/';
        $key  = $con_data->api_key;

        foreach ($images_data as $image) {
            try {
                //Here you set the path to the image you need to upload TIENE QUE SER RUTA COMPLETA PARA QUE SE PUEDA ACCEDER
                $image_path = Config::get('global.pim_root') . $image;

                $args['image'] = new CurlFile($image_path);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
                curl_setopt($ch, CURLOPT_URL, $urlImage);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_USERPWD, $key . ':');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if (200 != $httpCode) {
                    Error::create([
                        'error' => 'Ha ocurrido un error al insertar la imagen ' . $image . ' en el producto ' . $id_created_product
                    ]);
                }
            } catch (\Throwable $th) {
                Error::create([
                    'error' => 'Ha ocurrido un error al insertar la imagen ' . $image . ' en el producto ' . $id_created_product . ' | ' . $th
                ]);
                continue;
            }
        }
    }

    /**
     * Función que comprueba si existe una marca en una tienda en concreto, si existe, devuelve el id
     * en caso contrario la crea y devuelve el id
     */
    public function getManufacturer(int $id_tienda, $marca)
    {
        $obj_tienda = new Tienda();
        $now = new \DateTime();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        // Busco la marca
        $xml = $ws->get(['resource' => 'manufacturers', 'filter[name]' => $marca]);

        // Mira si la marca esta creada, si no lo esta, la creo siempre devuelvo el id de la marca
        if (isset($xml->manufacturers->manufacturer)) {
            return (int) $xml->manufacturers->manufacturer->attributes()->id;
        } else {
            $blank = $ws->get(['url' => $con_data->store_root . 'api/manufacturers?schema=blank']);

            $fields = $blank->manufacturer;
            $fields->name = $marca;
            $fields->active = true;
            $fields->date_add = $now->format('Y-m-d H:i:s');
            $fields->date_upd = $now->format('Y-m-d H:i:s');

            $createdXml = $ws->add(['resource' => 'manufacturers', 'postXml' => $blank->asXML()]);
            if (isset($createdXml->manufacturer)) {
                return (int) $createdXml->manufacturer[0]->id;
            } else {
                Error::create([
                    'error' => 'Ha ocurrido un error al insertar la marca ' . $marca . ' en la tienda ' . $con_data->store_root
                ]);
            }
        }
    }

    /**
     * Buscamos el id del grupo de atributo (talla, color...)
     * En caso de que no exista, lo creamos y devolvemos el id
     */
    public function getAttributeGroupId(int $id_tienda, $attribute_name)
    {
        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        // Busco el id group_attribute
        $xml = $ws->get(['resource' => 'product_options', 'filter[name]' => $attribute_name]);

        // Mira si esta creado, si no lo esta, la creo siempre devuelvo el id
        if (isset($xml->product_options->product_option)) {
            return (int) $xml->product_options->product_option->attributes()->id;
        } else {
            $blank = $ws->get(['url' => $con_data->store_root . 'api/product_options?schema=blank']);

            $fields = $blank->product_option;

            if ($attribute_name == 'color') {
                $fields->group_type = 'color';
                $fields->is_color_group = true;
            } else {
                $fields->group_type = 'select';
                $fields->is_color_group = false;
            }
            // Obtengo el numero de idiomas que tiene la tienda
            $num_idiomas = count($fields->name->language);

            // Añado los datos a lso diferentes idiomas, en este caso el public name estará capitalizada la primera letra
            for ($i = 0; $i < $num_idiomas; $i++) {
                $node = dom_import_simplexml($fields->name->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata name"));
                $fields->name->language[$i][0] = $attribute_name;
                $fields->name->language[$i][0]['id'] = ($i + 1);
                $fields->name->language[$i][0]['xlink:href'] = $con_data->store_root . '/api/languages/' . ($i + 1);

                $node = dom_import_simplexml($fields->public_name->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata public_name"));
                $fields->public_name->language[$i][0] = ucwords($attribute_name);
                $fields->public_name->language[$i][0]['id'] = ($i + 1);
                $fields->public_name->language[$i][0]['xlink:href'] = $con_data->store_root . '/api/languages/' . ($i + 1);
            }

            $createdXml = $ws->add(['resource' => 'product_options', 'postXml' => $blank->asXML()]);
            if (isset($createdXml->product_option)) {
                return (int) $createdXml->product_option[0]->id;
            } else {
                Error::create([
                    'error' => 'Ha ocurrido un error al insertar el attribute group ' . $attribute_name . ' en la tienda ' . $con_data->store_root
                ]);
            }
        }
    }

    /**
     * Buscamos el id del atributo que recibimos por parametro, si el valor nos e encuentra, se crea uno nuevo
     * Para saber si estamos ant eun color, tenemos el parametro $hex que si no esta presente, será false y por lo tanto
     * no vamos a insertar un color
     * Hay una limitación en los codigos de los colores y es que tienen que cumplir un formato de #XXXXXX, si no tiene
     * 6 valores en el codigo (excluyendo #), añadimos 0 por la derecha hasta tener máximo 7 valores
     */
    public function getAttributeOptionValueId(int $id_tienda, $attribute_value, $id_attribute_group, $hex = false)
    {
        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        // Busco el id del atributo
        $xml = $ws->get(['resource' => 'product_option_values', 'filter[id_attribute_group]' => $id_attribute_group, 'filter[name]' => $attribute_value]);

        // Miro si esta creado, si no lo esta, la creo, siempre devuelvo el id
        if (isset($xml->product_option_values->product_option_value)) {
            return (int) $xml->product_option_values->product_option_value->attributes()->id;
        } else {
            $blank = $ws->get(['url' => $con_data->store_root . 'api/product_option_values?schema=blank']);

            $fields = $blank->product_option_value;

            $fields->id_attribute_group = $id_attribute_group;

            if ($hex != false) {
                if (strpos($hex, '#') === false) {
                    $hex = '#' . $hex;
                }
                $fields->color = str_pad($hex, 7, "0", STR_PAD_RIGHT);
                // $fields->color = $hex;
            }

            // Obtengo el numero de idiomas que tiene la tienda
            $num_idiomas = count($fields->name->language);

            // Añado los datos a lso diferentes idiomas, en este caso el public name estará capitalizada la primera letra
            for ($i = 0; $i < $num_idiomas; $i++) {
                $node = dom_import_simplexml($fields->name->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata name"));
                $fields->name->language[$i][0] = $attribute_value;
                $fields->name->language[$i][0]['id'] = ($i + 1);
                $fields->name->language[$i][0]['xlink:href'] = $con_data->store_root . '/api/languages/' . ($i + 1);
            }

            try {
                $createdXml = $ws->add(['resource' => 'product_option_values', 'postXml' => $blank->asXML()]);
            } catch (PrestaShopWebserviceException $ps_ex) {
                Error::create([
                    'error' => 'Ha ocurrido un error al insertar el attribute ' . $attribute_value . ' en la tienda ' . $con_data->store_root . ' | ' . $ps_ex
                ]);
                return null;
            }

            if (isset($createdXml->product_option_value)) {
                return (int) $createdXml->product_option_value[0]->id;
            } else {
                Error::create([
                    'error' => 'Ha ocurrido un error al insertar el attribute ' . $attribute_value . ' en la tienda ' . $con_data->store_root
                ]);
                return null;
            }
        }
    }

    /**
     * Función que devuelve la información de una combinación buscando por su id y tienda
     * En caso de que no exista, devuelve null
     */
    public function getCombinationData(int $id_tienda, $id_combi = null)
    {
        if ($id_combi != null) {
            $obj_tienda = new Tienda();
            $con_data = $obj_tienda->getTiendaData($id_tienda);
            $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

            // Busco el id del atributo
            $xml = $ws->get(['resource' => 'combinations', 'display' => 'full', 'filter[id]' => $id_combi]);

            // Miro si esta creado, si no lo esta, la creo, siempre devuelvo el id
            if (isset($xml->combinations->combination)) {
                return (int) $xml->combinations->combination->attributes()->id;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Función que devuelve en un array las id de los option values de la combinacion
     */
    public function getCombinationOptionValues(int $id_tienda, $id_combi = null)
    {
        if ($id_combi != null) {
            $obj_tienda = new Tienda();
            $con_data = $obj_tienda->getTiendaData($id_tienda);
            $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

            // Busco el id del atributo
            $xml = $ws->get(['resource' => 'combinations', 'display' => 'full', 'filter[id]' => $id_combi]);

            // Miro si esta creado, si no lo esta, la creo, siempre devuelvo el id
            if (isset($xml->combinations->combination)) {

                $option_values = [];

                $combi = $xml->combinations->combination;
                $array_option_values = $combi->associations->product_option_values->product_option_value;

                foreach ($array_option_values as $value) {
                    array_push($option_values, (int) $value->id);
                }

                return $option_values;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Se comprueba si existe un producto dentro de una tienda en concreto, si existe se devuelve el id
     */
    public function checkProductExistByRef(int $id_tienda, $ref_producto)
    {
        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        // Busco el id del atributo
        $xml = $ws->get(['resource' => 'products', 'filter[reference]' => $ref_producto]);

        // Miro si esta creado, si no lo esta, la creo, siempre devuelvo el id
        if (isset($xml->products->product)) {
            return (int) $xml->products->product->attributes()->id;
        } else {
            return null;
        }
    }

    /**
     * Comprobamos que los datos de la combinacion no son nulos
     * En caso de que no sean nulos recuperamos el schema en blanco y rellenamos los datos
     * Los datos vienen tal cual de la base de datos de una combi del PIM
     * Se establecen y se formatean los datos de forma adecuada y se guarda la combinacion
     */
    public function createCombination(int $id_tienda, $combinacion_data = null, $precio_producto)
    {
        if ($combinacion_data == null) {
            return null;
        }
        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        $blank = $ws->get(['url' => $con_data->store_root . 'api/combinations?schema=blank']);

        $fields = $blank->combination;

        $fields->id_product = $combinacion_data->id_product;

        // Se comprueba que el ean13 es correcto
        if ($combinacion_data->ean13 != '' && $this->checkEan13Format($combinacion_data->ean13)) {
            $fields->ean13 = $combinacion_data->ean13;
        }

        $fields->quantity = (int) $combinacion_data->cantidad;
        $fields->reference = $combinacion_data->referencia;
        $fields->price = $combinacion_data->precio_sin_iva - $precio_producto;

        $fields->weight = (float) $combinacion_data->peso;
        $fields->minimal_quantity = 1;

        foreach ($combinacion_data->product_option_values as $key => $option_value) {
            $fields->associations->product_option_values->product_option_value[$key]->id = $option_value;
        }

        try {
            $createdXml = $ws->add(['resource' => 'combinations', 'postXml' => $blank->asXML()]);
        } catch (PrestaShopWebserviceException $ps_ex) {
            Error::create([
                'error' => 'Ha ocurrido un error al insertar la combinacion ' . $combinacion_data->nombre_combinacion . ' en la tienda ' . $con_data->store_root . ' | ' . $ps_ex
            ]);
            return null;
        }

        if (isset($createdXml->combination)) {
            return (int) $createdXml->combination[0]->id;
        } else {
            Error::create([
                'error' => 'Ha ocurrido un error al insertar la combinacion ' . $combinacion_data->nombre_combinacion . ' en la tienda ' . $con_data->store_root
            ]);
            return null;
        }
    }

    public function editarCombination(int $id_tienda, $combinacion_data = null, $precio_producto, int $id_combi)
    {
        if ($combinacion_data == null) {
            return null;
        }

        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        $xml = $ws->get(['resource' => 'combinations', 'id' => $id_combi]);

        $fields = $xml->combination;
        
        // Se comprueba que el ean13 es correcto
        if ($combinacion_data->ean13 != '' && $this->checkEan13Format($combinacion_data->ean13)) {
            $fields->ean13 = $combinacion_data->ean13;
        }

        $fields->quantity = (int) $combinacion_data->cantidad;
        $fields->reference = $combinacion_data->referencia;
        $fields->price = $combinacion_data->precio_sin_iva - $precio_producto;

        $fields->weight = (float) $combinacion_data->peso;

        try {
            $updatedXml = $ws->edit(['resource' => 'combinations', 'id' => $id_combi, 'putXml' => $xml->asXML()]);
        } catch (PrestaShopWebserviceException $ps_ex) {
            Error::create([
                'error' => 'Ha ocurrido un error al editar la combinacion ' . $combinacion_data->nombre_combinacion . ' en la tienda ' . $con_data->store_root . ' | ' . $ps_ex
            ]);
            return "null";
        }

        if (isset($updatedXml->combination)) {
            return (int) $updatedXml->combination[0]->id;
        } else {
            Error::create([
                'error' => 'Ha ocurrido un error al editar la combinacion ' . $combinacion_data->nombre_combinacion . ' en la tienda ' . $con_data->store_root
            ]);
            return null;
        }
    }

    /**
     * Función para actualizar el stock de un producto en concreto o una combinacion
     * Recibe por parametros el id de la tienda, la cantidad de stock nueva, el id del producto y el id de la combi
     * Si el id de la combi no esta presente, se actualiza el stock del producto normalmente, si esta presente
     * se actualiza el stock de la combi
     */
    public function updateStockAvailable(int $id_tienda, int $stock, int $id_product, int $id_product_attribute = 0)
    {
        $obj_tienda = new Tienda();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        // Recupero el id del stock available
        $xml = $ws->get(['resource' => 'stock_availables', 'filter[id_product]' => $id_product, 'filter[id_product_attribute]' => $id_product_attribute]);

        // Recupero los datos del objeto entero para editar
        $xml = $ws->get(['resource' => 'stock_availables', 'id' => (int) $xml->stock_availables->stock_available->attributes()->id]);

        // Cojo la referencia
        $fields = $xml->stock_available;

        // Actualizo el stock
        $fields->quantity = $stock;

        // Guardo los datos del stock actualizado
        try {
            $opt = array('resource' => 'stock_availables');
            $opt['putXml'] = $xml->asXML();
            $opt['id'] = (int) $fields->id;
            $xml = $ws->edit($opt);
        } catch (PrestaShopWebserviceException $e) {
            Error::create([
                'error' => 'Ha ocurrido un error al actualizar el stock del producto ' . $id_product . ' en la tienda ' . $con_data->store_root . ' | ' . $e
            ]);
            return null;
        }
    }

    /**
     * Función para añadir un producto a una tienda, se realiza todo el proceso incluida la parte de crear las combinaciones
     */
    public function addProductToTienda(int $id_tienda, int $id_product)
    {
        $obj_tienda = new Tienda();
        $obj_product = new Producto();
        $now = new \DateTime();
        $con_data = $obj_tienda->getTiendaData($id_tienda);
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        $product = $obj_product->getProductFullData($id_product);

        // Compruebo que si el producto esta creado, si no lo está, paso a editarlo
        if ($this->checkProductExistByRef($id_tienda, $product->referencia) == null) {

            // Obtengo el schema en blanco para rellenar los datos del producto
            $blank = $ws->get([
                'url' => $con_data->store_root . 'api/products?schema=blank'
            ]);

            $fields = $blank->product->children();
            $fields->id_manufacturer = $this->getManufacturer($id_tienda, $product->marca);
            $fields->reference = $product->referencia;

            $fields->id_category_default = 2;
            $fields->id_tax_rules_group = 1;
            $fields->minimal_quantity = 1;
            $fields->additional_delivery_times = 1;
            $fields->state = 1;

            if ($product->ean13 != '' && $this->checkEan13Format($product->ean13)) {
                $fields->ean13 = $product->ean13;
            }

            $fields->weight = (float) $product->peso;
            $fields->price = (float) $product->precio_sin_iva;
            $fields->active = $product->activo;
            $fields->date_add = $now->format('Y-m-d H:i:s');
            $fields->date_upd = $now->format('Y-m-d H:i:s');

            $num_idiomas = count($fields->name->language);

            foreach ($product->idiomas as $i => $idioma_data) {

                if ($i < $num_idiomas) {
                    // Para los link rewrite
                    $node = dom_import_simplexml($fields->link_rewrite->language[$i][0]);
                    $no = $node->ownerDocument;
                    $node->appendChild($no->createCDATASection("cdata link_rewrite"));
                    $fields->link_rewrite->language[$i][0] = $idioma_data->slug;
                    $fields->link_rewrite->language[$i][0]['id'] = ($i + 1);
                    $fields->link_rewrite->language[$i][0]['xlink:href'] = $con_data->store_root .  '/api/languages/' . ($i + 1);

                    // Para los nombres de los productos
                    $node = dom_import_simplexml($fields->name->language[$i][0]);
                    $no = $node->ownerDocument;
                    $node->appendChild($no->createCDATASection("cdata name"));
                    $fields->name->language[$i][0] = $idioma_data->nombre_producto;
                    $fields->name->language[$i][0]['id'] = ($i + 1);
                    $fields->name->language[$i][0]['xlink:href'] = $con_data->store_root .  '/api/languages/' . ($i + 1);

                    // Para long description
                    $node = dom_import_simplexml($fields->description->language[$i][0]);
                    $no = $node->ownerDocument;
                    $node->appendChild($no->createCDATASection("cdata description"));
                    $fields->description->language[$i][0] = $idioma_data->descr_larga;
                    $fields->description->language[$i][0]['id'] = ($i + 1);
                    $fields->description->language[$i][0]['xlink:href'] = $con_data->store_root .  '/api/languages/' . ($i + 1);

                    // Para short description
                    $node = dom_import_simplexml($fields->description_short->language[$i][0]);
                    $no = $node->ownerDocument;
                    $node->appendChild($no->createCDATASection("cdata description_short"));
                    $fields->description_short->language[$i][0] = $idioma_data->descr_corta;
                    $fields->description_short->language[$i][0]['id'] = ($i + 1);
                    $fields->description_short->language[$i][0]['xlink:href'] = $con_data->store_root .  '/api/languages/' . ($i + 1);

                    // Para meta_title
                    $node = dom_import_simplexml($fields->meta_title->language[$i][0]);
                    $no = $node->ownerDocument;
                    $node->appendChild($no->createCDATASection("cdata meta_title"));
                    $fields->meta_title->language[$i][0] = $idioma_data->tit_seo;
                    $fields->meta_title->language[$i][0]['id'] = ($i + 1);
                    $fields->meta_title->language[$i][0]['xlink:href'] = $con_data->store_root .  '/api/languages/' . ($i + 1);

                    // Para meta_description
                    $node = dom_import_simplexml($fields->meta_description->language[$i][0]);
                    $no = $node->ownerDocument;
                    $node->appendChild($no->createCDATASection("cdata meta_description"));
                    $fields->meta_description->language[$i][0] = $idioma_data->descr_seo;
                    $fields->meta_description->language[$i][0]['id'] = ($i + 1);
                    $fields->meta_description->language[$i][0]['xlink:href'] = $con_data->store_root .  '/api/languages/' . ($i + 1);
                }
            }

            $fields->associations->categories->addChild('category')->addChild('id', 2);

            $createdXml = $ws->add([
                'resource' => 'products',
                'postXml' => $blank->asXML()
            ]);

            // GESTIONAMOS EL RETURN Y PROCEDEMOS CON EL STOCK AVAILABLE
            $fields = $createdXml->product->children();

            // When new product created a new stock available id was created and we can take this id to use.

            $id_created_product = $fields->id;

            // Gestionamos las fotos si existen TODO TODO TODODOODODODODODODODODOODODODO descomentar antes de subir
            if (count($product->imagenes) > 0) {
                $this->addImageToProduct($id_tienda, (int) $id_created_product, $product->imagenes);
            }

            // Añadimos el stock en caso de que no tengamos combinaciones
            // En caso contrario empezamos a gestionar todo el tema de las combinaciones (atributos...)
            if (!$product->producto_combinacion) {
                $this->updateStockAvailable($id_tienda, $product->cantidad, (int) $id_created_product);
            } else {
                if (count($product->combinaciones) > 0) {
                    // Iteramos sobre todas las combinaciones
                    foreach ($product->combinaciones as $combi) {
                        // Array que contiene todos los id's de los atributos de la combinacion, se crean si no existen
                        $array_id_attributes_values = [];
                        // Iteramos sobre cada atributo de la combinacion
                        foreach ($combi->atributos as $atributo) {
                            // Recuperamos el id atributo group
                            $id_attribute_group = $this->getAttributeGroupId($id_tienda, $atributo->tipo_atributo);
                            // Comprobamos que no sea null
                            if ($id_attribute_group != null) {
                                // Miramos si es un color viendo si el campo color esta vacio
                                // En cualquier caso, recuperamos el id del attribute value
                                if ($atributo->color != null) {
                                    // En caso de que sea un color
                                    $id_attribute_value = $this->getAttributeOptionValueId($id_tienda, $atributo->idiomas[0]->valor_atributo, $id_attribute_group, $atributo->color);
                                } else {
                                    // En caso de que no sea un color
                                    $id_attribute_value = $this->getAttributeOptionValueId($id_tienda, $atributo->idiomas[0]->valor_atributo, $id_attribute_group);
                                }

                                // Añadimos los id de los atributos a la combi para crearla bien
                                array_push($array_id_attributes_values, $id_attribute_value);
                            }
                        }

                        // Asignamos el array con los ids a la combi
                        $combi->product_option_values = $array_id_attributes_values;
                        $combi->id_product = (int) $id_created_product;

                        // Una vez que tenemos todos los datos, procedemos a crear la combinacion
                        $id_combi = $this->createCombination($id_tienda, $combi, (float) $product->precio_sin_iva);
                        $this->updateStockAvailable($id_tienda, $combi->cantidad, $combi->id_product, $id_combi);
                    }
                }
            }
        } else {
            // Edito el producto en la tienda correspondiente
            $this->editProduct($id_tienda, $product, $con_data, $ws);
        }
    }

    public function editProduct(int $id_tienda, $product_data, $con_data)
    {
        $now = new \DateTime();
        $ws = new PrestaShopWebservice($con_data->store_root, $con_data->api_key, $con_data->debug);

        // Configuro los datos de busqueda para obtener el id del producto por referencia
        $opt = [
            'resource' => 'products',
            'filter[reference]' => $product_data->referencia
        ];

        // Obtengo el id del producto a editar
        $xml_id = (int) $ws->get($opt)->products->children()->product->attributes()->id;

        // Busco el producto a editar con todos sus atributos
        $xml = $ws->get([
            'resource' => 'products',
            'id' => $xml_id
        ]);

        // Creo una referencia al xml a editar
        $fields = $xml->product->children();

        // EDITO LOS DATOS
        $fields->price = (float) $product_data->precio_sin_iva;
        $fields->active = $product_data->activo;
        $fields->reference = $product_data->referencia;

        if ($product_data->ean13 != '' && $this->checkEan13Format($product_data->ean13)) {
            $fields->ean13 = $product_data->ean13;
        }

        $fields->weight = (float) $product_data->peso;
        $fields->date_upd = $now->format('Y-m-d H:i:s');

        $num_idiomas = count($fields->name->language);

        foreach ($product_data->idiomas as $i => $idioma_data) {
            if ($i < $num_idiomas) {
                // Para los link rewrite
                $node = dom_import_simplexml($fields->link_rewrite->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata link_rewrite"));
                $fields->link_rewrite->language[$i][0] = $idioma_data->slug;
                $fields->link_rewrite->language[$i][0]['id'] = ($i + 1);

                // Para los nombres de los productos
                $node = dom_import_simplexml($fields->name->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata name"));
                $fields->name->language[$i][0] = $idioma_data->nombre_producto;
                $fields->name->language[$i][0]['id'] = ($i + 1);

                // Para long description
                $node = dom_import_simplexml($fields->description->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata description"));
                $fields->description->language[$i][0] = $idioma_data->descr_larga;
                $fields->description->language[$i][0]['id'] = ($i + 1);

                // Para short description
                $node = dom_import_simplexml($fields->description_short->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata description_short"));
                $fields->description_short->language[$i][0] = $idioma_data->descr_corta;
                $fields->description_short->language[$i][0]['id'] = ($i + 1);

                // Para meta_title
                $node = dom_import_simplexml($fields->meta_title->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata meta_title"));
                $fields->meta_title->language[$i][0] = $idioma_data->tit_seo;
                $fields->meta_title->language[$i][0]['id'] = ($i + 1);

                // Para meta_description
                $node = dom_import_simplexml($fields->meta_description->language[$i][0]);
                $no = $node->ownerDocument;
                $node->appendChild($no->createCDATASection("cdata meta_description"));
                $fields->meta_description->language[$i][0] = $idioma_data->descr_seo;
                $fields->meta_description->language[$i][0]['id'] = ($i + 1);
            }
        }

        // position in category
        $dom = dom_import_simplexml($fields->position_in_category);
        $dom->parentNode->removeChild($dom);
        // manufacturer_name
        $dom = dom_import_simplexml($fields->manufacturer_name);
        $dom->parentNode->removeChild($dom);
        // quantity
        $dom = dom_import_simplexml($fields->quantity);
        $dom->parentNode->removeChild($dom);

        // Actualizo el producto
        try {
            $updatedXml = $ws->edit([
                'resource' => 'products',
                'id' => (int) $fields->id,
                'putXml' => $xml->asXML()
            ]);

            $id_updated_product = (int) $fields->id;

            // Añadimos el stock en caso de que no tengamos combinaciones
            // En caso contrario empezamos a gestionar todo el tema de las combinaciones (atributos...)
            if (!$product_data->producto_combinacion) {
                $this->updateStockAvailable($id_tienda, $product_data->cantidad, (int) $id_updated_product);
            } else {
                if (count($product_data->combinaciones) > 0) {

                    $datos_combinaciones_actuales = [];
                    $combis_product = $updatedXml->product->associations->combinations->combination;

                    foreach ($combis_product as $combi_product) {
                        $id_combi_pro = (int) $combi_product->id;
                        $datos_combinaciones_actuales[$id_combi_pro] = $this->getCombinationOptionValues($id_tienda, $id_combi_pro);
                    }

                    // Iteramos sobre todas las combinaciones
                    foreach ($product_data->combinaciones as $combi) {
                        // Array que contiene todos los id's de los atributos de la combinacion, se crean si no existen
                        $array_id_attributes_values = [];
                        // Id de la combinacion en caso de editar
                        $id_combi_editar = '';
                        // Variable de control para ver si creamos o editamos una combinacion
                        $crear_combi = true;

                        // Iteramos sobre cada atributo de la combinacion
                        foreach ($combi->atributos as $atributo) {
                            // Recuperamos el id atributo group
                            $id_attribute_group = $this->getAttributeGroupId($id_tienda, $atributo->tipo_atributo);
                            // Comprobamos que no sea null
                            if ($id_attribute_group != null) {
                                // Miramos si es un color viendo si el campo color esta vacio
                                // En cualquier caso, recuperamos el id del attribute value
                                if ($atributo->color != null) {
                                    // En caso de que sea un color
                                    $id_attribute_value = $this->getAttributeOptionValueId($id_tienda, $atributo->idiomas[0]->valor_atributo, $id_attribute_group, $atributo->color);
                                } else {
                                    // En caso de que no sea un color
                                    $id_attribute_value = $this->getAttributeOptionValueId($id_tienda, $atributo->idiomas[0]->valor_atributo, $id_attribute_group);
                                }

                                // Añadimos los id de los atributos a la combi para crearla bien
                                array_push($array_id_attributes_values, $id_attribute_value);
                            }
                        }

                        // Recorro todas las combis del producto
                        foreach ($datos_combinaciones_actuales as $key => $datos_combi_actual) {
                            // Primero compruebo que el tamaño coincide para seguir con el resto de comprobaciones
                            // si no coincide, paso directamente al siguiente
                            if (count($datos_combi_actual) == count($array_id_attributes_values) && $crear_combi != false) {
                                // Recorro el array de los atributos actuales para comprobar si coinciden los valores
                                foreach ($array_id_attributes_values as $attribute) {
                                    // Se mira si el id del atributo esta dentro del array de los atributos de la combi
                                    // si esta, entonces tiene que estar en todos los elementos del array, en caso de que 
                                    // al menos uno de los id no este en el array de la combi, paso a la siguiene
                                    if (in_array($attribute, $datos_combi_actual)) {
                                        $crear_combi = false;
                                    } else {
                                        $crear_combi = true;
                                        break;
                                    }
                                }

                                // Despues de la iteracion compruebo si se ha encontrado el valor de la combinacion
                                // a editar o a crear, si ya se encontro, directamente rompo el bucle y paso a lo siguiente
                                if ($crear_combi == false) {
                                    $id_combi_editar = $key;
                                    break;
                                }
                            }
                        }

                        $combi->id_product = (int) $id_updated_product;

                        if ($crear_combi) {
                            // Asignamos el array con los ids a la combi
                            $combi->product_option_values = $array_id_attributes_values;

                            // Una vez que tenemos todos los datos, procedemos a crear la combinacion
                            $id_combi = $this->createCombination($id_tienda, $combi, (float) $product_data->precio_sin_iva);
                            $this->updateStockAvailable($id_tienda, $combi->cantidad, $combi->id_product, $id_combi);
                        } else {
                            // Editamos la combi
                            $this->editarCombination($id_tienda, $combi, (float) $product_data->precio_sin_iva, $id_combi_editar);
                            $this->updateStockAvailable($id_tienda, $combi->cantidad, $combi->id_product, $id_combi_editar);
                        }
                    }
                }
            }
        } catch (PrestaShopWebserviceException $e) {
            Error::create([
                'error' => 'Ha ocurrido un error al actualizar el producto ' . $product_data->referencia . ' en la tienda ' . $con_data->store_root . ' | ' . $e
            ]);
            return null;
        }
    }

    /**
     * Se comprueba que el string del ean13 cumple el patron establecido por ps
     */
    public function checkEan13Format($ean13)
    {
        if (preg_match("/^[0-9]{0,13}$/", $ean13)) {
            return true;
        } else {
            return false;
        }
    }
}
