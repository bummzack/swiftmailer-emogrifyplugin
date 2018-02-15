<?php

namespace Bummzack\SwiftMailer\EmogrifyPlugin;

use Pelago\Emogrifier;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;

/**
 * Emogrifier Plugin that will convert your CSS to inline styles.
 * To be used with SwiftMailer.
 *
 * @package Bummzack\SwiftMailer\EmogrifyPlugin
 */
class EmogrifierPlugin implements Swift_Events_SendListener
{
    /**
     * @var Emogrifier
     */
    protected $emogrifier;

    /**
     * @param Emogrifier $emogrifier
     */
    public function __construct(Emogrifier $emogrifier = null)
    {
        if ($emogrifier) {
            $this->emogrifier = $emogrifier;
        } else {
            $this->emogrifier = new Emogrifier();
        }
    }

    /**
     * Access to the emogrifier instance that's being used internally
     * @return Emogrifier
     */
    public function getEmogrifier()
    {
        return $this->emogrifier;
    }

    /**
     * Set the emogrifier instance that's being used internally
     * @param Emogrifier $emogrifier
     * @return $this
     */
    public function setEmogrifier(Emogrifier $emogrifier)
    {
        $this->emogrifier = $emogrifier;
        return $this;
    }

    /**
     * @param Swift_Events_SendEvent $event
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();

        $body = $message->getBody();
        if (!empty($body) && $message->getContentType() !== 'text/plain') {
            $this->emogrifier->setHtml($body);
            $message->setBody($this->emogrifier->emogrify());
        }

        foreach ($message->getChildren() as $messagePart) {
            if ($messagePart->getContentType() === 'text/html') {
                $body = $messagePart->getBody();

                if (empty($body)) {
                    continue;
                }

                $this->emogrifier->setHtml($body);
                $messagePart->setBody($this->emogrifier->emogrify());
            }
        }
    }

    /**
     * @param Swift_Events_SendEvent $event
     */
    public function sendPerformed(\Swift_Events_SendEvent $event)
    {
        /* No op */
    }
}
