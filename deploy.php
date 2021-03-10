<?php

namespace Deployer;

require 'recipe/common.php';

/*
 * Configuration
 */

set('allow_anonymous_stats', false);
set('bin/php', '/usr/bin/php8.0');

// Project name
set('application', 'mwop.net');

// Project repository
set('repository', 'git://github.com/weierophinney/mwop.net.git');
set('branch', 'main');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server
set('writable_dirs', ['data']);
set('writable_mode', 'chown');

// Hosts
host('mwop.net')
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

desc('Update package sources');
task('install:prepare', function () {
    run('apt update -y');
});

desc('Install util packages');
task('install:utils', function () {
    run('apt install -y curl unzip');
});

desc('Install cron');
task('install:cron', function () {
    run('apt install -y cron');
});

desc('Install supervisor');
task('install:supervisor', function () {
    run('apt install -y supervisor');
});

desc('Install redis');
task('install:redis', function () {
    run('
        apt install -y redis-server ;
        if [[ ! -f /var/spool/redis/mwop.net.rdb ]];then
            mkdir -p /var/spool/redis ;
            touch /var/spool/redis/mwop.net.rdb
        fi
    ');
});

desc('Install PHP');
task('install:php', function () {
    // Depending on PHP version, php{VERSION}-json package may be required
    // Note: "apt install" will upgrade a package if already installed and a newer version exists.
    run('
        if [[ ! "$(ls -l /etc/apt/sources.list.d/)" =~ ondrej-.*?\.list[^.] ]]
        then
            echo "Installing ondrej PPA for the first time" ;
            add-apt-repository -y ppa:ondrej/php
        fi
        apt install -y php8.0-cli php8.0-bcmath php8.0-bz2 php8.0-curl php8.0-dev php8.0-gd php8.0-intl php8.0-ldap php8.0-mbstring php8.0-opcache php8.0-readline php8.0-sqlite3 php8.0-tidy php8.0-xml php8.0-xsl php8.0-zip ;
    ');
});

desc('Install Swoole');
task('install:swoole', function () {
    run('
        if [[ ! "$({{bin/php}} -m)" =~ "swoole" || "$({{bin/php}} -r "echo swoole_version();")" != "4.6.3" ]];then
            echo "Installing Swoole 4.6.3 for the first time" ;
            mkdir -p /tmp/swoole ;
            update-alternatives --set php-config /usr/bin/php-config8.0
            update-alternatives --set phpize /usr/bin/phpize8.0
            cd /tmp/swoole && curl -s -o swoole.tgz https://pecl.php.net/get/swoole-4.6.3.tgz ;
            cd /tmp/swoole && tar xzvf swoole.tgz --strip-components=1 ;
            cd /tmp/swoole && phpize ;
            cd /tmp/swoole && ./configure --enable-swoole --enable-http2 --enable-sockets --enable-openssl --enable-swoole-json --enable-swoole-curl ;
            cd /tmp/swoole && make ;
            cd /tmp/swoole && make install ;
            rm -rf /tmp/swoole ;
            echo "extension=swoole.so" > /etc/php/8.0/cli/conf.d/swoole.ini
        fi
    ');
});
after('install:php', 'install:swoole');

desc('Install Composer');
task('install:composer', function () {
    run('
        if [[ ! -x /usr/local/bin/composer || ! "$(composer --no-ansi --version)" =~ "^Composer version 2" ]];then
            echo "Installing Composer for the first time" ;
            curl https://getcomposer.org/composer.phar --output /usr/local/bin/composer ;
            chmod 755 /usr/local/bin/composer
        fi
    ');
});
after('install:swoole', 'install:composer');
set(
    'composer_options',
    '--verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader --ignore-platform-req=php'
);

desc('Install Caddy');
task('install:caddy', function () {
    run('
        if [[ ! "$(ls -l /etc/apt/sources.list.d/)" =~ caddy-fury\.list[^.] ]]
            echo "Installing Caddy repo for the first time" ;
            echo "deb [trusted=yes] https://apt.fury.io/caddy/ /" > /etc/apt/sources.list.d/caddy-fury.list ;
            apt update ;
            mkdir -p /etc/caddy/conf.d ;
            echo "import /etc/caddy/conf.d/*.caddy" > /etc/caddy/Caddyfile ;
        fi
        apt install -y caddy ;
    ');
});

desc('Install system dependencies');
task('install:system_dependencies', [
    'install:utils',
    'install:cron',
    'install:supervisor',
    'install:redis',
    'install:php',
    'install:caddy',
]);
before('install:system_dependencies', 'install:prepare');

/*
 * SERVICE START/STOP
 */

desc('Reload Caddy with revised configuration');
task('caddy:reload', function () {
    within('{{release_path}}', function () {
        run('
            cp etc/caddy/mwop.net.caddy /etc/caddy/conf.d/ ;
            systemctl restart caddy ;
        ');
    });
});

desc('Reload Caddy with previous configuration');
task('caddy:reload_previous', function () {
    if (! has('previous_release')) {
        return;
    }
    within('{{previous_release}}', function () {
        run('
            cp etc/caddy/mwop.net.caddy /etc/caddy/conf.d/ ;
            systemctl restart caddy ;
        ');
    });
});

desc('Stop swoole instance');
task('swoole:stop', function () {
    run('
        if [[ "$(ps ax)" =~ "supervisord" ]];then
            systemctl stop supervisor ;
        fi
    ');
});

desc('Start swoole instance');
task('swoole:start', function () {
    run('
        if [[ ! "$(ps ax)" =~ "supervisord" ]];then
            systemctl start supervisor ;
        else
            systemctl stop supervisor ;
            systemctl start supervisor ;
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
task('deploy:install_php_ini', 'cp etc/php/mwop.ini /etc/php/8.0/cli/conf.d');

desc('Rollback php.ini');
task('rollback:php_ini', function () {
    if (! has('previous_release')) {
        return;
    }
    run('cp {{previous_release}}/etc/php/mwop.ini /etc/php/8.0/cli/conf.d');
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
    run('cp {{previous_release}}/etc/cron.d/mwopnet /etc/cron.d/mwopnet');
});

desc('Upload env');
task('upload:env', function () {
    upload('.prod.env', '{{release_path}}/.env');
});

desc('Build assets');
task('build:assets', function () {
    runLocally('make assets');
});

desc('Build blog');
task('build:blog', 'sudo -u www-data {{bin/composer}} build:blog');

desc('Build homepage');
task('build:homepage', 'sudo -u www-data {{bin/composer}} build:homepage');

desc('Fetch instagram feed');
task('build:instagram', 'sudo -u www-data {{bin/php}} vendor/bin/laminas instagram-feeds');

desc('Fetch comics');
task('build:comics', 'sudo -u www-data {{bin/php}} vendor/bin/phly-comic.php fetch-all -p --output data/comics.phtml --exclude dilbert --exclude reptilis-rex --exclude nih');

// Copy asset templates
desc('Deploy assets');
task('deploy:assets', function () {
    upload('assets/build/js', '{{release_path}}/public');
    upload('assets/build/css', '{{release_path}}/public');
});

desc('Build');
task('build', [
    'deploy:app_config',
    'deploy:install_php_ini',
    'deploy:install_redis_conf',
    'deploy:install_supervisor_conf',
    'deploy:cronjob',
    'upload:env',
    'build:assets',
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
