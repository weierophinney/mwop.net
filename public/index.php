<?php
require_once __DIR__ . '/../library/zf2/Zend/Loader/ClassMapAutoloader.php';
$classmap = new Zend\Loader\ClassMapAutoloader(array(
    __DIR__ . '/../library/.classmap.php',
    __DIR__ . '/../application/.classmap.php',
));
$classmap->register();

require_once __DIR__ . '/../library/Phly/Mustache/_autoload.php';

use Zend\Log\Logger,
    mwop\Controller\Front as FrontController,
    Zend\Di\DependencyInjectionContainer as DiC,
    Zend\Di\Definition as DiDefinition,
    Zend\Di\Reference as DiReference;

$logger = Logger::factory(array(
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

$di = new DiC();
$injector = $di->getInjector();

$blogCreateFormRoute = new DiDefinition('mwop\Mvc\Router\RegexRoute');
$blogCreateFormRoute->setParam('regex', '#^/(?P<controller>blog)/admin/(?P<action>create)#')
                    ->setParam('spec', '/blog/admin/create');
$blogRoute = new DiDefinition('mwop\Mvc\Router\RegexRoute');
$blogRoute->setParam('regex', '#^/(?P<controller>blog)(/(?P<id>[^/]+))?#')
          ->setParam('spec', '/blog/{id}');
$router = new DiDefinition('mwop\Mvc\Router');
$router->addMethodCall('addRoutes', array(
    array(
        'blog-create-form' => new DiReference('route-blog-create-form'),
        'blog'             => new DiReference('route-blog'),
    ),
));

$injector->setDefinition($blogCreateFormRoute, 'route-blog-create-form')
         ->setDefinition($blogRoute, 'route-blog')
         ->setDefinition($router, 'router');

$di->set('mongo-collection-entries', function() {
    $mongo      = new Mongo();
    $mongoDb    = $mongo->mwoptest;
    $collection = $mongoDb->entries;
    return $collection;
});
$mongo          = new DiDefinition('mwop\DataSource\Mongo');
$mongo->setParam('options', new DiReference('mongo-collection-entries'))
      ->setParamMap(array('options' => 0));
$entryResource  = new DiDefinition('mwop\Resource\Entry');
$entryResource->addMethodCall('setDataSource', array(
                    new DiReference('data-source'),
                ))
              ->addMethodCall('setCollectionClass', array('mwop\Resource\MongoCollection'));
$blogController = new DiDefinition('Blog\Controller\Entry');
$blogController->addMethodCall('resource', array(new DiReference('resource-entry')));

$injector->setDefinition($mongo, 'data-source')
         ->setDefinition($entryResource, 'resource-entry')
         ->setDefinition($blogController);

$front = new mwop\Controller\Front($di);
$front->addControllerMap('blog', 'Blog\Controller\Entry');

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
