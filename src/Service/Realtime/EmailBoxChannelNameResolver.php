<?php

namespace App\Service\Realtime;

use App\Entity\TemporaryEmailBox;

class EmailBoxChannelNameResolver
{
    private const string NAMESPACE = 'email-box';

    public function resolve(TemporaryEmailBox $temporaryEmailBox): string
    {
        return self::NAMESPACE . ':' . $temporaryEmailBox->getUuid();
    }
}
