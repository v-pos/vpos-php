<?php

    declare(strict_types=1);
    namespace Vpos\Vpos;

    use GuzzleHttp\Client;
    use JetBrains\PhpStorm\ArrayShape;
    use JetBrains\PhpStorm\Pure;
    use Ramsey\Uuid\Uuid;

    final class Vpos
    {
        const LOCATION = 17;
        private string $host;
        private int $pos_id;
        private false|array|string $refund_callback_url;
        private false|array|string $payment_callback_url;
        private false|array|string $supervisor_card;
        private string $token;
        private Client $client;

        public function __construct($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url, )
        {
            $this->host = $this->getHost();
            $this->pos_id = (int) $pos_id;
            $this->refund_callback_url = $refund_callback_url;
            $this->payment_callback_url = $payment_callback_url;
            $this->supervisor_card = $supervisor_card;
            $this->token = "Bearer " . $token;
            $this->client = new Client();
        }

        #[Pure] private function getHost(): string
        {
            return "https://vpos.ao/api/v1";
        }

        #[ArrayShape(['http_errors' => "false", 1 => "false[]", 'headers' => "array"])] private function setDefaultRequestOptions(): array
        {
            return [
                'http_errors' => false,
                ['allow_redirects' => false],
                'headers' => [
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json']
            ];
        }

        private function returnVposObject($response): array
        {
            return match ($response->getStatusCode()) {
                200 => [
                    'status_code' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'data' => $response->getBody()->getContents()
                ],
                202 => [
                    'status_code' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'location' => $response->getHeader('Location')[0]
                ],
                default => [
                    'status_code' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase(),
                    'details' => $response->getBody()->getContents()
                ],
            };
        }

        public function getTransaction($id): array
        {
            $response = $this->client->request(
                "GET",
                $this->host . "/transactions/" . $id,
                $this->setDefaultRequestOptions()
            );
            return $this->returnVposObject($response);
        }

        public function newPayment($customer, $amount): array
        {
            $options = $this->setRequestOptionsForPayment(
                customer: $customer,
                amount: $amount,
                transaction_type: "payment"
            );
            $response = $this->client->request(
                method: "POST",
                uri: $this->host . "/transactions",
                options: $options
            );
            return $this->returnVposObject($response);
        }

        #[ArrayShape(['http_errors' => "false", 1 => "false[]", 'json' => "array", 'headers' => "array"])] private function setRequestOptionsForPayment($customer, $amount, $transaction_type): array
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
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
                ]
            ];
        }

        public function newRefund($id): array
        {
            $options = $this->setRequestOptionsForRefund(
                transaction_id: $id,
                transaction_type: "refund"
            );
            $response = $this->client->request(
                "POST",
                $this->host . "/transactions",
                $options
            );
            return $this->returnVposObject($response);
        }

        #[ArrayShape(['http_errors' => "false", 1 => "false[]", 'json' => "array", 'headers' => "array"])] private function setRequestOptionsForRefund($transaction_id, $transaction_type): array
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
                    'Authorization' => $this->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ];
        }

        public function getRequest($id): array
        {
            $options = $this->setDefaultRequestOptions();
            $response = $this->client->request(
                "GET",
                $this->host . "/requests/" . $id,
                $options
            );
            return $this->returnVposObject($response);
        }

        #[Pure] public function getRequestId($response): string
        {
            if ($response['status_code'] == 202) {
                return substr($response['location'], self::LOCATION);
            }
            return substr($response['location'], self::LOCATION);
        }

        public function setToken($token): void
        {
            $this->token = "Bearer ". $token;
        }
    }

