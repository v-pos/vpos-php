<?php

    declare(strict_types=1);
    namespace Vpos\Vpos\Vpos;

    class Vpos
    {
        private function getToken() 
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