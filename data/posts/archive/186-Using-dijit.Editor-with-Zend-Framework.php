<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('186-Using-dijit.Editor-with-Zend-Framework');
$entry->setTitle('Using dijit.Editor with Zend Framework');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1219938652);
$entry->setUpdated(1220102088);
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
    We're getting ready to release Zend Framework 1.6.0. However, one important
    Dijit had to be omitted from the release as I was not able to get it working
    in time: dijit.Editor.
</p>

<p>
    This dijit is important as it provides an out-of-the-box WYSIWYG editor that
    you can use with your forms. Unfortunately, actually using it with forms is
    pretty tricky -- Dojo actually ends up storing content <em>outside</em> the
    form, which means you need to create a handler that pulls the content into a
    hidden element when saving.
</p>

<p>
    I <em>have</em> created an implementation, however, that you can start using
    now, and I'm posting it below. It includes both a view helper for displaying
    it, as well as a form element for use with Zend_Form. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    The View Helper looks like this:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php
/** Zend_Dojo_View_Helper_Textarea */
require_once 'Zend/Dojo/View/Helper/Textarea.php';

/**
 * dijit.Editor view helper
 * 
 * @uses       Zend_Dojo_View_Helper_Textarea
 * @category   My
 * @package    My_View
 * @subpackage Helper
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version    $Id: $
 */
class My_View_Helper_Editor extends Zend_Dojo_View_Helper_Textarea
{
    /**
     * @param string Dijit type
     */
    protected $_dijit = 'dijit.Editor';

    /**
     * @param string Dojo module
     */
    protected $_module = 'dijit.Editor';

    /**
     * dijit.Editor
     * 
     * @param  string $id 
     * @param  string $value 
     * @param  array $params 
     * @param  array $attribs 
     * @return string
     */
    public function editor($id, $value = null, $params = array(), $attribs = array())
    {
        $hiddenName = $textareaName = $id;

        $hiddenAttribs = array(
            'id'    =&gt; $hiddenName,
            'name'  =&gt; $hiddenName,
            'value' =&gt; $value,
            'type'  =&gt; 'hidden',
        );

        if (array_key_exists('id', $attribs)) {
            $hiddenAttribs['id'] = $attribs['id'];
            $attribs['id'] .= 'Editor';
            $id = $attribs['id'];
        }

        if (']' == $textareaName[strlen($textareaName) - 1]) {
            $textareaName = rtrim($textareaName, ']');
            $textareaName .= 'Editor]';
        }

        $this-&gt;_createGetParentFormFunction();
        $this-&gt;_createEditorOnSubmit($hiddenAttribs['id'], $id);

        $html = '&lt;input' . $this-&gt;_htmlAttribs($hiddenAttribs) . $this-&gt;getClosingBracket()
              . $this-&gt;textarea($textareaName, $value, $params, $attribs);
        return $html;
    }

    /**
     * Create JS function for retrieving parent form
     * 
     * @return void
     */
    protected function _createGetParentFormFunction()
    {
        $function =&lt;&lt;&lt;EOJ
if (zend == undefined) {
    var zend = {};
}
zend.findParentForm = function(elementNode) {
    while (elementNode.nodeName.toLowerCase() != 'form') {
        elementNode = elementNode.parentNode;
    }
    return elementNode;
};
EOJ;

        $this-&gt;dojo-&gt;addJavascript($function);
    }

    /**
     * Create onSubmit binding for element
     * 
     * @param  string $hiddenId 
     * @param  string $editorId 
     * @return void
     */
    protected function _createEditorOnSubmit($hiddenId, $editorId)
    {
        $this-&gt;dojo-&gt;onLoadCaptureStart();
        echo &lt;&lt;&lt;EOJ
function() {
    var form = zend.findParentForm(dojo.byId('$hiddenId'));
    dojo.connect(form, 'onsubmit', function () {
        dojo.byId('$hiddenId').value = dijit.byId('$editorId').getValue(false);
    });
}
EOJ;
        $this-&gt;dojo-&gt;onLoadCaptureEnd();
    }
}
</code></pre></div>

<p>
    There's a lot of code in there, but the important bits are the last two
    methods, which allow finding the parent form of the Editor dijit, and then
    tying the form onsubmit event to an action that sets a hidden value to the
    provided Editor content.
</p>

<p>
    The form element is much easier:
</p>

<div class="example"><pre><code class="language-php">
&lt;?php
/** Zend_Dojo_Form_Element_Dijit */
require_once 'Zend/Dojo/Form/Element/Dijit.php';

/**
 * dijit.Editor
 * 
 * @uses       Zend_Dojo_Form_Element_Dijit
 * @category   My
 * @package    My_Form
 * @subpackage Element
 * @license    New BSD {@link http://framework.zend.com/license/new-bsd}
 * @version    $Id: $
 */
class My_Form_Element_Editor extends Zend_Dojo_Form_Element_Dijit
{
    /**
     * @var string View helper
     */
    public $helper = 'Editor';
}
</code></pre></div>

<p>
    Honestly, that's it. Since the view helper does the heavy lifting of
    display, all the element needs to do is to indicate which helper to use.
</p>

<p>
    Putting it all together in a form, you'll need to do the following:
</p>

<div class="example"><pre><code class="language-php">
$view-&gt;addHelperPath('My/View/Helper/', 'My_View_Helper');
$form-&gt;addPrefixPath('My_Form_Element', 'My/Form/Element');

$form-&gt;addElement('Editor', 'content');
</code></pre></div>

<p>
    The helper prefix path is probably best added in your bootstrap; the form
    prefix path should be added early in your form class's <code>init()</code>
    method, or passed in via configuration.
</p>

<p>
    You should see this element shipped in ZF's standard library likely in ZF
    1.6.1.
</p>

<p>
    <b>Update:</b> Jasone E. noted that the view helper was missing a $_module
    declaration of "dijit.Editor" -- this has been added.
</p>
EOT;
$entry->setExtended($extended);

return $entry;
