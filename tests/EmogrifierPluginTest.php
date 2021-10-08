<?php


namespace Bummzack\SwiftMailer\EmogrifyPlugin\Tests;

use Bummzack\SwiftMailer\EmogrifyPlugin\EmogrifierPlugin;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class EmogrifierPluginTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testBodyOnly()
    {
        $html = '<style>.test { color: red; }</style><p class="test">Hello World</p>';

        $plugin = new EmogrifierPlugin();

        $message = $this->createMessage($html);
        $message->shouldReceive('setBody')
            ->once()
            ->with('<p class="test" style="color: red;">Hello World</p>');

        $evt = $this->createSendEvent($message);

        $plugin->beforeSendPerformed($evt);
        $plugin->sendPerformed($evt);
    }

    public function testWithMessagePart()
    {
        $htmlBody = '<style>p { color: red; }</style><p>MessageBody</p>';
        $htmlPart = '<p>MessagePart</p>';

        $part1 = $this->createMessagePart($htmlPart);
        $part2 = $this->createMessagePart('Plain text', 'text/plain');

        $message = $this->createMessage($htmlBody, 'text/html', [$part1, $part2]);

        $message->shouldReceive('setBody')
            ->once()
            ->with('<p style="color: red;">MessageBody</p>');

        $part1->shouldReceive('setBody')
            ->once()
            ->with('<p>MessagePart</p>');

        $part2->shouldNotHaveReceived('setBody');

        $event = $this->createSendEvent($message);

        $plugin = new EmogrifierPlugin();

        $plugin->beforeSendPerformed($event);
    }

    public function testWithCss()
    {
        $css = 'div { color: green; } p { color: red; }';
        $htmlBody = '<div>MessageBody</div>';
        $htmlPart = '<p>MessagePart</p>';

        $part = $this->createMessagePart($htmlPart);
        $message = $this->createMessage($htmlBody, 'text/html', [$part]);

        $message->shouldReceive('setBody')
            ->once()
            ->with('<div style="color: green;">MessageBody</div>');

        $part->shouldReceive('setBody')
            ->once()
            ->with('<p style="color: red;">MessagePart</p>');

        $event = $this->createSendEvent($message);

        $plugin = new EmogrifierPlugin();
        $this->assertNull($plugin->getCss());
        $plugin->setCss($css);
        $this->assertEquals($css, $plugin->getCss());

        $plugin->beforeSendPerformed($event);
    }

    public function testEmptyBodies()
    {
        $message = $this->createMessage('', 'text/html', [
            $part = $this->createMessagePart('')
        ]);

        $part->shouldNotHaveReceived('setBody');
        $message->shouldNotHaveReceived('setBody');

        $event = $this->createSendEvent($message);

        $plugin = new EmogrifierPlugin();
        $plugin->beforeSendPerformed($event);
    }

    public function testSendPerformed()
    {
        $event = $this->createSendEvent($msg = $this->createMessage(''));
        $msg->shouldNotReceive('getBody', 'getContentType', 'getChildren');

        $plugin = new EmogrifierPlugin();
        $plugin->sendPerformed($event);
    }

    protected function createMessage($body = '', $mimeType = 'text/html', $parts = [])
    {
        $message = $this->getMockery('Swift_Mime_SimpleMessage')->shouldIgnoreMissing();
        $message->shouldReceive('getContentType')
            ->zeroOrMoreTimes()
            ->andReturn($mimeType);
        $message->shouldReceive('getBody')
            ->zeroOrMoreTimes()
            ->andReturn($body);
        $message->shouldReceive('getChildren')
            ->zeroOrMoreTimes()
            ->andReturn($parts);

        return $message;
    }

    protected function createMessagePart($body = '', $mimeType = 'text/html')
    {
        $part = $this->getMockery('Swift_Mime_SimpleMimeEntity')->shouldIgnoreMissing();
        $part->shouldReceive('getContentType')
            ->zeroOrMoreTimes()
            ->andReturn($mimeType);
        $part->shouldReceive('getBody')
            ->zeroOrMoreTimes()
            ->andReturn($body);
        return $part;
    }

    protected function createSendEvent($message)
    {
        $evt = $this->getMockery('Swift_Events_SendEvent')->shouldIgnoreMissing();
        $evt->shouldReceive('getMessage')
            ->zeroOrMoreTimes()
            ->andReturn($message);

        return $evt;
    }

    protected function getMockery($class)
    {
        return \Mockery::mock($class);
    }
}
