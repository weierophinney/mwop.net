---
id: 2016-06-30-aws-codedeploy
author: matthew
title: 'Push-to-Deploy with AWS CodeDeploy'
draft: false
public: true
created: '2016-06-30T14:10:00-05:00'
updated: '2016-06-30T14:10:00-05:00'
tags:
    - aws
    - devops
    - php
    - programming
---
[AWS CodeDeploy](https://aws.amazon.com/codedeploy/) is a tool for automating
application deployments to EC2 instances and clusters. It can pull application
archives from either S3 or GitHub, and then allows you to specify how to
install, configure, and run the application via a configuration specification
and optionally hook scripts. When setup correctly, it can provide a powerful way
to automate your deployments.


I started looking into it because I wanted to try out my site on PHP 7, and do a
few new things with nginx that I wasn't doing before. Additionally, I've
accidently forgotten to deploy a few times in the past year after writing a blog
post, and I wanted to see if I solve that situation; I'd really enjoyed the
"push-to-deploy" paradigm of OpenShift and EngineYard in the past, and wanted to
see if I could recreate it.

[Enrico](http://www.zimuel.it) first pointed me to the service, and I was later
[inspired by a slide deck by Ric Harvey](https://docs.google.com/presentation/d/19r3BCzBmFViJP3YHisn4miJBz2Oq0EH4bbpVDhjWZXQ/edit).
The process wasn't easy, due to a number of things that are not documented or
not fully documented in the AWS CodeDeploy documentation, but in the end, I was
able to accomplish exactly that: push-to-deploy. This post details what I found,
some recommendations on how to create your deployments, and ways to avoid some
of the pitfalls I fell into.

<!--- EXTENDED -->

## Preparing for CodeDeploy on AWS

The first thing you need to do is setup a whole slew of profiles, roles, and
policies on AWS.  The
[AWS CodeDeploy Getting Started guide](http://docs.aws.amazon.com/codedeploy/latest/userguide/getting-started-setup.html)
walks you through the various details of that. While it's not trivial or easy,
I was able to get everything ready without any real stumbling blocks.

## Create an EC2 instance

Once you've setup your IAM (Identity and Access Management) profiles, roles, and
policies, you can start enabling CodeDeploy on your EC2 instances. While you can
assign an IAM policy to an existing EC2 instance, I recommend using a new
instance, to ensure that you can troubleshoot and debug without affecting a
running application.

I went and selected an Ubuntu 16.04 AMI (specifically, ami-32b6515f), as I want
to use the latest LTS, and I'm familiar with both Ubuntu and Debian systems.
(This turned out to pose a few issues, which I'll detail later.)

When I created the instance, I tied it to the IAM policy I created for
CodeDeploy, ensuring I'll be able to use it with that service.

## Setting up the EC2 instance

If you don't install the official Amazon Linux AMI, you won't have the various
tools in place needed to run the CodeDeploy agent. Among other things:

- The www-data user is setup such that it cannot use a shell, which means it
  cannot run scripts — which poses a problem for running deployment scripts or
  cronjobs as the user.
- You need to install the CodeDeploy agent on the instance, and it may need
  some dependencies installed depending on the AMI you use.

### www-data

The www-data user exists by default. However, it has the login shell set to
`/usr/sbin/nologin`. This means that if you specify:

```yaml
runas: www-data
```

in one of your `appspec.yml` hooks, it will fail; this also affects execution of
crontab entries. The solution is to update the user to have a real login shell.
Run:

```bash
$ sudo vipw
```

`vipw` is a safer way to edit the `/etc/passwd` file, and will prompt you for an
editor to use before opening it. Find the entry for `www-data`, change the shell
to `/bin/bash`, save, and exit.

### Ruby 2.0

In order to install the code deploy agent on the server, you need to have ruby
2.0 installed; the installer for the agent will not work with any other version
at this time.

If you're on Ubuntu 14.04, or if you're on the official Amazon Linux AMI, it's
already installed, or can be installed from existing package repositories:

```bash
# On Ubuntu 14.04:
$ sudo apt-get install ruby2.0
# On Amazon Linux or Fedora:
$ sudo yum install ruby2.0
```

If, like me, you decide to use Ubuntu 16.04 (xenial), that version is
unavailable (the lowest version available is 2.3), and even some well-known
package repositories do not have xenial packages available (if they ever will).

So, I had to create a package, which involves downloading a 2.0 release, using a
utility to create a debian package out of it, and then installing it.

To do that, I did the following:

```bash
$ sudo apt-get install checkinstall build-essential zlib1g-dev libssl-dev libreadline6-dev libyaml-dev
$ wget http://cache.ruby-lang.org/pub/ruby/2.0/ruby-2.0.0-p481.tar.gz
$ tar xzf ruby-2.0.0-p481.tar.gz
$ cd ruby-2.0.0-p481
$ sudo checkinstall .
```

When `checkinstall` runs, it will prompt you for a few things:

```text
The package documentation directory ./doc-pak does not exist.
Should I create a default set of package docs? [y]:
```

Answer "y".

It then asks for a description; I used "Ruby 2.0 interpreter".

At this point, it shows you what values the package will be built with. Time to
change a few.

- Change the Name (option 2) to "ruby2.0"
- Change the Version (option 3) to "2.0.0-p481"
- Change the Requires list (option 10) to read: build-essential,zlib1g-dev,libssl-dev,libreadline6-dev,libyaml-dev
- Change the Provides list (option 11) to read: ruby-interpreter,ruby-interpreter:any,ruby-interpreter:i386,ruby2.0:any

Once done, hit ENTER.

This builds the package in the current directory as
`ruby2.0_2.0.0-p481-1_amd64.deb`, which you can then install with `dpkg -i`,
and later remove with `dpkg -r ruby2.0`.

> The package metadata values are important. Without them, the agent may or may
> not be able to identify that a usable ruby version is installed on the system.

### CodeDeploy Agent

To install the code deploy agent, you need to know what region you're in. From
there, you do the following:

```bash
$ mkdir code-deploy-agent
$ cd code-deploy-agent
$ wget https://aws-codedeploy-{region name}.s3.amazonaws.com/latest/install
$ chmod +x ./install
$ sudo ./install auto
```

I have my EC2 instance launched in us-east-1, so the above url became 
`https://aws-codedeploy-us-east-1.s3.amazonaws.com/latest/install`.
(See the [documentation on installing the agent](http://docs.aws.amazon.com/codedeploy/latest/userguide/how-to-run-agent-install.html)
for valid S3 bucket values for the installer, as not all regions are
represented.)

If you have installed the dependencies as listed above, all should go well. From
there, check to see if it's running:

```bash
$ sudo service codedeploy-agent status
```

If it's not running, start it:

```bash
$ sudo service codedeploy-agent start
```

## Creating your deployment

I found that, in the end, PHP application deployment was quite easy with
CodeDeploy, but that due to oddities in the `appspec.yml` rules, as well as in
where and how event hooks scripts are executed, the documentation often failed
me. As such, this is probably the most important section of this narrative.

A basic `appspec.yml` has the following structure:

```yaml
version: 0.0
os: linux
files:
  - source: /
    destination: /var/www/example.com
permissions:
  - object: /var/www/example.com
    pattern: "**"
    owner: www-data
    group: www-data
    type:
      - directory
      - file
hooks:
  ApplicationStop:
    - location: .aws/application-stop.sh
      timeout: 30
      runas: root
  BeforeInstall:
    - location: .aws/before-install.sh
      timeout: 300
      runas: root
  AfterInstall:
    - location: .aws/after-install-www-data.sh
      timeout: 300
      runas: www-data
    - location: .aws/after-install-root.sh
      timeout: 30
      runas: root
  ApplicationStart:
    - location: .aws/application-start.sh
      timeout: 30
      runas: root
```

Let's talk about each of the sections.

### Files

`files` allows you to specify which files from your archive should be
installed, and their destination on the filesystem. Each entry requires a
`source`, which will be a path relative to the archive, and a `destination`,
which will be its destination on the server.

If you specify `/` (or `\\` for Windows instances), CodeDeploy will copy the
entire archive. I've found this is typically easiest, as the `appspec.yml`
specification *does not provide wildcard functionality*, nor any
whitelist/blacklist functionality. Yes, you can specify directories or files,
but once you go that route, if you have more than a handful, the specification
gets unwieldy.

> One tip I read early on was to ship the deployable code within a
> subdirectory of the archive. This is similar to how Zend Server's ZPK format
> expects things as well, but it's pretty much counter to every PHP framework
> skeleton or application I've used or seen.

One thing to know: this does not work like Unix `cp` or `mv`. With those
utilities, if the source is a file and you specify a destination path that does
not exist, they will create it as a file. However, CodeDeploy does not. As an
example, consider the following:

```yaml
files:
  - source: .aws/crontab
    destination: /var/spool/cron/crontabs/www-data
```

Since `/var/spool/cron/crontabs` is a directory, if I were using `cp` or `mv`,
I'd expect this operation to create the file
`/var/spool/cron/crontabs/www-data`. Instead, because the source name and
destination name do not match, CodeDeploy creates that as a *directory*, and
then copies the file `crontab` beneath it, giving us the file
`/var/spool/cron/crontabs/www-data/crontab`. Which is utterly unusable. (There
are ways around it via hook scripts, which I'll detail later.)

Another thing to keep in mind: when providing a source *directory*, CodeDeploy
copies all files under it, recursively, to the destination. If the destination
does not include the source directory name, you'll be in for a surprise:

```yaml
files:
  - source: bin
    destination: /var/www/example.com
```

will copy all the files under `bin/` to the directory `/var/www/example.com`. It
*will not* create a `bin/` directory under that path! As such, you likely want
to use:

```yaml
files:
  - source: bin
    destination: /var/www/example.com/bin
```

For these reasons, I found it was far easier to just copy the entire archive.

### Permissions

The `permissions` section allows you to specify permissions for individual files
or trees on the server. These are applied during the Install event, after all
files have been deployed to their location.

The format is:

```yaml
permissions:
  - object: /var/www/example.com
    pattern: "**"
    owner: www-data
    group: www-data
    mode: 4755
    type:
      - directory
      - file
```

You can specify individual files or directories for the `object`. directories
require a `pattern` following them, which allows you to provide a POSIX glob for
specifying files and directories to which to apply the permissions; `**`
indicates it should match everything under the tree.

Additionally, the `type` parameter allows you to specify whether the permissions
apply to specifically directories or files; you can specify both at the same
time if desired.

The owner, group, and mode arguments are just as you would use for either
`chown`, `chgrp`, or `chmod`.

The above will likely work for most cases. I broke that into two separate
statements, one for applying to directories, another for files:

```yaml
permissions:
  - object: /var/www/example.com
    pattern: "**"
    owner: www-data
    group: www-data
    mode: 4750
    type:
      - directory
  - object: /var/www/example.com
    pattern: "**"
    owner: www-data
    group: www-data
    mode: 640
    type:
      - file
```

There's a fair amount more you can do; read the
[appspec.yml permissions documentation](http://docs.aws.amazon.com/codedeploy/latest/userguide/app-spec-ref-permissions.html)
for more details.

### Hooks

Hooks allow you to specify scripts to run during each of the CodeDeploy events.
There are five events you can listen to:

- ApplicationStop, which occurs at the start of a deployment operation.
- BeforeInstall, which occurs before any `files` specified in the `appspec.yml`
  are deployed to their destinations.
- AfterInstall, which occurs after files have been deployed.
- ApplicationStart, which happens after installation is complete
- ValidateService, which happens after the application has been started.

There are actually a few other events, but only the above can trigger hook
scripts.

As noted in the sample `appspec.yml`, the various hook sections have the
following format:

```yaml
hooks:
  <event name>:
    - location: <path to script>
      timeout: <timeout in seconds>
      runas: <user to run script as>
```

The only required element is the `location` field, which is the script to
execute. The `timeout` can be used to help ensure that scripts that take too long
to execute fail the deployment, allowing you to return to the previous
deployment. The `runas` is used to specify a user to execute the script under,
and defualts to root; I like to specify it explicitly, and some scripts may need
to run under different users (in particular, the www-data user).

Now come the various caveats and recommendations.

First things first: I put all the various files related to deployment on AWS in
a dedicated directory, `.aws/`. This allows me to have it all in one place, and
segregate it from the rest of my application.

Second: I strongly recommend creating a script named after the event
in which it executes; e.g., `after-install.sh`. This makes identifing which
script to edit and debug far simpler. If the script needs to be run as a
specific user, I include that in the script name as well:
`after-install-www-data.sh`.

Third, the "deployment directory" is not the same as the "installation
directory". The *deployment directory* is where CodeDeploy downloads your code
(whether from GitHub or S3). During the Install event, it *copies* code from
that directory into the final destination (per your `appspec.yml` "Files"
rules). However, Install *will only copy directories that were part of the
original archive*. That means any files you generate *will not be part of the
installation directory*.

Fourth, hook script `Location` values *are always relative to the deployment
directory*. Not the installation directory. In fact, even *fully qualified
paths* are interpreted as if they were relative to the deployment directory,
which means system tools cannot be called directly! As such, you'll need to make
those calls to system tools *within a hook script*.

#### Example

```yaml
hooks:
  ApplicationStop:
    - location: .aws/application-stop.sh
      timeout: 30
      runas: root
  BeforeInstall:
    - location: .aws/before-install.sh
      timeout: 30
      runas: root
  AfterInstall:
    - location: .aws/after-install-www-data.sh
      timeout: 300
      runas: www-data
    - location: .aws/after-install-root.sh
      timeout: 30
      runas: root
  ApplicationStart:
    - location: .aws/application-start.sh
      timeout: 30
      runas: root
```

#### System dependencies

One cool thing about CodeDeploy is that, other than the requirements to allow
the CodeDeploy agent to run and ensuring www-data has a login shell, you can
assume that deployment will take care of the everything else for you, much as
you would when using Ansible, Puppet, Chef, or Docker.

The idea is this: during the BeforeInstall event, you will check for and install
system dependencies, create directories and configuration, etc.

One nice aspect about this approach is that if your system requirements change —
for example, if you decide to switch between grunt and gulp for preparing your
frontend assets — you can alter your hook script to add the new requirement,
and it will be installed on the next deployment.

I wrote one script to handle all of this for my site:

```bash
#!/bin/bash
#######################################################################
# System dependencies
#######################################################################

# Install needed dependencies
apt-get update
apt-get install -y nginx php7.0 php7.0-bcmath php7.0-bz2 php7.0-cli php7.0-ctype php7.0-curl php7.0-dom php7.0-fileinfo php7.0-fpm php7.0-gd php7.0-iconv php7.0-intl php7.0-json php7.0-mbstring php7.0-pdo php7.0-pdo-sqlite php7.0-phar php7.0-readline php7.0-simplexml php7.0-sockets php7.0-sqlite3 php7.0-tidy php7.0-tokenizer php7.0-xml php7.0-xsl php7.0-xmlreader php7.0-xmlwriter php7.0-zip npm python3-pip

# aws cli
pip3 install awscli

# Get Composer, and install to /usr/local/bin
if [ ! -f "/usr/local/bin/composer" ];then
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
else
    /usr/local/bin/composer self-update --stable --no-ansi --no-interaction
fi

# Create a COMPOSER_HOME directory for the application
if [ ! -d "/var/cache/composer" ];then
    mkdir -p /var/cache/composer
    chown www-data.www-data /var/cache/composer
fi

# Get private configuration
if [ ! -d "/var/www/config" ];then
    mkdir -p /var/www/config
fi
(cd /var/www/config && aws s3 sync s3://config.example.com .)

# Make a log directory for php-fpm
if [ ! -d "/var/log/php" ];then
    mkdir -p /var/log/php
fi
chown -R www-data.www-data /var/log/php
chmod -R ug+rwX /var/log/php

# Install grunt globally
npm install -g grunt-cli

# Ensure we can run npm as www-data
if [ ! -d "/var/www/.npm" ];then
    mkdir -p /var/www/.npm
    chown www-data.www-data /var/www/.npm
    chmod o-X /var/www/.npm
    chmod ug+rwX /var/www/.npm
fi
```

As you can see, I do some *conditional* installation as well; if certain files
or directories exist, I can skip over them or treat them differently. In the
case of running `apt-get`, `pip3`, or `npm`, I know that these applications will
check to see if the latest version is installed before attempting to do
anything, making these very fast operations most of the time.

> Some notes on a few items in there:
>
> - I created a `COMPOSER_HOME` directory as composer requires a place to cache
>   the results of pulling information from Packagist, as well as packages it
>   has downloaded. Since the www-data user doesn't have rights to create files
>   or directories under `/var/www`, we need to create a directory for it to
>   use.
> - Similarly, npm caches to `$HOME/.npm`. If there's a way to specify an
>   alternate directory, I've not found it yet. As such, I create the directory
>   here, if it doesn't exist, and make sure the www-data user has ownership of
>   it.
> - I want to be able to log my PHP errors, so I create a log directory for PHP,
>   and, again, make sure www-data can write to it.

Essentially, BeforeInstall is when I can make sure the system is ready to run my
application once installation completes.

#### Private configuration

One other thing to note in that script is the usage of the AWS CLI to pull some
files from S3:

```bash
# Get private configuration
if [ ! -d "/var/www/config" ];then
    mkdir -p /var/www/config
fi
(cd /var/www/config && aws s3 sync s3://config.example.com .)
```

What I've done here is stored production configuration settings and SSL
certificates in a private bucket on S3. Because the AWS CLI needs appropriate
credentials to access the bucket (and if you're on an EC2 instance, it inherits
credentials based on the instance policy), this is a safe operation, ensuring I
have that data stored securely, and not in my git repository. I currently store
my application production configuration there, as well as my SSL certificates.
The lines above pull them from the bucket when I'm preparing to deploy, ensuring
I have the latest production-ready versions.

#### Application preparation

With a PHP application, we likely want to wait to do anything until after our
files have been moved to their installation directory. Why?

There are two reasons: location, and install quirks.

The first is that hook scripts appear to run with a working directory of
`/opt/codedeploy-agent`, and not the deployment directory. When I tested,
`composer install` and `npm install` both failed, due to being unable to locate
their respective configuration... because they were running under
`/opt/codedeploy-agent`.

You can, of course, figure out the current working directory from within bash
with a few hurdles, and I tried that to make things work. However, that unveiled
another issue: *CodeDeploy will only move directories that were originally part
of the archive*. So, if you run `composer install` within a `BeforeInstall`
script, the `vendor/` directory does not get moved to the installation
directory.

As such, for most PHP projects, you'll need to use an AfterInstall script to
do your work. Moreover, you'll need to have the script change the working
directory to the installation directory.

So, as an example:

```bash
#!/bin/bash
#######################################################################
# Application preparation
#######################################################################

(
    cd /var/www/example.com ;

    # Copy in the production local configuration
    cp /var/www/config/php/*.* config/autoload/ ;

    # Execute a composer installation
    COMPOSER_HOME=/var/cache/composer composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader ;

    # Execute other scripts as needed ...

    # Compile CSS and JS
    npm install ;
    grunt ;
    rm -Rf node_modules ;
)
```

In the above:

- I copy my production configuration that I synced from S3 into my application.
- I run Composer to install dependencies. Notice that I specify the composer
  cache directory I setup in by BeforeInstall script as the `COMPOSER_HOME`!
- If I have other deployment/build tasks, I can run those.
- In my case, I'm using grunt to aggregate and minimize CSS and JS assets, so I
  run that, and then clean-up after myself.

The big thing to note is this construct:

```bash
(
    cd /var/www/example.com ;

    # tasks..
)
```

Since this runs AfterInstall, I know the destination directory is ready, and I
run my deployment operations there. The script itself, however, *is still being
run from the CodeDeploy agent deployment directory*, which is why I need to
change directories within my script.

#### System configuration

Now that the application has been prepared, we can update the system.

Some aspects of web applications that might change from one deployment to the
next:

- Crontabs
- SSL configuration
- Web server configuration
- PHP configuration

You likely won't want to update these, however, unless everything else during
deployment has succeeded, so we do this *last*.

Here's my system configuration script, `after-install-root.sh`:

```bash
#!/bin/bash
#######################################################################
# System preparation following successful application installation.
#######################################################################

SCRIPT_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
CRONTAB_PATH=/var/spool/cron/crontabs/www-data

# Setup www-data crontab
cp ${SCRIPT_PATH}/crontab ${CRONTAB_PATH} && chown www-data.crontab ${CRONTAB_PATH} && chmod 600 ${CRONTAB_PATH}

# Bring in the SSL configuration and prep it
mv /var/www/config/ssl/*.* /etc/ssl/
(cd /etc/ssl && cat example.com.crt example.com.ca-bundle > example.com.chained.crt)

# Copy nginx configuration
cp ${SCRIPT_PATH}/mwop.net.conf /etc/nginx/sites-available/
if [ ! -e "/etc/nginx/sites-enabled/example.com.conf" ];then
    (cd /etc/nginx/sites-enabled && ln -s ../sites-available/example.com.conf .)
fi

# Copy php configuration for php-fpm process
cp ${SCRIPT_PATH}/php.ini /etc/php/7.0/fpm/conf.d/example.com.ini
cp ${SCRIPT_PATH}/php-fpm.conf /etc/php/7.0/fpm/pool.d/www.conf
```

Again, this script runs in the context of the deployment directory, not the
installation destination directory. Further, we need to copy files from it to
various locations on the server, as well as from the files we downloaded via S3.

Crontabs have to be owned by the user, and the crontab group, and named after
the user; I'd have loved to have been able to do this via the `appspec.yml`
`files` configuration, but never found a combination that worked; as such, I do
it here in the hook script.

> The above example assumes you've put some configuration files in your `.aws/`
> directory:
> 
> - `php.ini`, with my production PHP settings.
> - `php-fpm.conf`, with my production PHP-FPM configuration.
> - `example.com.conf`, with my production nginx settings.
>
> I don't detail what these files contain, as that will vary quite a bit between
> applications.

The idea with this AfterInstall script is to ensure that we have appropriate
server configuration to execute the current state of our application.

#### Application start and stop

Remember when I mentioned earlier that all hook script `location` entries are
relative to the deployment directory? This is where that information comes in.

When we start deployment, we need to stop any services we may be updating. For a
PHP application, this is likely:

- The web server.
- If you're using php-fpm, then php-fpm.

I originally tried this:

```yaml
hooks:
  ApplicationStop:
    - location: service nginx stop
      timeout: 30
      runas: root
    - location: service php7.0-fpm stop
      timeout: 30
      runas: root
```

However, CodeDeploy was trying to resolve those as something along the lines of
`/opt/codedeploy-agent/deployment/{some-uuid}/{deployment-id}/archive/service`.

So, the trick is to do those calls within a hook script you have in your
repository. For example:

```bash
#!/bin/bash
service nginx stop
service php7.0-fpm stop
```

Similarly, we want to bring the services back up during ApplicationStart:

```bash
#!/bin/bash
service php7.0-fpm start
service nginx start
service cron restart
```

(I also restart cron after installing the new crontab for www-data.)

## Deployment

The first time you deploy, you'll need to do it manually. Assuming you have
installed and properly configured the AWS CLI on your own machine, and have
setup CodeDeploy, you can do the following:

```bash
$ aws deploy create-deployment \
> --application-name {application-name} \
> --deployment-group-name {deployment-group-name} \
> --deployment-config-name CodeDeployDefault.OneAtATime \
> --ignore-application-stop-failures \
> --github-location repository={user or org}/{repo},commitId={sha1}
```

Fill in all bracketed items with appropriate values.

> One thing to note from the original `create-deployment` command: the
> `--ignore--application-stop-failures` flag. This flag is necessary to ensure
> that deployment can continue if your ApplicationStop script fails. Why would
> you want this? Well, recall that we use BeforeInstall to setup our system
> dependencies. On our first execution, or on any execution where we add new
> services to start and stop, *you may have services that do not yet exist*.
> The point of the deployment is to install those! As such, use that flag!

This will give you a JSON payload like the following:

```json
{
    "deploymentId": "d-XXXXXXXXXX"
}
```

You can then check the status using:

```bash
$ aws deploy get-deployment --deployment-id {deploymentId} --query "deploymentInfo.[status,creator]"
```

You can check that periodically, or pass it to the `watch` command to determine
the status. If all goes well, you'll see a `"status": "Succeeded"` message.

### Troubleshooting

If and/or when it fails, you have a couple places you can look.

If you go to the [CodeDeploy console on AWS](https://console.aws.amazon.com/codedeploy/home),
you can drill down into your application and see the deployments. When a
deployment fails, you'll see a link to the deployment ID, which will take you an
overview showing the instances to which it attempted to deploy. Each instance
has a "View Events" link, which brings you to an overview of the events, and any
failed events will have a link to logs.

You can also SSH to your server, and go to
`/opt/codedeploy-agent/deployment-root/{some uuid}/`. Do an `ls -ltr | tail -n1` to find
the latest deployment ID, and then descend into it. In that directory, you can
then do a `less logs/scripts.log`, and usually discover what the error is. (This
was how I discovered the issues with where and how the hook scripts are
executed, as well as the issues with Composer and npm that I ended up working
around.)

## Automation

AWS has an official AWS CodeDeploy webhook for GitHub that can be used along
with the GitHub Auto-Deployment webhook. Once you have confirmed that you can
create successful deployments, you can wire these up.

The AWS blog has [an excellent guide to setting up autodeployment](https://blogs.aws.amazon.com/application-management/post/Tx33XKAKURCCW83/Automatically-Deploy-from-GitHub-Using-AWS-CodeDeploy);
I have nothing I can add to that. I followed the instructions once I had a
working deployment, and it all *just worked*.

## Summary

AWS CodeDeploy is quite powerful, and, once you understand its quirks, is a
solid approach to deployment; it essentially allows you to create a custom PaaS
for your application with "push to deploy", and ensures that each deployment is
setup based on the current production requirements.

While this post detailed using a single EC2 node, you can also setup multiple
instances under the same policy; when CodeDeploy triggers a deployment, it will
only succeed once all nodes have successfully deployed. As such, it even
provides a path to horizontal scaling!

I'm really happy with the results, despite the amount of trial-and-error it took
to get things working. Hopefully this post will help reduce the amount of time
others need to make this powerful tool work for them!
