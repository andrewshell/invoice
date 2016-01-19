<?php
declare(strict_types = 1);

namespace Cadre\Core\Responder;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Radar\Adr\Responder\ResponderAcceptsInterface;
use Twig_Environment;

class TwigResponder implements ResponderAcceptsInterface
{
    protected $request;

    protected $response;

    protected $payload;

    protected $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public static function accepts(): array
    {
        return ['text/html'];
    }

    public function __invoke(
        Request $request,
        Response $response,
        array $payload = null
    ): Response {
        $this->request = $request;
        $this->response = $response;
        $this->payload = $payload;
        if (true === $this->payload['success']) {
            $this->success();
        } else {
            $this->notFound();
        }
        return $this->response;
    }

    protected function htmlBody(array $data)
    {
        if (isset($data)) {
            $view = $this->request->getAttribute('_view', '/app/views/layout.twig.html');
            $template = $this->twig->loadTemplate($view);
            $body = $template->render($data);
            $this->response = $this->response->withHeader('Content-Type', 'text/html');
            $this->response->getBody()->write($body);
        }
    }

    protected function success()
    {
        $this->response = $this->response->withStatus(200);
        $this->htmlBody($this->payload);
    }

    protected function notFound()
    {
        $this->response = $this->response->withStatus(404);
        $this->request = $this->request->withAttribute('_view', '/app/views/notfound.twig.html');
        $this->htmlBody($this->payload);
    }
}
