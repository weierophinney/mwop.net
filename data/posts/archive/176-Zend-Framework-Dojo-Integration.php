<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('176-Zend-Framework-Dojo-Integration');
$entry->setTitle('Zend Framework Dojo Integration');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1211381820);
$entry->setUpdated(1211733637);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'mvc',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I'm pleased to announce that 
    <a href="http://framework.zend.com/">Zend Framework</a> will be partnering
    with <a href="http://dojotoolkit.org/">Dojo Toolkit</a> to deliver
    out-of-the-box Ajax and rich user interfaces for sites developed in Zend
    Framework.
</p>

<p>
    First off, for those ZF users who are using other Javascript toolkits: Zend
    Framework will continue to be basically JS toolkit agnostic. You will still
    be able to use whatever toolkit you want with ZF applications. ZF will
    simply be shipping Dojo so that users have a toolkit by default. Several
    points of integration have been defined, and my hope is that these can be
    used as a blueprint for community contributions relating to other javascript
    frameworks. In the meantime, developers choosing to use Dojo will have a
    rich set of components and integration points to work with.
</p>

<p>
    The integration points we have defined for our initial release are as
    follows:
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<ul>
    <li><p>
        <b>JSON-RPC Server:</b> We are re-working the Zend_Json_Server that has
        been in our incubator since, oh, what? 0.2.0? and never released to
        actually follow a specification: 
        <a href="http://groups.google.com/group/json-rpc">JSON-RPC</a>. This
        will allow it to work seamlessly with Dojo, as well as other toolkits
        that have JSON-RPC client implementations. I have actually completed
        work on this, though the proposal is waiting to be approved; if you want
        to check it out, you can find it in the 
        <a href="http://framework.zend.com/svn/framework/branch/user/matthew/zed_json_server">ZF svn</a>.
        </p><p>
        The original Zend_Json_Server implementation will be abandoned. It was
        never fully tested nor fully documented, which has prevented its
        release. Additionally, since it implemented its own ad-hoc standard, it
        did not provide the type of interoperability that a true JSON-RPC server
        implementation will provide. I am excited that we will finally be able
        to provide a standards-compliant solution for general availability.
        </p><p>
        One final note: there are currently two different JSON-RPC
        specifications, 1.0 and 2.0. Currently, the implementation I've been
        working on will switch payload formats based on the request, and can
        deliver different SMD formats appropriately as well.
    </p></li>

    <li>
        <b>dojo() View Helper:</b> Enabling Dojo for a page is not typically as
        trivial as just loading the <code>dojo.js</code> script -- you have a
        choice of loading it from the AOL CDN or a local path, and also may want
        or need to load additional dojo, dijit, or dojox modules, specify custom
        modules and paths, specify code to run at <code>onLoad()</code>, and
        specify stylesheets for decorating dijits. On top of this, this
        information may change from page to page, and may only be needed for
        a subset of pages. The <code>dojo()</code> view helper will act as a 
        <a href="http://framework.zend.com/manual/en/zend.view.helpers.html#zend.view.helpers.initial.placeholder">placeholder</a>
        implementation, and facilitate all of the above tasks, as well as take
        care of rendering the necessary <code>style</code> and
        <code>script</code> elements in your page.
    </li>

    <li>
        <b>Form Element implementations:</b> One area that developers really
        leverage javascript and ajax toolkits is forms. In particular, many
        types of form input can benefit from advanced and rich user interfaces
        that only javascript can provide: calendar choosers, time selectors,
        etc. Additionally, many like to use client-side validation in order to
        provide instantaneous validation feedback to users (instead of requiring
        a round-trip to the server). We will be identifying a small group of
        form elements that we feel solve the most relevant use cases, and write
        Dojo-specific versions that can be utilized with <code>Zend_Form</code>.
        (One thing to note: <code>Zend_Form</code>'s design already works very
        well with Dojo, allowing many widgets and client-side validations to be
        created by simply setting the appropriate element attributes.)
    </li>

    <li>
        <b>dojo.data Compatibility:</b> <code>dojo.data</code> defines a
        standard storage interface; services providing data in this format can
        then be consumed by a variety of Dojo facilities to provide highly
        flexible and dynamic content for your user interfaces. We will be
        building a component that will create dojo.data compatible payloads with
        which to respond to XmlHttpRequests; you will simply need to pass in the
        data, and provide metadata regarding it.
    </li>
</ul>

<p>
    So, some examples are in order. First off, <code>Zend_Json_Server</code>
    operates like all of ZF's server components: if follows the 
    <a href="http://php.net/soap_soapserver_construct">SoapServer API</a>. This
    allows you to attach arbitrary classes and functions to the server
    component. Additionally, it can build a Service Mapping Description (SMD)
    that Dojo can consume in order to discover valid methods and signatures. As
    an example, on the server side you could have the following:
</p>

<div class="example"><pre><code class="language-php">
// /json-rpc.php
// Assumes you have a class 'Foo' with methods 'bar' and 'baz':
$server = new Zend_Json_Server();
$server-&gt;setClass('Foo')
       -&gt;setTarget('/json-rpc.php')
       -&gt;setEnvelope('JSON-RPC-1.0')
       -&gt;setDojoCompatible(true);

// For GET requests, simply return the service map
if ('GET' == $_SERVER['REQUEST_METHOD']) {
    $smd = $server-&gt;getServiceMap();
    header('Content-Type: application/json');
    echo $smd;
    exit;
}

$server-&gt;handle();
</code></pre></div>

<p>
    On your view script side, you might then do the following:
</p>

<div class="example"><pre><code class="language-php">
&lt;h2&gt;Dojo JSON-RPC Demo&lt;/h2&gt;
&lt;input name=\&quot;foo\&quot; type=\&quot;button\&quot; value=\&quot;Demo\&quot; onClick=\&quot;demoRpc()\&quot;/&gt;
&lt;? 
$this-&gt;dojo()-&gt;setLocalPath('/js/dojo/dojo.js')
             -&gt;addStyleSheetModule('dijit.themes.tundra')
             -&gt;requireModule('dojo.rpc.JsonService');
$this-&gt;headScript()-&gt;captureStart(); ?&gt;
function demoRpc()
{
    var myObject = new dojo.rpc.JsonService('/json-rpc.php');
    console.log(myObject.bar());
}
&lt;? $this-&gt;headScript()-&gt;captureEnd() ?&gt;
</code></pre></div>

<p>
    And, finally, in your layout script, you might have the following:
</p>

<div class="example"><pre><code class="language-php">
&lt;?= $this-&gt;doctype() ?&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;title&gt;...&lt;/title&gt;
        &lt;?= $this-&gt;dojo() ?&gt;
        &lt;?= $this-&gt;headScript() ?&gt;
    &lt;/head&gt;
    &lt;body class=\&quot;tundra\&quot;&gt;
        &lt;?= $this-&gt;layout()-&gt;content ?&gt;
    &lt;/body&gt;
&lt;/html&gt;
</code></pre></div>

<p>
    The example doesn't do much -- it simply logs the results of the JSON-RPC
    call to the console -- but it demonstrates a number of things:
</p>

<ul>
    <li>
        <b>dojo() View Helper:</b> The example shows using dojo from a local
        path relative to the server's document root; using the 'Tundra'
        stylesheet shipped with Dojo and attaching it to the layout; capturing a
        required module ('dojo.rpc.JsonService'); and rendering the necessary
        Dojo stylsheet and script includes in the layout.
    </li>
    <li>
        <b>JSON-RPC client:</b> Dojo requires that you point the JsonService to
        an endpoint that delivers a Service Mapping Description; in this
        example, I use any GET request to return the SMD. Once the SMD is
        retrieved, any methods exposed are available to the Javascript object as
        if they were internal methods -- hence <code>myObject.bar()</code>.
        Dojo's current implementation performs all other requests as POST
        requests, passing the data via the raw POST body.
    </li>
</ul>

<p>
    There will be more to come in the future, and I will be blogging about
    developments as I get more proposals up and code into the repository. All in
    all, this is a very exciting collaboration, and should help provide ZF
    developers the ability to rapidly create web applications with rich, dynamic
    user interfaces.
</p>

<p>
    <b>Update:</b> <a href="http://andigutmans.blogspot.com/2008/05/dojo-and-zend-framework-partnership.html">Andi has posted an FAQ on our integration</a>.
EOT;
$entry->setExtended($extended);

return $entry;
