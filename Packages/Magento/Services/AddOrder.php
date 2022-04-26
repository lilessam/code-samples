<?php

namespace App\Magento\Services;

use App\Magento\Contracts\Service;
use function GuzzleHttp\json_decode;

class AddOrder implements Service
{
    protected $product;
    protected $cartId;
    protected $cartInfo;

    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        /**
         * Get the product.
         *
         */
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $args[0] . '/index.php/rest/V1/products/' . $args[2] . '/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $args[1],
                'Content-Type: application/json',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if (!$err) {
            $this->product = json_decode($response);
        }
        curl_close($curl);

        //

        /**
         * Create the guest cart
         */
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $args[0] . '/index.php/rest/V1/guest-carts/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $args[1],
                'Content-Type: application/json',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            $this->cartId = str_replace('"', '', $response);
        }

        /**
         * Get the just added cart.
         *
         */

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $args[0] . '/index.php/rest/V1/guest-carts/' . $this->cartId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $args[1],
                'Content-Type: application/json',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        if (!$err) {
            $this->cartInfo = json_decode($response);
        }

        curl_close($curl);

        /**
         * Add product to cart.
         *
         */
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $args[0] . '/index.php/rest/V1/guest-carts/' . $this->cartId . '/items',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                'cartItem' => [
                    'quoteId' => $this->cartId,
                    'sku' => $args[2],
                    'qty' => 1
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $args[1],
                'Content-Type: application/json',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        /**
         * Add the shipping address to the cart.
         */

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $args[0] . '/index.php/rest/V1/guest-carts/' . $this->cartId . '/shipping-information',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($args[3]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $args[1],
                'Content-Type: application/json',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        //

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $args[0] . '/index.php/rest/V1/guest-carts/' . $this->cartId . '/order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => json_encode(['paymentMethod' => ['method' => 'checkmo']]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer mvf9lkgem5bb4uwh7vjvkhtfl4hczngq',
                'Content-Type: application/json',
                'Postman-Token: 823e49ef-1aed-4eb3-a148-138136f8dd1a',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return true;
    }
}
