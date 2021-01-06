<?php

    declare(strict_types=1);
    namespace Vpos\Vpos;

    use GuzzleHttp\Client;
    use Ramsey\Uuid\Uuid;

    final class Vpos
    {
        private $host = "";
        private $pos_id = 0;
        private $refund_callback_url = "";
        private $payment_callback_url = "";
        private $supervisor_card = "";
        private $merchant_vpos_token = "";
        private $client = null;

        public function __construct() 
        {
            $this->host = $this->getHost();
            $this->pos_id = $this->getPosId();
            $this->refund_callback_url = $this->getRefundCallbackUrl();
            $this->payment_callback_url = $this->getPaymentCallbackUrl();
            $this->supervisor_card = $this->getSupervisorCard();
            $this->merchant_vpos_token = $this->getMerchantToken();
            $this->client = new \GuzzleHttp\Client();
        }

        public function getTransactions() 
        {
            $response = $this->client->request('GET', $this->host . "/transactions", $this->set_headers());
            return $this->return_vpos_object($response);
        }

        public function getTransaction($id)
        {
            $response = $this->client->request("GET", $this->host . "/transactions/" . $id, $this->set_headers());
            return $this->return_vpos_object($response);
        }

        public function setToken($token): void 
        {
            $this->merchant_vpos_token = "Bearer ". $token;
        }

        private function return_vpos_object($response) 
        {

            switch($response->getStatusCode()) {
                case 200 || 201:
                    return [
                    'status' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'data' => $response->getBody()
                ];
                case 202 || 203: 
                    return [
                        'status' => $response->getStatusCode(),
                        'message' => $response->getReasonPhrase(),
                        'location' => $response->getBody()
                 ];
                 default:
                 return [
                    'status' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'details' => $response->getBody()
                ];
                    
            }
        }

        private function set_post_headers() 
        {
            return [
                'http_errors' => false,
                'headers' => [
                'Idempotency-Key' => Uuid::uuid4()->toString(),  
                'Authorization' => $this->merchant_vpos_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json']
            ];
        }

        private function set_headers() 
        {
            return [
                'http_errors' => false,
                'headers' => [
                'Authorization' => $this->merchant_vpos_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json']
            ];
        }

        private function getPosId() 
        {
            return (int) getenv("GPOS_POS_ID");
        }

        private function getRefundCallbackUrl()
        {
            return getenv("VPOS_REFUND_CALLBACK");
        }

        private function getPaymentCallbackUrl()
        {
            return getenv("VPOS_PAYMENT_CALLBACK");
        }

        private function getSupervisorCard() 
        {
            return getenv("GPO_SUPERVISOR_CARD");
        }

        private function getMerchantToken() 
        {
            return "Bearer " . getenv("MERCHANT_VPOS_TOKEN");
        }

        private function getHost() 
        {
            if (getenv("VPOS_ENVIRONMENT") == "PRD")
            {
                return "https://api.vpos.ao/api/v1";
            } else {
                return "https://sandbox.vpos.ao/api/v1";
            }
        }
    }

?>