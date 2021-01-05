<?php

    declare(strict_types=1);
    namespace Vpos\Vpos;

    final class Vpos
    {
        private $host = "";
        private $pos_id = 0;
        private $refund_callback_url = "";
        private $payment_callback_url = "";
        private $supervisor_card = "";
        private $merchant_vpos_token = "";

        public function __construct() 
        {
             $host = $this->getHost();
             $pos_id = $this->getPosId();
             $refund_callback_url = $this->getRefundCallbackUrl();
             $payment_callback_url = $this->getPaymentCallbackUrl();
             $supervisor_card = $this->getSupervisorCard();
             $merchant_vpos_token = $this->getMerchantToken();
        }

        public function getTransactions() 
        {
            return null;
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
            $token = getenv("MERCHANT_VPOS_TOKEN");
            return "Bearer " . $token;
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