<?php

namespace Bummzack\SwiftMailer\EmogrifyPlugin;

use Pelago\Emogrifier\CssInliner;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;
use Symfony\Component\CssSelector\Exception\ParseException;

/**
 * Emogrifier Plugin that will convert your CSS to inline styles.
 * To be used with SwiftMailer.
 *
 * @package Bummzack\SwiftMailer\EmogrifyPlugin
 */
class EmogrifierPlugin implements Swift_Events_SendListener
{
    protected $css = null;

    public function getCss(): ?string
    {
        return $this->css;
    }

    public function setCss($value): self
    {
        $this->css = $value;
        return $this;
    }

    /**
     * @param Swift_Events_SendEvent $evt
     * @throws ParseException
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        $body = $message->getBody();
        if (!empty($body) && $message->getContentType() !== 'text/plain') {
            $html = CssInliner::fromHtml($body)->inlineCss($this->css ?? '')->render();
            $message->setBody($html);
        }

        foreach ($message->getChildren() as $messagePart) {
            if ($messagePart->getContentType() === 'text/html') {
                $body = $messagePart->getBody();

                if (empty($body)) {
                    continue;
                }

                $html = CssInliner::fromHtml($body)->inlineCss($this->css ?? '')->render();
                $messagePart->setBody($html);
            }
        }
    }

    /**
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(\Swift_Events_SendEvent $evt)
    {
        /* No op */
    }
}
