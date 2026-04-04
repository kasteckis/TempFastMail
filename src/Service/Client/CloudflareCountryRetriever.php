<?php

namespace App\Service\Client;

use Symfony\Component\HttpFoundation\Request;

class CloudflareCountryRetriever
{
    public function getCountryCode(Request $request): ?string
    {
        return $request->headers->get('CF-IPCountry');
    }
}

