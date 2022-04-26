<?php

namespace App\Magento\Lib;

class MagentoClient
{
    public $bearer_token = '';
    public $base_url = '';

    public function __construct($token, $base_url)
    {
        $this->base_url = $base_url;
        $this->bearer_token = $token;
    }

    public function request($endpoint, $method = 'GET', $body = false)
    {
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $headers = [];
        $headers[] = 'Authorization: Bearer ' . $this->bearer_token;
        if ($body) {
            $headers[] = 'Content-Type: application/json';
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

    public function getProduct($product_id)
    {
        return $this->request('/products/' . $product_id . '/', 'GET');
    }

    public function createCart()
    {
        return $this->request('/guest-carts/', 'POST');
    }

    public function addToCart($cart_id, $product_sku, $quantity = 1)
    {
        $order = [
            'cartItem' => [
                'quote_id' => $cart_id,
                'sku' => $product_sku,
                'qty' => $quantity,
            ]
        ];

        return $this->request(
        '/guest-carts/' . $cart_id . '/items',
      'POST',
      json_encode($order)
      );
    }

    public function setShipping($cart_id, $shipping)
    {
        return $this->request(
        '/guest-carts/' . $cart_id . '/shipping-information',
      'POST',
      json_encode($shipping)
    );
    }

    public function placeOrder($cart_id, $payment_method = 'cashondelivery')
    {
        $payment = [
            'paymentMethod' => ['method' => $payment_method]
        ];

        return $this->request(
        '/guest-carts/' . $cart_id . '/order',
      'PUT',
      json_encode($payment)
    );
    }

    public function getPaymentMethods($cart_id)
    {
        return $this->request('/guest-carts/' . $cart_id . '/payment-information', 'GET');
    }
}
