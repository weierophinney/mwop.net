<?php
ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);
require_once __DIR__ . '/../library/zf2/Zend/Loader/ClassMapAutoloader.php';
$classmap = new Zend\Loader\ClassMapAutoloader(array(
    __DIR__ . '/../library/.classmap.php',
    __DIR__ . '/../application/.classmap.php',
));
$classmap->register();

require_once __DIR__ . '/../library/Phly/Mustache/_autoload.php';

use mwop\Controller\Front as FrontController;

$events = Zend\EventManager\StaticEventManager::getInstance();

$log = Zend\Log\Logger::factory(array(
    'writer' => array(
        'writerName'   => 'Stream',
        'writerParams' => array(
            'stream' => '/tmp/blog.log',
        ),
    )
));

$app   = new AppContext();
$front = new mwop\Controller\Front($app);
$front->addControllerMap('blog', 'Blog\Controller\Entry')
      ->addControllerMap('page', 'Site\Controller\Page');

$router = $app->get('router');
$layout = $app->get('presentation');

$view = new Phly\Mustache\Mustache();
$view->setTemplatePath(__DIR__ . '/../application/views');
$view->getRenderer()->addPragma(new Phly\Mustache\Pragma\ImplicitIterator());
$subViews = new Phly\Mustache\Pragma\SubViews($view);

$events->attach('mwop\Controller\Restful', 'dispatch.post', function($e) use ($layout, $router, $view) {
    $request    = $e->getParam('request');
    $response   = $e->getParam('response');
    $params     = $e->getParam('__RESULT__');
    $controller = $request->getMetadata('controller');
    $template   = $controller . '/';
    switch (strtolower($request->getMethod())) {
        case 'get':
            if ('create' == $request->getMetadata('action')) {
                $template .= 'form';
                break;
            }
            if (null !== $id = $request->getMetadata('id')) {
                $template .= 'entity';
                break;
            }
            $template .= 'list';
            break;
        case 'post':
            if (!$params['success']) {
                $template .= 'form';
                break;
            }
            $template .= 'entity';
            $url       = $router->assemble(
                array('id'   => $params['entity']['id']),
                array('name' => 'blog')
            );
            $response->getHeaders()->setStatusCode(201)
                                   ->addHeader('Location', $url);
            break;
        case 'update':
            $template .= 'entity';
            $params['entity_updated'] = true;
            break;
        case 'delete':
            $response->getHeaders()->setStatusCode(204);
            $template .= 'delete';
            break;
        default:
            break;
    }

    if ($request->isXmlHttpRequest()) {
        $response->setContent($view->render($template, $params));
    } else {
        $subView = new Phly\Mustache\Pragma\SubView($template, $params);

        $layout->content = $subView;
        $response->setContent($view->render($layout->layout(), $layout));
    }
});

$events->attach('Site\Controller\Page', 'dispatch.post', function($e) use ($layout, $view, $router) {
    $request    = $e->getParam('request');
    $response   = $e->getParam('response');
    $page       = $e->getParam('__RESULT__');
    $template   = 'pages/' . $page;

    if (404 == $page) {
        $response->getHeaders()->setStatusCode(404);
    }

    if ($request->isXmlHttpRequest()) {
        $response->setContent($view->render($template));
    } else {
        $layout->content = new Phly\Mustache\Pragma\SubView($template);
        $response->setContent($view->render($layout->layout(), $layout));
    }
});

$front->setNotFoundHandler(function($request, $response) use ($layout, $view) {
    $response->getHeaders()->setStatusCode(404);

    $template = 'pages/404';

    if ($request->isXmlHttpRequest()) {
        $response->setContent($view->render($template));
    } else {
        $subView = new Phly\Mustache\Pragma\SubView($template);
        $layout->content = $subView;
        $response->setContent($view->render($layout->layout(), $layout));
    }
});

$cache = new Zend\Cache\Manager();
$cache->getCache('default')->getBackend()->setCacheDir(__DIR__ . '/../cache/static');

$front->events()->attach('dispatch.router.post', function($e) use ($cache) {
    $request = $e->getParam('request');
    if (!$request instanceof Zend\Http\Request || !$request->isGet()) {
        return;
    }

    $metadata = json_encode(array_merge($request->getMetadata(), $request->query()->toArray()));
    $key      = hash('md5', $metadata);
    /*
    $backend  = $cache->getCache('default');
    if (false !== ($content = $backend->load($key))) {
        $response = $e->getParam('response');
        $response->setContent($content);
        return $response;
    }
     */
    return;
}, 100);

$front->events()->attach('dispatch.post', function($e) use ($cache) {
    $request = $e->getParam('request');
    if (!$request instanceof Zend\Http\Request || !$request->isGet()) {
        return;
    }

    /*
    $metadata = json_encode(array_merge($request->getMetadata(), $request->query()->toArray()));
    $key      = hash('md5', $metadata);
    $backend  = $cache->getCache('default');
    $response = $e->getParam('response');
    $backend->save($response->getContent(), $key);
     */
}, -100);

$request  = $app->get('request');
$response = $front->dispatch($request);
$response->send();
