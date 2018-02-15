<?php


namespace Bummzack\SwiftMailer\EmogrifyPlugin\Tests;

use Bummzack\SwiftMailer\EmogrifyPlugin\EmogrifierPlugin;
use Pelago\Emogrifier;

class EmogrifierPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testGetterSetter()
    {
        $plugin = new EmogrifierPlugin();

        // The default is an emogrifier instance
        $this->assertInstanceOf(Emogrifier::class, $plugin->getEmogrifier());

        $newInstance = new Emogrifier();
        $plugin->setEmogrifier($newInstance);
        $this->assertEquals($newInstance, $plugin->getEmogrifier());

        $plugin = new EmogrifierPlugin($newInstance);
        $this->assertEquals($newInstance, $plugin->getEmogrifier());
    }

    public function testBodyOnly()
    {
        $html = '<style>.test { color: red; }</style><p class="test">Hello World</p>';

        $message = $this->createMockedMessage($html);
        $event = $this->createMockedSendEvent($message);

        $plugin = new EmogrifierPlugin();
        $emogrifier = $this->getMockBuilder(Emogrifier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $emogrifier->expects($this->once())
            ->method('setHtml')
            ->with(
                $this->equalTo($html)
            );
        $emogrifier->expects($this->once())
            ->method('emogrify');

        $plugin->setEmogrifier($emogrifier);
        $plugin->beforeSendPerformed($event);
    }

    public function testWithMessagePart()
    {
        $htmlBody = '<p>MessageBody</p>';
        $htmlPart = '<p>MessagePart</p>';

        $part1 = $this->createMockedMessagePart($htmlPart);
        $part2 = $this->createMockedMessagePart('Plain text', 'text/plain');

        $message = $this->createMockedMessage($htmlBody, 'text/html', [$part1, $part2]);
        $event = $this->createMockedSendEvent($message);

        $plugin = new EmogrifierPlugin();

        $emogrifier = $this->getMockBuilder(Emogrifier::class)
            ->disableOriginalConstructor()
            ->getMock();

        $emogrifier->expects($this->any())
            ->method('setHtml')
            ->withConsecutive(
                $this->equalTo($htmlBody),
                $this->equalTo($htmlPart)
            );

        $emogrifier->expects($this->exactly(2))
            ->method('emogrify');


        $plugin->setEmogrifier($emogrifier);
        $plugin->beforeSendPerformed($event);
    }

    public function testEmptyBodies()
    {
        $message = $this->createMockedMessage('', 'text/html', [
            $this->createMockedMessagePart('')
        ]);

        $event = $this->createMockedSendEvent($message);

        $plugin = new EmogrifierPlugin();

        $emogrifier = $this->getMockBuilder(Emogrifier::class)
            ->disableOriginalConstructor()
            ->getMock();

        $emogrifier->expects($this->never())
            ->method('setHtml');

        $emogrifier->expects($this->never())
            ->method('emogrify');


        $plugin->setEmogrifier($emogrifier);
        $plugin->beforeSendPerformed($event);
    }

    public function testSendPerformed()
    {
        $event = $this->createMockedSendEvent($this->createMockedMessage(''));

        $plugin = new EmogrifierPlugin();
        $this->assertEmpty($plugin->sendPerformed($event));
    }

    /**
     * Build a mocked send-event that will return the given message
     * @param $message
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockedSendEvent($message)
    {
        $event = $this->getMockBuilder('Swift_Events_SendEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($message));

        return $event;
    }

    /**
     * Build a mocked message part
     * @param $body
     * @param string $contentType
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockedMessagePart($body, $contentType = 'text/html')
    {
        $messagePart = $this->getMockBuilder('Swift_Mime_MimeEntity')
            ->disableOriginalConstructor()
            ->getMock();

        $messagePart->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));

        $messagePart->expects($this->any())
            ->method('getContentType')
            ->will($this->returnValue($contentType));

        return $messagePart;
    }

    /**
     * Build a mocked swift message
     * @param $body
     * @param string $contentType
     * @param array $messageParts
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockedMessage($body, $contentType = 'text/html', array $messageParts = [])
    {
        $message = $this->getMockBuilder('Swift_Mime_Message')
            ->disableOriginalConstructor()
            ->getMock();

        $message->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));

        $message->expects($this->any())
            ->method('getContentType')
            ->will($this->returnValue($contentType));

        $message->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue($messageParts));

        return $message;
    }
}
