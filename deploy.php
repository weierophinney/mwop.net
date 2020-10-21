<?php

namespace Deployer;

require 'recipe/common.php';

/*
 * Configuration
 */

set('allow_anonymous_stats', false);

// Project name
set('application', 'mwop.net');

// Project repository
set('repository', 'git://github.com/weierophinney/mwop.net.git');
set('branch', 'feature/deployer');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server
set('writable_dirs', ['data']);
set('writable_mode', 'chown');

// Paths to clear on completion
set('clear_paths', ['node_modules']);

// Hosts
host('testing.mwop.net')
    ->stage('production')
    ->set('deploy_path', '/var/www/{{application}}')
    ->set('http_user', 'www-data');

/*
 * Tasks
 */

/*
 * INSTALL tasks
 *
 * These MUST use callables, as otherwise, they rely on the RELEASE_PATH being
 * present... and that path WILL NOT exist on first run.
 */

desc('Install util packages');
task('install:utils', function () {
    run('
        which curl && which jq && which unzip ;
        if [ "$?" -ne "0" ];then
            apt update -y ;
            apt install -y curl jq unzip
        fi
    ');
});

desc('Install cron');
task('install:cron', function () {
    run('
        dpkg --no-pager -l cron ;
        if [ "$?" -ne "0" ];then
            echo "Installing cron for the first time" ;
             apt-get install -y cron
        fi
    ');
});

desc('Install supervisor');
task('install:supervisor', function () {
    run('
        dpkg --no-pager -l supervisor ;
        if [ "$?" -ne "0" ];then
            echo "Installing supervisor for the first time" ;
             apt-get install -y supervisor
        fi
    ');
});

desc('Install redis');
task('install:redis', function () {
    run('
        dpkg --no-pager -l redis-server ;
        if [ "$?" -ne "0" ];then
            echo "Installing Redis for the first time" ;
             apt-get install -y redis-server
        fi
        mkdir -p /var/spool/redis ;
        touch /var/spool/redis/mwop.net.rdb
    ');
});

desc('Install node');
task('install:node', function () {
    run('
        dpkg --no-pager -l nodejs ;
        if [ "$?" -ne "0" ];then
            echo "Installing node.js 10 for the first time";
            curl -sL https://deb.nodesource.com/setup_10.x | bash - ;
            apt-get install -y nodejs ;
        fi
    ');
});

desc('Install grunt');
task('install:grunt', function () {
    run('
        which grunt ;
        if [ "$?" -ne "0" ];then
            echo "Installing Grunt for the first time" ;
            npm install -g grunt-cli
        fi
    ');
});
after('install:node', 'install:grunt');

desc('Install PHP');
task('install:php', function () {
    run('
        dpkg --no-pager -l php7.4-cli ;
        if [ "$?" -ne "0" ];then
            echo "Installing PHP 7.4 for the first time" ;
            add-apt-repository -y ppa:ondrej/php ;
            apt update ;
            apt install -y php7.4-cli php7.4-bcmath php7.4-bz2 php7.4-curl php7.4-dev php7.4-gd php7.4-intl php7.4-json php7.4-ldap php7.4-mbstring php7.4-opcache php7.4-readline php7.4-sqlite3 php7.4-tidy php7.4-xml php7.4-xsl php7.4-zip ;
            update-alternatives --set php /usr/bin/php7.4
        fi
    ');
});

desc('Install Swoole');
task('install:swoole', function () {
    run('
        php -m | grep -q swoole ;
        if [ "$?" -ne "0" ];then
            echo "Installing Swoole for the first time" ;
            mkdir -p /tmp/swoole ;
            cd /tmp/swoole && curl -s -o swoole.tgz https://pecl.php.net/get/swoole-4.4.17.tgz ;
            cd /tmp/swoole && tar xzvf swoole.tgz --strip-components=1 ;
            cd /tmp/swoole && phpize ;
            cd /tmp/swoole && ./configure --enable-http2 ;
            cd /tmp/swoole && make ;
            cd /tmp/swoole && make install ;
            rm -rf /tmp/swoole ;
            echo "extension=swoole.so" > /etc/php/7.4/cli/conf.d/swoole.ini
        fi
    ');
});
after('install:php', 'install:swoole');

desc('Install Composer');
task('install:composer', function () {
    run('
        which composer ;
        if [ "$?" -ne "0" ];then
            curl https://getcomposer.org/composer-1.phar --output /usr/local/bin/composer ;
            chmod 755 /usr/local/bin/composer
        fi
    ');
});
after('install:swoole', 'install:composer');

desc('Install Caddy');
task('install:caddy', function () {
    run('
        dpkg --no-pager -l caddy ;
        if [ "$?" -ne "0" ];then
            echo "Installing Caddy for the first time" ;
            echo "deb [trusted=yes] https://apt.fury.io/caddy/ /" > /etc/apt/sources.list.d/caddy-fury.list ;
            apt update ;
            apt install -y caddy ;
        fi
    ');
});

desc('Install system dependencies');
task('install:system_dependencies', [
    'install:utils',
    'install:cron',
    'install:supervisor',
    'install:redis',
    'install:node',
    'install:php',
    'install:caddy',
]);

/*
 * SERVICE START/STOP
 */

desc('Reload Caddy with revised configuration');
task('caddy:reload', function () {
    run('
        cd {{release_path}} ;
        cat etc/caddy/Caddyfile.json | jq ".logging.logs.default.exclude[0]" | curl -X POST -H "Content-Type: application/json" -d @- http://localhost:2019/config/logging/logs/default/exclude/0/ ;
        cat etc/caddy/Caddyfile.json | jq ".logging.logs.log_mwop_net" | curl -X POST -H "Content-Type: application/json" -d @- http://localhost:2019/config/logging/logs/log_mwop_net/ ;
        cat etc/caddy/Caddyfile.json | jq ".apps.http.servers.mwop_net" | curl -X POST -H "Content-Type: application/json" -d @- http://localhost:2019/config/apps/http/servers/mwop_net/ ;
    ');
});

desc('Reload Caddy with previous configuration');
task('caddy:reload_previous', function () {
    if (! has('previous_release')) {
        return;
    }
    run('
        cd {{previous_release}} ;
        cat etc/caddy/Caddyfile.json | jq ".logging.logs.default.exclude[0]" | curl -X POST -H "Content-Type: application/json" -d @- http://localhost:2019/config/logging/logs/default/exclude/0/ ;
        cat etc/caddy/Caddyfile.json | jq ".logging.logs.log_mwop_net" | curl -X POST -H "Content-Type: application/json" -d -d @- http://localhost:2019/config/logging/logs/log_mwop_net/ ;
        cat etc/caddy/Caddyfile.json | jq ".apps.http.servers.mwop_net" | curl -X POST -H "Content-Type: application/json" -d @- http://localhost:2019/config/apps/http/servers/mwop_net/ ;
    ');
});

desc('Stop swoole instance');
task('swoole:stop', function () {
    run('
        ps ax | grep -v grep | grep -q supervisord ;
        if [ "$?" == "0" ];then
            supervisorctl avail | grep -v grep | grep -q mwop_net:mwopnet ;
            if [ "$?" == "0" ];then
                supervisorctl stop mwop_net:mwopnet ;
            fi
        fi
    ');
});

desc('Start swoole instance');
task('swoole:start', function () {
    run('
        ps ax | grep -v grep | grep -q supervisord ;
        if [ "$?" != "0" ];then
            service supervisor start ;
        else
            supervisorctl restart mwop_net:mwopnet ;
        fi
    ');
});

desc('Restart web server');
task('webserver:restart', [
    'swoole:stop',
    'swoole:start',
    'caddy:reload',
]);

/*
 * BUILD TASKS
 */

desc('Prepare application configuration');
task('deploy:app_config', 'mv config/autoload/local.php.dist config/autoload/local.php');

desc('Update php.ini');
task('deploy:install_php_ini', 'cp etc/php/mwop.ini /etc/php/7.4/cli/conf.d');

desc('Rollback php.ini');
task('rollback:php_ini', function () {
    if (! has('previous_release')) {
        return;
    }
    run('cp {{previous_release}}/etc/php/mwop.ini /etc/php/7.4/cli/conf.d');
});

desc('Install redis configuration');
task('deploy:install_redis_conf', '
    cp etc/redis/redis.conf /etc/redis/redis.conf ;
    service redis-server restart
');

desc('Rollback redis configuration');
task('rollback:redis_conf', function () {
    if (! has('previous_release')) {
        return;
    }
    run('
        cp {{previous_release}}/etc/redis/redis.conf /etc/redis/redis.conf ;
        service redis-server restart
    ');
});

desc('Install supervisor configuration');
task('deploy:install_supervisor_conf', '
    sed --in-place -e "s#%release_path%#{{release_path}}#g" etc/supervisor/mwop.net.conf ;
    cp etc/supervisor/mwop.net.conf /etc/supervisor/conf.d/mwop.net.conf ;
');

desc('Rollback supervisor configuration');
task('rollback:supervisor_conf', function () {
    if (! has('previous_release')) {
        return;
    }
    run('cp {{previous_release}}/etc/supervisor/mwop.net.conf /etc/supervisor/conf.d/mwop.net.conf');
});

desc('Deploy cronjob');
task('deploy:cronjob', '
    sed --in-place -e "s#%release_path%#{{release_path}}#" etc/cron.d/mwopnet;
    cp etc/cron.d/mwopnet /etc/cron.d/mwopnet;
');

desc('Rollback cronjob');
task('rollback:cronjob', function () {
    if (! has('previous_release')) {
        return;
    }
    run('cp {{previous_release}}/etc/cron.d/mwop.net /etc/cron.d/mwopnet');
});

desc('Upload env');
task('upload:env', function () {
    write('Uploading production env');
    upload('.prod.env', '{{release_path}}/.env');
});

desc('Build js and css');
task('build:grunt', '
    npm install ;
    cp node_modules/bootstrap/dist/js/bootstrap.js public/js/ ;
    cp node_modules/jquery/dist/jquery.js public/js/ ;
    cp node_modules/autocomplete.js/dist/autocomplete.jquery.js public/js/ ;
    grunt
');

desc('Build blog');
task('build:blog', 'sudo -u www-data composer build:blog');

desc('Build homepage');
task('build:homepage', 'sudo -u www-data composer build:homepage');

desc('Fetch instagram feed');
task('build:instagram', 'sudo -u www-data php bin/mwop.net.php instagram-feeds');

desc('Fetch comics');
task('build:comics', 'sudo -u www-data php vendor/bin/phly-comic.php fetch-all -p --output data/comics.phtml --exclude dilbert --exclude reptilis-rex --exclude nih');

// Copy asset templates
desc('Prepare asset templates');
task('deploy:assets', 'php bin/mwop.net.php asset:use-dist-templates');

desc('Build');
task('build', [
    'deploy:app_config',
    'deploy:install_php_ini',
    'deploy:install_redis_conf',
    'deploy:install_supervisor_conf',
    'deploy:cronjob',
    'build:grunt',
    'upload:env',
    'build:blog',
    'build:homepage',
    'build:instagram',
    'build:comics',
    'deploy:assets',
]);

desc('Rollback on failure');
task('failure:tasks', [
    'deploy:unlock',
    'rollback:php_ini',
    'rollback:redis_conf',
    'rollback:supervisor_conf',
    'rollback:cronjob',
    'swoole:start',
    'caddy:reload_previous',
]);

/*
 * Deployment
 */

// Full deployment
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// WORKFLOW
after('deploy:prepare', 'install:system_dependencies');
after('deploy:vendors', 'build');
before('deploy:unlock', 'webserver:restart');
after('deploy:failed', 'failure:tasks');
