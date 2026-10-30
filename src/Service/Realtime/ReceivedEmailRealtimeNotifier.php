<?php

namespace App\Service\Realtime;

use App\Entity\ReceivedEmail;
use Psr\Log\LoggerInterface;

class ReceivedEmailRealtimeNotifier
{
    public function __construct(
        private CentrifugoClient $centrifugoClient,
        private EmailBoxChannelNameResolver $emailBoxChannelNameResolver,
        private LoggerInterface $logger,
    ) {
    }

    public function notify(ReceivedEmail $receivedEmail): void
    {
        $temporaryEmailBox = $receivedEmail->getTemporaryEmailBox();

        if ($temporaryEmailBox === null) {
            return;
        }

        $channel = $this->emailBoxChannelNameResolver->resolve($temporaryEmailBox);

        try {
            if (!$this->centrifugoClient->hasSubscribers($channel)) {
                return;
            }

            $this->centrifugoClient->publish($channel, ['uuid' => $receivedEmail->getUuid()]);
        } catch (\Throwable $exception) {
            $this->logger->error(sprintf(
                'Failed to notify Centrifugo about received email on channel %s: %s',
                $channel,
                $exception->getMessage(),
            ), ['exception' => $exception]);
        }
    }
}
