<?php
require_once __DIR__ . '/../library/zf2/Zend/Loader/ClassMapAutoloader.php';
$classmap = new Zend\Loader\ClassMapAutoloader(array(
    __DIR__ . '/../library/.classmap.php',
    __DIR__ . '/../application/.classmap.php',
));
$classmap->register();

require_once __DIR__ . '/../library/Phly/Mustache/_autoload.php';

$logger = Zend\Log\Logger::factory(array(
    array(
        'writerName'   => 'Stream',
        'writerParams' => array(
            'stream'   => '/tmp/blog.zf2.log',
        ),
        'formatterName' => 'Simple',
        'filterParams' => array(
            'format'   => '%timestamp%: %message% -- %info%',
        ),
    ),
));
$logger->info('Logger initialized');

$router = new mwop\Mvc\Router();
$router->addRoute(new mwop\Mvc\Router\RegexRoute(
    '#^/(?P<controller>blog)/admin/(?P<action>create)#',
    '/blog/admin/create'
), 'blog-create-form');
$router->addRoute(new mwop\Mvc\Router\RegexRoute(
    '#^/(?P<controller>blog)(/(?P<id>[^/]+))?#',
    '/blog/{id}'
), 'blog');

$front = new mwop\Controller\Front();
$front->addControllerMap('blog', 'Blog\Controller\Entry')
      ->router($router);

$view = new Phly\Mustache\Mustache();
$view->setTemplatePath(__DIR__ . '/../application/views');
$view->getRenderer()->addPragma(new Phly\Mustache\Pragma\ImplicitIterator());
$subViews = new Phly\Mustache\Pragma\SubViews($view);

$events = Zend\EventManager\StaticEventManager::getInstance();
$events->attach('mwop\Controller\Restful', 'dispatch.post', function($e) use ($view, $router) {
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
            $params['updated'] = true;
            break;
        case 'delete':
            $response->getHeaders()->setStatusCode(204);
            $template .= 'delete';
            break;
        default:
            break;
    }
    $subView = new Phly\Mustache\Pragma\SubView($template, $params);
    $response->setContent($view->render('layout', array('content' => $subView)));
});

$request  = new Zend\Http\Request();
$response = $front->dispatch($request);
$response->send();
