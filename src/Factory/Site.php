<?php
namespace Mwop\Factory;

use Mwop\Job\Middleware as Jobs;
use Phly\Conduit\Middleware;

class Site
{
    public function __invoke($services)
    {
        $site      = new Middleware();
        $templated = $services->get('Mwop\Templated');

        // Home page
        $templated->pipe('/', $services->get('Mwop\HomePage'));

        // Blog
        $templated->pipe('/blog', function ($req, $res, $next) use ($services) {
            $blog = $services->get('Mwop\Blog\Middleware');
            return $blog($req, $res, $next);
        });

        // Contact form
        $templated->pipe('/contact', function ($req, $res, $next) use ($services) {
            $contact = $services->get('Mwop\Contact\Middleware');
            $contact($req, $res, $next);
        });

        // Comics
        $templated->pipe('/comics', function ($req, $res, $next) use ($services) {
            $comics = new Middleware();
            $comics->pipe($services->get('Mwop\Auth\UserSession'));
            $comics->pipe($services->get('Mwop\ComicsPage'));
            return $comics($req, $res, $next);
        });

        // Resume
        $templated->pipe('/resume', $services->get('Mwop\ResumePage'));

        $site->pipe($templated);

        // Authentication (opauth)
        $site->pipe('/auth', function ($req, $res, $next) use ($services) {
            $auth = $services->get('Mwop\Auth\Middleware');
            return $auth($req, $res, $next);
        });

        // Job Queue jobs
        $site->pipe('/jobs', function ($req, $res, $next) {
            $jobs = new Jobs();
            return $jobs($req, $res, $next);
        });


        return $site;
    }
}
