<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PrestaShopWebservice;

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
}
