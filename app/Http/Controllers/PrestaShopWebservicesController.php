<?php

namespace App\Http\Controllers;

use App\Models\Error;
use App\Models\PrestaConnector;
use App\Models\Producto;
use CURLFile;
use Exception;
use Illuminate\Http\Request;
use PrestaShopWebservice;
use SimpleXMLElement;

class PrestaShopWebservicesController extends Controller
{
    //

    /**
     * Editar un producto, V 1
     */
    public function test(Request $request)
    {
        // Creo la conexión al ws
        $ws = new PrestaShopWebservice('https://pim.isobayev.com/ps-1/', 'ZN1WB8KNU7J19U5DGV9S1S3HWK9DTT3N', false);

        // Configuro los datos de busqueda para obtener el id del producto por referencia
        $opt = [
            'resource' => 'products',
            'filter[reference]' => 'demo_14'
        ];

        // Obtengo el id del producto a editar
        $xml_id = (int) $ws->get($opt)->products->children()->product->attributes()->id;

        // Busco el producto a editar con todos sus atributos
        $xml = $ws->get([
            'resource' => 'products',
            'id' => $xml_id
        ]);

        // Creo una referencia al xml a editar
        $params = $xml->product->children();

        // EDITO LOS DATOS
        $params->price = 2;

        // position in category
        $dom = dom_import_simplexml($params->position_in_category);
        $dom->parentNode->removeChild($dom);
        // manufacturer_name
        $dom = dom_import_simplexml($params->manufacturer_name);
        $dom->parentNode->removeChild($dom);
        // quantity
        $dom = dom_import_simplexml($params->quantity);
        $dom->parentNode->removeChild($dom);

        // Actualizo el producto
        try {
            $updatedXml = $ws->edit([
                'resource' => 'products',
                'id' => (int) $params->id,
                'putXml' => $xml->asXML()
            ]);
            dump($updatedXml);
        } catch (\Throwable $th) {
        }
        return 'patata';
    }

    public function test2(Request $request)
    {
        $ws = new PrestaShopWebservice('https://pim.isobayev.com/ps-1/', 'ZN1WB8KNU7J19U5DGV9S1S3HWK9DTT3N', false);

        // call to retrieve customer with ID 2
        $xml = $ws->get([
            'resource' => 'customers',
            'id' => 2, // Here we use hard coded value but of course you could get this ID from a request parameter or anywhere else
        ]);

        dump($xml);

        return 'aha';
        $customerFields = $xml->customer->children();
        $customerFields->firstname = 'John';
        $customerFields->lastname = 'DOE';

        $updatedXml = $ws->edit([
            'resource' => 'customers',
            'id' => (int) $customerFields->id,
            'putXml' => $xml->asXML(),
        ]);

        dump($updatedXml);

        $customerFields = $updatedXml->customer->children();

        dump($customerFields);
    }

    public function test3(Request $request)
    {
        $ws = new PrestaShopWebservice('https://pim.isobayev.com/ps-1/', 'ZN1WB8KNU7J19U5DGV9S1S3HWK9DTT3N', false);

        $blank = $ws->get([
            'url' => 'https://pim.isobayev.com/ps-1/api/customers?schema=blank'
        ]);

        $customerFields = $blank->customer->children();
        $customerFields->firstname = 'Chaval';
        $customerFields->lastname = 'Apellido del Chaval';
        $customerFields->email = 'correo@chaval.com';
        $customerFields->passwd = 'ginettag50';

        dump($blank);
        dump($customerFields);

        $createdXml = $ws->add([
            'resource' => 'customers',
            'postXml' => $blank->asXML()
        ]);

        dump($createdXml->customer->children());
    }

    /**
     * Crear producto
     */
    public function test4(Request $request)
    {
        $ws = new PrestaShopWebservice('https://pim.isobayev.com/ps-1/', 'ZN1WB8KNU7J19U5DGV9S1S3HWK9DTT3N', false);

        $blank = $ws->get([
            'url' => 'https://pim.isobayev.com/ps-1/api/products?schema=blank'
        ]);

        dump($blank);

        $fields = $blank->product->children();
        $fields->price = 15;

        dump($fields);

        $createdXml = $ws->add([
            'resource' => 'products',
            'postXml' => $blank->asXML()
        ]);

        dump($createdXml);
    }

    /**
     * Eliminar
     */
    public function test5(Request $request)
    {
        $ws = new PrestaShopWebservice('https://pim.isobayev.com/ps-1/', 'ZN1WB8KNU7J19U5DGV9S1S3HWK9DTT3N', false);

        $opt = [
            'resource' => 'products',
            'id' => 22
        ];

        $xml = $ws->delete($opt);

        dump($xml);
    }

    /**
     * Test para crear un producto con todo el set completo (combis, atributos, fotos...)
     */
    public function test6(Request $request)
    {

        $obj_ps = new PrestaConnector();
        $obj_product = new Producto();
        return $obj_ps->checkProductExistByRef(1, "Refclear-1");
        return $obj_ps->addProductToTienda(1, 7);
        // return $obj_product->getProductFullData(7);
        return 'goes';

        $now = new \DateTime();
        $ws = new PrestaShopWebservice("http://pim.isobayev.com/ps-1/", 'ZN1WB8KNU7J19U5DGV9S1S3HWK9DTT3N', true);

        $blank = $ws->get([
            'url' => 'http://pim.isobayev.com/ps-1/api/products?schema=blank'
        ]);

        dump($blank);
        
        $fields = $blank->product->children();
        // TODO, crear o comprobar primero la marca
        // $fields->id_manufacturer = $Product[0]->id_manufacturer;
        $fields->reference = 'REF112345528';

        $fields->id_category_default = 2;
        $fields->id_tax_rules_group = 1;
        $fields->minimal_quantity = 1;
        $fields->additional_delivery_times = 1;
        $fields->state = 1;

        $fields->weight = 1.2;
        $fields->price = 15.30;
        $fields->active = true;
        $fields->date_add = $now->format('Y-m-d H:i:s');
        $fields->date_upd = $now->format('Y-m-d H:i:s');

        for ($i = 0; $i < 3; $i++) {
            // Para los link rewrite
            $node = dom_import_simplexml($fields->link_rewrite->language[$i][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata link_rewrite"));
            $fields->link_rewrite->language[$i][0] = "link-rewrite-" . ($i + 1);
            $fields->link_rewrite->language[$i][0]['id'] = ($i + 1);
            $fields->link_rewrite->language[$i][0]['xlink:href'] = 'http://pim.isobayev.com/ps-1/api/languages/' . ($i + 1);

            // Para los nombres de los productos
            $node = dom_import_simplexml($fields->name->language[$i][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata name"));
            $fields->name->language[$i][0] = "Name " . ($i + 1);
            $fields->name->language[$i][0]['id'] = ($i + 1);
            $fields->name->language[$i][0]['xlink:href'] = 'http://pim.isobayev.com/ps-1/api/languages/' . ($i + 1);

            // Para long description
            $node = dom_import_simplexml($fields->description->language[$i][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata description"));
            $fields->description->language[$i][0] = "description " . ($i + 1);
            $fields->description->language[$i][0]['id'] = ($i + 1);
            $fields->description->language[$i][0]['xlink:href'] = 'http://pim.isobayev.com/ps-1/api/languages/' . ($i + 1);

            // Para short description
            $node = dom_import_simplexml($fields->description_short->language[$i][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata description_short"));
            $fields->description_short->language[$i][0] = "description_short " . ($i + 1);
            $fields->description_short->language[$i][0]['id'] = ($i + 1);
            $fields->description_short->language[$i][0]['xlink:href'] = 'http://pim.isobayev.com/ps-1/api/languages/' . ($i + 1);

            // Para meta_title
            $node = dom_import_simplexml($fields->meta_title->language[$i][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata meta_title"));
            $fields->meta_title->language[$i][0] = "meta_title " . ($i + 1);
            $fields->meta_title->language[$i][0]['id'] = ($i + 1);
            $fields->meta_title->language[$i][0]['xlink:href'] = 'http://pim.isobayev.com/ps-1/api/languages/' . ($i + 1);

            // Para meta_description
            $node = dom_import_simplexml($fields->meta_description->language[$i][0]);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection("cdata meta_description"));
            $fields->meta_description->language[$i][0] = "meta_description " . ($i + 1);
            $fields->meta_description->language[$i][0]['id'] = ($i + 1);
            $fields->meta_description->language[$i][0]['xlink:href'] = 'http://pim.isobayev.com/ps-1/api/languages/' . ($i + 1);
        }

        $fields->associations->categories->addChild('category')->addChild('id', 2);

        dump($fields);

        $createdXml = $ws->add([
            'resource' => 'products',
            'postXml' => $blank->asXML()
        ]);

        // GESTIONAMOS EL RETURN Y PROCEDEMOS CON EL STOCK AVAILABLE
        $fields = $createdXml->product->children();

        // When new product created a new stock available id was created and we can take this id to use.
        $stock_available_id = $fields->associations->stock_availables->stock_available[0]->id;

        $id_created_product = $fields->id;

        // Here we get the stock available with were product id
        try {
            $opt = array('resource' => 'stock_availables');
            $opt['id'] = $stock_available_id;
            $xml = $ws->get($opt);
        } catch (Exception $e) {
            echo $e;
        }

        $fields = $xml->children()->children();
        //There we put our stock
        $fields->quantity = 15;

        // There we call to save our stock quantity.
        try {
            $opt = array('resource' => 'stock_availables');
            $opt['putXml'] = $xml->asXML();
            $opt['id'] = $stock_available_id;
            $xml = $ws->edit($opt);
            // if WebService don't throw an exception the action worked well and we don't show the following message
            echo "Successfully updated.";
        } catch (Exception $e) {
            echo $e;
        }

        $obj_ps->addImageToProduct(1, (int) $id_created_product, ['/fotos/foto-1.png', '/fotos/foto-2.png', '/fotos/foto-3.png', '/fotos/foto-4.jpg']);

        dump($obj_ps->createCombination(1));

        return 'jeje3';

    }
}
