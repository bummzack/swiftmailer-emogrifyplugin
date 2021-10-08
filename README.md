# Swift Mailer Emogrify Plugin

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bummzack/swiftmailer-emogrifyplugin/badges/quality-score.png?b=1)](https://scrutinizer-ci.com/g/bummzack/swiftmailer-emogrifyplugin/?branch=1)
[![Code Coverage](https://codecov.io/gh/bummzack/swiftmailer-emogrifyplugin/branch/1/graph/badge.svg)](https://codecov.io/gh/bummzack/swiftmailer-emogrifyplugin)
[![Build Status](https://travis-ci.com/bummzack/swiftmailer-emogrifyplugin.svg?branch=1)](https://travis-ci.com/bummzack/swiftmailer-emogrifyplugin)
[![Latest Stable Version](https://poser.pugx.org/bummzack/swiftmailer-emogrifyplugin/v/stable)](https://packagist.org/packages/bummzack/swiftmailer-emogrifyplugin)

Inline CSS in the HTML output of SwiftMailer using [Emogrifier](https://github.com/MyIntervals/emogrifier).

## Installation and requirements

Install via composer, using:

    composer require bummzack/swiftmailer-emogrifyplugin
    
Requirements:

 - PHP 7.2+
 - SwiftMailer 6.x
 - Emogrifier 6.x
 
## Usage

By default, the plugin will inline CSS that is part of the HTML, eg. styles defined in `<style>` tags.

### Supplying custom CSS

```php
$plugin = new EmogrifierPlugin();
$plugin->setCss('.customStyle: { color: red; };');
```

### Example

Here's how you could use the plugin to send emails with custom styles loaded from a file:

```php
$plugin = new Bummzack\SwiftMailer\EmogrifyPlugin\EmogrifierPlugin();
$emogrifier->setCss(file_get_contents( /* path to your CSS file */ ));

// Create the Mailer using any Transport
$mailer = new Swift_Mailer(
    new Swift_SmtpTransport('smtp.example.org', 25)
);

// Use Emogrifier plugin to inline styles.
$mailer->registerPlugin($plugin);

$message = new Swift_Message();
$message
    ->setSubject('Your subject')
    ->setFrom(['test@example.com' => 'Test'])
    ->setTo(['receiver@example.com'])
    ->setBody('<p>My custom HTML</p>', 'text/html');

// Send your email
$mailer->send($message);
```
