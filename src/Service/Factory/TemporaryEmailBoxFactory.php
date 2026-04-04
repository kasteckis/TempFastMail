<?php

namespace App\Service\Factory;

use App\Entity\TemporaryEmailBox;

class TemporaryEmailBoxFactory
{
    public function create(string $email, string $creatorIp, ?string $countryCode): TemporaryEmailBox
    {
        $temporaryEmailBox = new TemporaryEmailBox();

        $temporaryEmailBox
            ->setEmail($email)
            ->setCreatorIp($creatorIp)
            ->setCountryCode($countryCode)
        ;

        return $temporaryEmailBox;
    }
}
