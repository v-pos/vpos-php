<?php

    declare(strict_types=1);
    namespace Vpos\Vpos;

    use GuzzleHttp\Client;
    use GuzzleHttp\RequestOptions;
    use Ramsey\Uuid\Uuid;

    final class Vpos
    {
        private $host;
        private $pos_id;
        private $refund_callback_url;
        private $payment_callback_url;
        private $supervisor_card;
        private $merchant_vpos_token;
        private $client;
        const LOCATION = 17;

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
            $response = $this->client->request('GET', $this->host . "/transactions", $this->setDefaultRequestOptions());
            return $this->returnVposObject($response);
        }

        public function getTransaction($id)
        {
            $response = $this->client->request("GET", $this->host . "/transactions/" . $id, $this->setDefaultRequestOptions());
            return $this->returnVposObject($response);
        }

        public function newPayment($customer, $amount)
        {
            $options = $this->setRequestOptionsForPayment(
                customer: $customer,
                amount: $amount,
                transaction_type: "payment"
            );
            $response = $this->client->request("POST", $this->host . "/transactions", $options);
            return $this->returnVposObject($response);
        }

        public function newRefund($id)
        {
            $options = $this->setRequestOptionsForRefund(
                transaction_id: $id,
                transaction_type: "refund"
            );
            $response = $this->client->request("POST", $this->host . "/transactions", $options);
            return $this->returnVposObject($response);
        }

        public function getRequest($id)
        {
            $options = $this->setDefaultRequestOptions();
            $response = $this->client->request("GET", $this->host . "/requests/" . $id, $options);
            return $this->returnVposObject($response);
        }

        public function getRequestId($response)
        {
            if ($response['status'] == 202) {
                return substr($response['location'], self::LOCATION);
            }
        }

        public function setToken($token): void 
        {
            $this->merchant_vpos_token = "Bearer ". $token;
        }

        private function returnVposObject($response) 
        {
            switch($response->getStatusCode()) {
                case 200:
                    return [
                    'status' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'data' => $response->getBody()->getContents()
                ];
                case 202:
                    return [
                        'status' => $response->getStatusCode(),
                        'message' => $response->getReasonPhrase(),
                        'location' => $response->getHeader('Location')[0]
                 ];
                 default:
                 return [
                    'status' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'details' => $response->getBody()->getContents()
                ];
                    
            }
        }

        private function setRequestOptionsForPayment($customer, $amount, $transaction_type)
        {
            return [
                'http_errors' => false,
                ['allow_redirects' => false],
                'json' => [
                    'type' => $transaction_type,
                    'pos_id' => $this->pos_id,
                    'mobile' => $customer,
                    'amount' => $amount,
                    'callback_url' => $this->payment_callback_url
                ],
                'headers' => [
                'Idempotency-Key' => Uuid::uuid4()->toString(),
                'Authorization' => $this->merchant_vpos_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
                ]
            ];
        }

            private function setRequestOptionsForRefund($transaction_id, $transaction_type)
            {
                return [
                    'http_errors' => false,
                    ['allow_redirects' => false],
                    'json' => [
                        'type' => $transaction_type,
                        'parent_transaction_id' => $transaction_id,
                        'supervisor_card' => $this->supervisor_card,
                        'callback_url' => $this->refund_callback_url
                    ],
                    'headers' => [
                        'Idempotency-Key' => Uuid::uuid4()->toString(),
                        'Authorization' => $this->merchant_vpos_token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ]
                ];
            }

        private function setDefaultRequestOptions()
        {
            return [
                'http_errors' => false,
                ['allow_redirects' => false],
                'headers' => [
                'Authorization' => $this->merchant_vpos_token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json']
            ];
        }

        private function getPosId() 
        {
            return (int) getenv("GPO_POS_ID");
        }

        private function getRefundCallbackUrl()
        {
            return getenv("VPOS_REFUND_CALLBACK_URL");
        }

        private function getPaymentCallbackUrl()
        {
            return getenv("VPOS_PAYMENT_CALLBACK_URL");
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

