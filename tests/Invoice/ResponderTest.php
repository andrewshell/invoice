<?php declare(strict_types = 1);

namespace Invoice;

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;

class ResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $responder;
    protected $payload;

    public function setup()
    {
        $loader = new \Twig_Loader_Array([
            '/app/views/default.twig.html' => 'default:{{ content }}',
            '/app/views/notfound.twig.html' => 'notfound:{{ message }}',
        ]);
        $twig = new \Twig_Environment($loader);

        $this->responder = new Responder($twig);
    }

    public function testAccepts()
    {
        $expect = ['text/html'];
        $actual = Responder::accepts();
        $this->assertSame($expect, $actual);
    }

    protected function getResponse($payload)
    {
        $request = ServerRequestFactory::fromGlobals()->withAttribute('_view', '/app/views/default.twig.html');
        $response = new Response();
        return $payload
            ? $this->responder->__invoke($request, $response, $payload)
            : $this->responder->__invoke($request, $response);
    }

    protected function assertPayloadResponse($payload, $status, array $headers, $body)
    {
        $response = $this->getResponse($payload);

        $this->assertEquals($status, $response->getStatusCode());

        foreach ($headers as $header => $expect) {
            $this->assertEquals((array) $expect, $response->getHeader($header));
        }

        ob_start();
        echo $response->getBody();
        $actual = ob_get_clean();

        $this->assertEquals($body, $actual);
    }

    public function testNotFound()
    {
        $payload = [
            'success' => false,
            'message' => 'any message',
        ];

        $this->assertPayloadResponse(
            $payload,
            404,
            ['Content-Type' => 'text/html'],
            'notfound:' . $payload['message']
        );
    }

    public function testSuccess()
    {
        $payload = [
            'success' => true,
            'content' => 'any content',
        ];

        $this->assertPayloadResponse(
            $payload,
            200,
            ['Content-Type' => 'text/html'],
            'default:' . $payload['content']
        );
    }
}
