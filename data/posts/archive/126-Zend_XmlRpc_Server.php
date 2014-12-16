<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('126-Zend_XmlRpc_Server');
$entry->setTitle('Zend_XmlRpc_Server');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1160965380);
$entry->setUpdated(1160965380);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    As <a href="http://weierophinney.net/matthew/archives/125-PHP-5s-Reflection-API.html">noted previously by myself</a> and 
    <a href="http://pixelated-dreams.com/archives/251-More-Web-Services.html">Davey</a>,
    I've been working on Zend_XmlRpc_Server for some months now. In the past
    couple weeks, I've refactored it to push the class/function reflection into
    Zend_Server_Reflection, and, in doing so, noted that there were further
    areas for refactoring into additional helper classes. Currently, it now has
    classes for the Request, Response, and Faults, and all actual XML wrangling
    is done in those, making the server basically XML-agnostic.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    One side benefit of this refactoring was that it allowed me to write tests
    much more quickly and easily. I no longer needed to worry about adding
    helper methods to the server in order to determine if it properly parsed the
    request to get the method call and arguments; I could simply test the public
    API for actually handling the requests. And I no longer needed to create XML
    in order to test the server; I could simply populate a request object in
    order to pass in the request, and check the response object to see if I
    received an appropriate value. No XML wrangling!
</p>
<p>
    So, for an example, advanced case usage with all the bells and whistles:
</p>
<div class="example"><pre><code class="language-php">
&lt;?php
require_once 'Zend/XmlRpc/Server.php';
require_once 'Zend/XmlRpc/Server/Fault.php';
require_once 'Zend/XmlRpc/Server/Cache.php';
require_once 'Services/Request.php';
require_once 'Services/Response.php';
require_once 'Services/Exception.php';

require_once 'Services/Comb.php';
require_once 'Services/Brush.php';
require_once 'Services/Pick.php';

// Specify a cache file
$cacheFile = dirname(__FILE__) . '/xmlrpc.cache';

// Allow Services_Exceptions to report as fault responses
Zend_XmlRpc_Server_Fault::attachFaultException('Services_Exception');

$server = new Zend_XmlRpc_Server();

// Attempt to retrieve server definition from cache
if (!Zend_XmlRpc_Server_Cache::get($cacheFile, $server)) {
    $server-&gt;setClass('Services_Comb', 'comb');   // methods called as comb.*
    $server-&gt;setClass('Services_Brush', 'brush'); // methods called as brush.*
    $server-&gt;setClass('Services_Pick', 'pick');   // methods called as pick.*

    // Save cache
    Zend_XmlRpc_Server_Cache::save($cacheFile, $server));
}

// Create a request object
$request = new Services_Request();

// Utilize a custom response
$server-&gt;setResponseClass('Services_Response');

echo $server-&gt;handle($request);
</code></pre></div>
<p>
    As an afterthought, something that hit me as I finished writing the
    Request, Response, and Fault classes is that, since the server doesn't need
    to do anything with XML, there's really no saying that these classes do,
    either. This means it could, theoretically, be used as a scaffold for other
    types of RPC web services -- for instance, using compression or ssl-encoded
    transactions, YAML, JSON, etc.  That will be a subject for another day.
</p>
<p>
    If you're interested in testing the XML-RPC server, which is mostly complete
    at this stage (@todo items at this stage only include verifying arguments
    received in a request match one of the signatures and that reflection
    translates the signature parameter and return types to XML-RPC types), you
    can grab it from the 
    <a href="http://framework.zend.com/wiki/display/ZFDEV/Zend+Framework+Subversion+Standards">Zend Framework subversion repository</a>, 
    in the incubator tree.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
