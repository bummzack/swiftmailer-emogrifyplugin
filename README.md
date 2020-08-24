# Swift Mailer Emogrify Plugin

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bummzack/swiftmailer-emogrifyplugin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bummzack/swiftmailer-emogrifyplugin/?branch=master)
[![Code Coverage](https://codecov.io/gh/bummzack/swiftmailer-emogrifyplugin/branch/master/graph/badge.svg)](https://codecov.io/gh/bummzack/swiftmailer-emogrifyplugin)
[![Build Status](https://travis-ci.org/bummzack/swiftmailer-emogrifyplugin.svg?branch=master)](https://travis-ci.org/bummzack/swiftmailer-emogrifyplugin)
[![Latest Stable Version](https://poser.pugx.org/bummzack/swiftmailer-emogrifyplugin/v/stable)](https://packagist.org/packages/bummzack/swiftmailer-emogrifyplugin)

Inline CSS in the HTML output of SwiftMailer using [Emogrifier](https://github.com/MyIntervals/emogrifier).

## Installation and requirements

Install via composer, using:

    composer require bummzack/swiftmailer-emogrifyplugin
    
Requirements:

 - PHP 5.6+
 - SwiftMailer 5.x
 - Emogrifier 3.x
 
## Usage

By default, the plugin will inline CSS that is part of the HTML, eg. styles defined in `<style>` tags.
You can instantiate the plugin with your own `Emogrifier` instance or change properties of the emogrifier instance. 
For a list of options, please head over to the [Emogrifier documentation](https://github.com/MyIntervals/emogrifier#options).

Please note, that the plugin is using one instance of `Emogrifier` to convert all message-parts,
so the settings you make apply to all converted html parts.

### Supplying custom CSS

```php
$plugin = new EmogrifierPlugin();
$plugin->getEmogrifier()->setCss('.customStyle: { color: red; };');
```

**Please note:** Calling `setHtml` on the Emogrifier instance doesn't have an effect, since it will be replaced with
the message body!

### Example

Here's how you could use the plugin to send emails with custom styles loaded from a file:

```php
$emogrifier = new Pelago\Emogrifier();
$emogrifier->setCss(file_get_contents( /* path to your CSS file */ ));

// Create the Mailer using any Transport
$mailer = new Swift_Mailer(
    new Swift_SmtpTransport('smtp.example.org', 25)
);

// Use Emogrifier plugin to inline styles. You can pass the emogrifier instance as a parameter
$mailer->registerPlugin(new Bummzack\SwiftMailer\EmogrifyPlugin\EmogrifierPlugin($emogrifier));

$message = new Swift_Message();
$message
    ->setSubject('Your subject')
    ->setFrom(['test@example.com' => 'Test'])
    ->setTo(['receiver@example.com'])
    ->setBody('<p>My custom HTML</p>', 'text/html');

// Send your email
$mailer->send($message);
```
