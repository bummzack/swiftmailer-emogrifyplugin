<?php

namespace Bummzack\SwiftMailer\EmogrifyPlugin;

use Pelago\Emogrifier\CssInliner;
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
    protected $css_content = '';

    /**
     * @param string CSS styles
     * @return $this
     */
    public function setCSSContent($cssContent)
    {
        $this->css_content = $cssContent;
        return $this;
    }

    /**
     * @return string CSS styles
     */
    public function getCSSContent()
    {
        return $this->css_content;
    }

    /**
     * @param Swift_Events_SendEvent $event
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $event)
    {
        $message = $event->getMessage();

        $css = $this->getCSSContent();

        $body = $message->getBody();
        if (!empty($body) && $message->getContentType() !== 'text/plain') {
            $body = CssInliner::fromHtml($body)->inlineCss($css)->render();
            $message->setBody($body);
        }

        foreach ($message->getChildren() as $messagePart) {
            if ($messagePart->getContentType() === 'text/html') {
                $body = $messagePart->getBody();

                if (empty($body)) {
                    continue;
                }

                $body = CssInliner::fromHtml($body)->inlineCss($css)->render();
                $messagePart->setBody($body);
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
