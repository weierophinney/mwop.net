<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('71-HTML_QuickForm-with-Smarty-Quickstart');
$entry->setTitle('HTML_QuickForm with Smarty Quickstart');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1115521898);
$entry->setUpdated(1115524660);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've been wanting to play with <a
        href="http://pear.php.net/package/HTML_QuickForm">HTML_QuickForm</a> for
    quite some time, but the documentation has looked rather sparse and scary
    regarding the use of HTML_QuickForm with <a
        href="http://smarty.php.net/">Smarty</a>. Since I've been busy at work,
    and I haven't wanted to take the time to learn a new library, I've simply
    been putting it off.
</p>
<p>
    Last night, I browsed through the package documentation, and noticed a link
    to an <a
    href="http://www.thelinuxconsultancy.co.uk/quickform.php">HTML_QuickForm
    Getting Started Guide</a> by Keith Edmunds. I was pleased to discover that
    he also has a <a
    href="http://www.thelinuxconsultancy.co.uk/smarty-guide.php">guide to
    using Smarty with HTML_QuickForm</a>. I got started with these tutorials,
    and found them excellent. I found myself wanting a little more meat
    afterwards, and found that I could now turn to the PEAR docs and actually
    make sense of it all.
</p>
<p>
    While I think Mr. Edmunds tutorials are great for starters, I found that
    there were a few pointers I could have used right off the bat. I present
    them here for you.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    I'm not going to do any code samples; at this point, you should have read
    the aformentioned tutorials. The following notes are to give a little more
    information to help get you running <em>faster</em> with HTML_Quickform +
    Smarty -- by giving some practical tools and steps for utilizing the combo.
</p>
<p>
   The way I see it, creating and using forms with HTML_QuickForm + Smarty uses
   the following steps: 
</p>
<ol>
    <li><b>Create the form elements</b>. This is done via the 'addElement()'
    method of HQF, and involves setting a form element type, name, and
    label.</li>
    <li><b>Create form input filters (Optional)</b>. This is done via the
    'applyFilter()' method of HQF; commonly, you may want to trim() input, and,
    if magic_quotes are on, stripslashes().</li>
    <li><b>Create validation rules</b>. This is done via the 'addRule()' method,
    which takes the element name, the text to display on error, and the rule
    type as arguments (and a fourth argument if required by the rule type).
    There's some really slick stuff you can do here:
        <ul>
            <li><b>You can add more than one rule to any given element.</b> For
            instance, a particular element may need to be both 'required' and
            match a given regex; you can add rules for both of these</li>
            <li>The <b>regex</b> rule lets you pass a fourth argument to
            addRule() with the regex the value should match.</li>
            <li>The <b>email</b> rule makes it so you don't have to remember a
            regex for emails every time you write a form.</li>
        </ul>
    </li>
    <li><b>Test for validation</b>. What I discovered is that it's actually nice
    to <b>do this as an if-then-else block</b>. This allows you to do different
    things based on whether or not the $form-&gt;validate() succeeds; for
    instance, you may not wish to display the same template on success as on
    failure. For certain, you will not need to do anything with the renderer if
    the form validates (assuming you don't display the form or its values
    again).</li>
    <li><b>Set renderer options</b>. Basically, if the form fails validation,
    you'll need to display it again. Most likely, you want to display something
    new back to the user: what elements had errors, and the error message for
    them. Additionally, you may want to show which elements are required. Each
    renderer supplies several methods to allow such customization. These methods
    each can display elements:
        <ul>
            <li>{$label} -- the form element's label</li>
            <li>{$html} -- the HTML for the form element</li>
            <li>{$error} -- the error text for the form element</li>
            <li>{$required} -- a flag indicating whether or not an element is
            required</li>
        </ul>
    If either the {$label} or {$html} element is referenced in the template
    passed to one of these methods, the appropriate field (label or html) for
    that form element will be affected before being passed to the master form
    template.<br /><br />
    The following methods are supplied by a renderer class:
        <ul>
            <li><b>setRequiredTemplate()</b>. Pass it a template as a string;
            this template will be called for each <em>required</em> element. So,
            you'll typically want to modify the {$label} element in the
            template, usually with some sort of flag, like an asterix.</li>
            <li><b>setErrorTemplate()</b>. Pass it a template as a string; this
            template will be displayed if an error occurs for a given element.
            Again, like setRequiredTemplate(), you'll typically want to modify
            the label, this time by appending the error string.</li>
            <li><b>setRequiredNote()</b>. Pass it a template as a string; this
            template can be displayed by calling the $form_data.requirednote
            field in the template (assuming you assign your form data to
            $form_data). This should be simply some text to the effect of '*
            These fields are required.' (and really, in a Smarty template, this
            probably isn't necessary).</li>
        </ul>
    </li>
    <li><b>Freeze and Process</b>. If the form validates, you should then freeze
    it, and, if necessary, process the submission. HQF has a method,
    'process()', that allows you to pass the values to a callback. However, you
    might find it easiest to simply take the values and process them yourself.
    You can do this easily and safely with the <b>exportValues()</b> method,
    which returns an associative array with form key =&gt; value pairs as they
    exist <em>post filtering</em>; additionally, it will only pass an array
    containing those elements handled by the form (i.e., no other values, even
    if passed, will be returned).</li>
</ol>
<h4>Parting Notes</h4>
<p>
    To my thinking, a config script/class/file could be written that would
    perform the first three steps, defining the form elements, their filters,
    and their validations; this could even be extended to handle the renderer
    options. Then, it would be a simple matter of calling that, testing for
    validation, and then either re-displaying the form or processing it with the
    values returned.
</p>
<p>
    (I'll probably use the above to create a Cgiapp plugin for just this very
    purpose, similar in nature to CGI::Applications Data::FormValidator plugin.)
</p>
EOT;
$entry->setExtended($extended);

return $entry;