<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('159-Zend_Form-Advanced-Features');
$entry->setTitle('Zend_Form Advanced Features');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1202529360);
$entry->setUpdated(1207593555);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  2 => 'zend framework',
));

$body =<<<'EOT'
<p>
    I've been working on <a href="http://framework.zend.com/wiki/display/ZFPROP/Zend_Form"></a>
    for the past few weeks, and it's nearing release readiness. There are a
    number of features that Cal didn't cover in his 
    <a href="http://devzone.zend.com/article/3030-Lifting-the-Skirt-on-Zend-Framework-1.5---Zend_Form">DevZone coverage</a> 
    (in part because some of them weren't yet complete) that I'd like to
    showcase, including:
</p>

<ul>
    <li>Internationalization</li>
    <li>Element grouping for display and logistical purposes</li>
    <li>Array support</li>
</ul>

<p>
    This post will serve primarily as a high-level overview of some of these
    features; if you're looking for more in-depth coverage, please review the
    unit tests. :-)
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h3>Internationalization</h3>
<p>
    When using form components in many libraries, internationalization (i18n) is
    often tricky to accomplish. There are many potential translation targets:
    labels, submit and reset buttons, and error messages all potentially
    need to be treated.
</p>

<p>
    Zend_Form allows setting a translation object at both the element and form
    level, and also allows setting a default translation object for all forms
    and elements. I personally feel this latter is the most flexible; in most
    projects, you'll have a single set of translation files, so why not simply
    re-use the same object throughout?
</p>

<div class="example"><pre><code class="language-php">
// Create your translation object
$translate = new Zend_Translate(...);

// Set it as the default object for all forms and elements:
Zend_Form::setDefaultTranslator($translate);
</code></pre></div>

<p>
    What do you get for this?
</p>

<ul>
    <li>Legends. If a fieldset legend has a translation available, it will be
    translated.</li>

    <li>Labels. If the label you provide has a translation available,
    it will be used.</li>

    <li>Buttons. Submit, reset, and regular form button values will
    be translated.</li>

    <li>Error messages. Validation error messages will be translated,
    <em>with</em> any value substitutions as provided by Zend_Validate.</li>
</ul>

<p>
    In other words, translation in Zend_Form is pretty well integrated.
</p>

<h3>Element Grouping</h3>

<p>
    In Zend_Form, we distinguish between two types of element grouping: grouping
    for display purposes (DisplayGroups) and grouping for logistical purposes
    (Sub Forms)
</p>

<p>
    With DisplayGroups, you're basically saying you want to visually or
    semantically group elements together on the page. Usually (and by default)
    this is done with fieldsets. DisplayGroups provide a simple mechanism for
    doing this. The elements remain children of the parent form object, but are
    rendered within the display group.
</p>

<p>
    Other times, you want to group the elements logically. For instance, you
    might want to group a billing address separately from a shipping address.
    This grouping may be simple namespacing under array keys (I'll cover this
    more later), shared filters or decorators, or, in advanced use cases,
    separate pages of a multi-page form.
</p>

<p>
    Zend_Form's answer to these situations are "Sub Forms". They are actually a
    subclass of Zend_Form, and the only real difference is the class and the
    default decorators used (by default, they render in a fieldset). Since they
    share the same functionality as a regular form, this means they can validate
    their elements, render themselves, etc. However, Zend_Form itself
    <em>cannot</em> iterate over or render a sub forms elements; only the sub
    form can do that.
</p>

<p>
    One potentially powerful use case for sub forms is for multi-page forms. You
    could easily create a form consisting of several sub forms, and display a
    single sub form per page, persisting data in the session between form
    submissions; only when all pages have received their data would the parent
    form be valid, allowing you to finally pass the data to the model.
</p>

<p>
    Form grouping at the display and logical level both are powerful tools, and
    this functionality is trivial with Zend_Form.
</p>

<h4>Array Support</h4>

<p>
    Many developers like to namespace their form elements under nested arrays.
    This allows for groupings of related data, as well as having several groups
    with similar data on the same page. As an example, imagine a form showing
    both a shipping and a billing address:
</p>

<div class="example"><pre><code class="language-html">
&lt;form action=\&quot;/foo/bar\&quot; method=\&quot;post\&quot;&gt;
    &lt;fieldset&gt;
        &lt;legend&gt;Shipping Address&lt;/legend&gt;
        &lt;dl&gt;
            &lt;dt&gt;Address:&lt;/dt&gt;
            &lt;dd&gt;&lt;input name=\&quot;shipping[address]\&quot; type=\&quot;text\&quot; value=\&quot;\&quot; /&gt;&lt;/dd&gt;
         
            &lt;dt&gt;City:&lt;/dt&gt;
            &lt;dd&gt;&lt;input name=\&quot;shipping[city]\&quot; type=\&quot;text\&quot; value=\&quot;\&quot; /&gt;&lt;/dd&gt;
         
            &lt;dt&gt;Postal:&lt;/dt&gt;
            &lt;dd&gt;&lt;input name=\&quot;shipping[postal]\&quot; type=\&quot;text\&quot; value=\&quot;\&quot; /&gt;&lt;/dd&gt;
        &lt;/dl&gt;
    &lt;/fieldset&gt;

    &lt;fieldset&gt;
        &lt;legend&gt;Billing Address&lt;/legend&gt;
        &lt;dl&gt;
            &lt;dt&gt;Address:&lt;/dt&gt;
            &lt;dd&gt;&lt;input name=\&quot;billing[address]\&quot; type=\&quot;text\&quot; value=\&quot;\&quot; /&gt;&lt;/dd&gt;
         
            &lt;dt&gt;City:&lt;/dt&gt;
            &lt;dd&gt;&lt;input name=\&quot;billing[city]\&quot; type=\&quot;text\&quot; value=\&quot;\&quot; /&gt;&lt;/dd&gt;
         
            &lt;dt&gt;Postal:&lt;/dt&gt;
            &lt;dd&gt;&lt;input name=\&quot;billing[postal]\&quot; type=\&quot;text\&quot; value=\&quot;\&quot; /&gt;&lt;/dd&gt;
        &lt;/dl&gt;
    &lt;/fieldset&gt;
&lt;/form&gt;
</code></pre></div>

<p>
    PHP will receive two arrays from the submitted form, 'shipping' and
    'billing'. 
</p>

<p>
    Zend_Form now allows this (as of today). To keep all existing features, and
    to allow elements and sub forms to stay de-coupled from their parent forms,
    you need to do a little configuration:
</p>

<div class="example"><pre><code class="language-php">
$shipping = new Zend_Form_SubForm('shipping');

// This next line tells the elements, validators, and decorators that they are
// part of an array; by default, the sub form name is used:
$shipping-&gt;setIsArray(true);

// This can also be accomplished by explicitly setting the array name:
$shipping-&gt;setElementsBelongTo('shipping');
</code></pre></div>

<p>
    The fun part is that this can be arbitrarily deep, by specifying the array
    key as it would appear in the form. So, as an example, if we wanted the
    entire form returned in the 'demographics' array, and 'shipping' and
    'billing' were keys in that array, we could do the following:
</p>

<div class="example"><pre><code class="language-php">
// Set base key for entire form:
$form-&gt;setElementsBelongTo('demographics');

// Set subkey for shipping sub form:
$shipping-&gt;setElementsBelongTo('demographics[shipping]');

// Set subkey for billing sub form:
$billing-&gt;setElementsBelongTo('demographics[billing]');
</code></pre></div>

<p>
    When you set or retrieve values, or validate, these array keys are honored.
    What's more, since they are configurable, you can leave them out of your
    generic forms, and only set them in your concrete instances -- allowing
    re-use and re-purposing.
</p>

<h3>Conclusion</h3>

<p>
    This post is mainly to serve as high-level overview of some of the more
    advanced features of Zend_Form. In the coming weeks, more thorough
    documentation will be present in the Zend Framework repository, allowing
    developers to understand the functionality in more depth. Hopefully I've
    whetted some people's appetites out there, and we'll get more of you
    experimenting with the current code base.
</p>

<p>
    <b>Update:</b> fixed array notation HTML example to show separate billing
    and shipping addresses.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
