<?php
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$c = new \Slim\Container([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$c['view'] = function ($c) {
    $view = new \Slim\Views\Twig('src/twig', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    return $view;
};

$app = new \Slim\App($c);

// Permanently redirect paths with a trailing slash to their non-trailing counterpart
// Source: https://www.slimframework.com/docs/v3/cookbook/route-patterns.html
$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        $uri = $uri->withPath(substr($path, 0, -1));

        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

$app->get('/', function ($request, $response, $args) {
    return $this->view->render(
        $response,
        'home.twig',
        json_decode(file_get_contents('src/data/projects.json'), true)
    );
})->setName('home');

$app->get('/projects[/{slug}]', function ($request, $response, $args) {
    try {
        if (!isset($args['slug'])) {
            return $response->withRedirect('/');
        } else {
            return $this->view->render($response, 'projects/' . $args['slug'] . '.twig');
        }
    } catch (Exception $e) {
        // If the matching template can't be found, that's functionally a 404 error
        if (get_class($e) === 'Twig_Error_Loader') {
            throw new \Slim\Exception\NotFoundException($request, $response);
        } else {
            throw $e;
        }
    }
})->setName('projects');

$app->get('/{page}', function ($request, $response, $args) {
    try {
        return $this->view->render($response, $args['page'] . '.twig');
    } catch (Exception $e) {
        // If the matching template can't be found, that's functionally a 404 error
        if (get_class($e) === 'Twig_Error_Loader') {
            throw new \Slim\Exception\NotFoundException($request, $response);
        } else {
            throw $e;
        }
    }
});

$app->run();
