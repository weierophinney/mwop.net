<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('227-Exposing-Service-APIs-via-Zend-Framework');
$entry->setTitle('Exposing Service APIs via Zend Framework');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1256341320);
$entry->setUpdated(1256726636);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
));

$body =<<<'EOT'
<p>
    The hubbub surrounding "Web 2.0" is around sharing data. In the early
    iterations, the focus was on "mashups" -- consuming existing public APIs in
    order to mix and match data in unique ways. Now, more often than not, I'm
    hearing more about <em>exposing</em> services for others to consume. Zend
    Framework makes this latter trivially easy via its various server classes.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    All Zend Framework server classes follow PHP's <a
        href="http://php.net/soapserver">SoapServer</a> API. In a nutshell, you
    can basically do the following with any server class:
</p>

<div class="example"><pre><code class="language-php">
$server = new Zend_XmlRpc_Server();
$server-&gt;setClass('My_Awesome_Api');
echo $server-&gt;handle();
</code></pre></div>

<p>
    Each server protocol we support in this way -- 
    <a href="http://framework.zend.com/manual/en/zend.soap.html">SOAP</a>,
    <a href="http://framework.zend.com/manual/en/zend.xmlrpc.server.html">XML-RPC</a>,
    <a href="http://framework.zend.com/manual/en/zend.json.server.html">JSON-RPC</a>,
    and <a href="http://framework.zend.com/manual/en/zend.amf.server.html">AMF</a>
    -- has its own little nuances, but the basics follow the above pattern.
</p>

<p>
    Where should you do this, however? Many developers want to stick this in
    their MVC application directly, in order to have pretty URLs. However, the
    framework team typically recommends against this. When serving APIs, you
    want responses to return as quickly as possible, and as the servers
    basically encapsulate the Front Controller and MVC patterns in their design,
    there's no good reason to duplicate processes and add processing overhead.
</p>

<p>
    Additionally, there's often a need to version your APIs. As you add new
    features or need to change method signatures, you'll need to introduce a new
    version of the API for developers to consume. 
</p>

<p>
    One recommendation to solve each problem is to move your server endpoints
    into your public directory structure, and then utilize your web server's
    URL rewriting capabilities. As an example, you could organize your endpoints
    as follows:
</p>

<pre>
public
|-- api
|   |-- v1
|   |   |-- xmlrpc.php
|   |   |-- soap.php
|   |   |-- jsonrpc.php
</pre>

<p>
    You might then configure your URL rewriting as follows:
</p>

<div class="example"><pre><code class="language-conf">
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
RewriteRule ^api/v1/xmlrpc api/v1/xmlrpc.php [L]
RewriteRule ^api/v1/soap api/v1/soap.php [L]
RewriteRule ^api/v1/jsonrpc api/v1/jsonrpc.php [L]
RewriteRule ^.*$ index.php [NC,L]
</code></pre></div>

<p>
    This allows you to move the service scripts to other locations if necessary,
    as well as to have each have explicit dependencies to insulate them from
    changes elsewhere in the codebase.
</p>

<p>
    As a standard best practice, you do not want code duplication. Code
    duplication becomes quite common when taking the above strategy, as each
    endpoint script will often have common logic for bootstrapping the
    application. One way you can avoid this is to leverage <a
        href="http://framework.zend.com/manual/en/zend.application.html">Zend_Application</a>.
    You can do this in one of two ways: (1) instantiate
    <code>Zend_Application</code> using the same configuration as your MVC
    application, and selectively bootstrap necessary resources; or (2) extend
    your MVC application's bootstrap class, and override the <code>run()</code>
    method.
</p>

<p>
    In the first case, you might do the following in your server endpoint
    scripts:
</p>

<div class="example"><pre><code class="language-php">
// Initialize application
require_once 'Zend/Application.php';
$app = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH .  '/configs/application.ini'
);

// Selectively bootstrap resources:
$app-&gt;bootstrap('db');

// Instantiate server, etc.
$server = new Zend_XmlRpc_Server();
</code></pre></div>

<p>
    In the second case, you would subclass your application bootstrap class, and
    override the <code>run()</code> method. Such an extending class could look
    like the following:
</p>

<div class="example"><pre><code class="language-php">
class XmlRpc_Bootstrap extends Bootstrap
{
    public function run()
    {
        $server = new Zend_XmlRpc_Server();
        $server-&gt;setClass('My_Awesome_Api');
        echo $server-&gt;handle();
    }
}
</code></pre></div>

<p>
    You would also need to modify your application bootstrapping slightly to
    notify it of your new bootstrap class:
</p>

<div class="example"><pre><code class="language-php">
$app = new Zend_Application(
    APPLICATION_ENV,
    array(
        'bootstrap' =&gt; array(
            'class' =&gt; 'XmlRpc_Bootstrap',
            'path'  =&gt; 'path/to/Bootstrap.php',
        ),
        'config' =&gt; APPLICATION_PATH . '/configs/application.ini',
    ),
);
$app-&gt;bootstrap()
    -&gt;run();
</code></pre></div>

<p>
    So, the takeaway is: Zend Framework makes exposing web services easy, and
    the addition of <code>Zend_Application</code> makes it trivially easy to
    re-use application configuration in order to expose your servers via
    discrete, unique endpoints in your application. What are <em>you</em>
    waiting for?
</p>
EOT;
$entry->setExtended($extended);

return $entry;
