---
id: 186-Using-dijit.Editor-with-Zend-Framework
author: matthew
title: 'Using dijit.Editor with Zend Framework'
draft: false
public: true
created: '2008-08-28T11:50:52-04:00'
updated: '2008-08-30T09:14:48-04:00'
tags:
    0: php
    2: 'zend framework'
---
We're getting ready to release Zend Framework 1.6.0. However, one important
Dijit had to be omitted from the release as I was not able to get it working in
time: `dijit.Editor`.

This dijit is important as it provides an out-of-the-box WYSIWYG editor that
you can use with your forms. Unfortunately, actually using it with forms is
pretty tricky — Dojo actually ends up storing content *outside* the form, which
means you need to create a handler that pulls the content into a hidden element
when saving.

I *have* created an implementation, however, that you can start using now, and
I'm posting it below. It includes both a view helper for displaying it, as well
as a form element for use with `Zend_Form`.

<!--- EXTENDED -->

The View Helper looks like this:

```php
<?php
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
            'id'    => $hiddenName,
            'name'  => $hiddenName,
            'value' => $value,
            'type'  => 'hidden',
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

        $this->_createGetParentFormFunction();
        $this->_createEditorOnSubmit($hiddenAttribs['id'], $id);

        $html = '<input' . $this->_htmlAttribs($hiddenAttribs) . $this->getClosingBracket()
              . $this->textarea($textareaName, $value, $params, $attribs);
        return $html;
    }

    /**
     * Create JS function for retrieving parent form
     * 
     * @return void
     */
    protected function _createGetParentFormFunction()
    {
        $function =<<<EOJ
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

        $this->dojo->addJavascript($function);
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
        $this->dojo->onLoadCaptureStart();
        echo <<<EOJ
function() {
    var form = zend.findParentForm(dojo.byId('$hiddenId'));
    dojo.connect(form, 'onsubmit', function () {
        dojo.byId('$hiddenId').value = dijit.byId('$editorId').getValue(false);
    });
}
EOJ;
        $this->dojo->onLoadCaptureEnd();
    }
}
```

There's a lot of code in there, but the important bits are the last two
methods, which allow finding the parent form of the Editor dijit, and then
tying the form onsubmit event to an action that sets a hidden value to the
provided Editor content.

The form element is much easier:

```php
<?php
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
```

Honestly, that's it. Since the view helper does the heavy lifting of display,
all the element needs to do is to indicate which helper to use.

Putting it all together in a form, you'll need to do the following:

```php
$view->addHelperPath('My/View/Helper/', 'My_View_Helper');
$form->addPrefixPath('My_Form_Element', 'My/Form/Element');

$form->addElement('Editor', 'content');
```

The helper prefix path is probably best added in your bootstrap; the form
prefix path should be added early in your form class's `init()` method, or
passed in via configuration.

You should see this element shipped in ZF's standard library likely in ZF 1.6.1.

**Update:** Jasone E. noted that the view helper was missing a `$_module` declaration of `dijit.Editor` — this has been added.
