---
id: 198-Autocompletion-with-Zend-Framework-and-Dojo
author: matthew
title: 'Autocompletion with Zend Framework and Dojo'
draft: false
public: true
created: '2008-12-12T11:07:29-05:00'
updated: '2008-12-15T06:49:51-05:00'
tags:
    0: dojo
    1: php
    3: 'zend framework'
---
I've fielded several questions about setting up an autocompleter with
[Zend Framework](http://framework.zend.com/) and [Dojo](http://dojotoolkit.org/), and
decided it was time to create a HOWTO on the subject, particularly as there are
some nuances you need to pay attention to.

<!--- EXTENDED -->

Which dijits perform autocompletion?
------------------------------------

Your first task is selecting an appropriate form element capable of
autocompletion. Dijit provides two, `ComboBox` and `FilteringSelect`. However,
they have different capabilities:

- `ComboBox` allows you to enter arbitrary text; if it doesn't match the
  associated list, it is still considered valid. The text *entered* is
  submitted — ***not*** the option value. (This differs from normal dropdown
  selects.)
- `FilteringSelect` also allows you to enter arbitrary text, but it will only
  be considered valid if it matches an option provided to it. The *option
  value* is submitted, just like a normal dropdown select.

Once you've chose the appropriate form element type, you then need to specify a
`dojo.data` store. `dojo.data` provides a consistent API for data structures
consumed by dijits and other dojo components. At its heart, it's simply an
array of arbitrary JSON structures that each contain a common identifier field
containing a unique value per item. Internally, both `ComboBox` and
`FilteringSelect` can utilize `dojo.data` stores to populate their options
and/or provide matches. Dojo provides a variety of `dojo.data` stores for such
purposes.

### Defining the form element

Defining the form element is very straightforward. From your `Zend_Dojo_Form`
instance (or your form extending that class), simply call `addElement()` as
usual. Later in this tutorial, depending on the approach you use, you may need
to add some information to the element definition, but for now, all that's
needed is the most basic of element definitions:

```php
$form->addElement('ComboBox', 'myAutoCompleteField', array(
    'label' => 'My autocomplete field:',
));
```

Providing data to a dojo.data store
-----------------------------------

We're going to work backwards now, as providing data to the data store is
relatively trivial when using `Zend_Dojo_Data`.

First, we'll create an action in our controller, and assign the model and the
query parameter to the view. We'll be setting up our `dojo.data` store to send
the query string via the GET parameter `q`, so that's what we'll assign to the
view.

```php
    public function autocompleteAction()
    {
        // First, get the model somehow
        $this->view->model = $this->getModel();

        // Then get the query, defaulting to an empty string
        $this->view->query = $this->_getParam('q', '');
    }
```

Now, let's create the view script. First, we'll disable layouts; second, we'll
pass our query to the model; third, we'll instantiate our `Zend_Dojo_Data`
object with the results of our query; and finally, we'll echo the
`Zend_Dojo_Data` instance.

```php
<?php
// Disable layouts
$this->layout()->disableLayout();

// Fetch results from the model; again, merely illustrative
$results = $this->model->query($this->params);

// Now, create a Zend_Dojo_Data object.
// The first parameter is the name of the field that has a
// unique identifier. The second is the dataset. The third
// should be specified for autocompletion, and should be the
// name of the field representing the data to display in the
// dropdown. Note how it corresponds to \"name\" in the 
// AutocompleteReadStore.
$data = new Zend_Dojo_Data('id', $results, 'name');

// Send our output
echo $data;
```

That's really all there is to it. You can actually automate some of this using
the `AjaxContext` action helper, making it even simpler.

Using dojox.data.QueryReadStore
-------------------------------

We now have an endpoint for our `dojo.data` data store, so now we need to
determine which store type to use.

`dojox.data.QueryReadStore` is a fantastic `dojo.data` store allowing you to
create arbitrary queries on data. It creates the query as a JSON object:

```javascript
{
    "query": { "name": "A*" },
    "queryOptions": { "ignoreCase": true },
    "sort": [{ "attribute": "name", "descending": false }],
    "start": 0,
    "count": 10
}
```

This is problematic in two ways. First, if you were to use it directly, you'd
be limited to POST requests, submitting it as a raw post. Second, and related,
this means that requests could not be cached client-side.

Fortunately, there's an easy way to correct the situation: extend
`dojox.data.QueryReadStore` and override the `fetch` method to rewrite the
query as a simple GET query with a single parameter.

```javascript
dojo.provide("custom.AutocompleteReadStore");

dojo.declare(
    "custom.AutocompleteReadStore", // our class name
    dojox.data.QueryReadStore,      // what we're extending
    {
        fetch: function(request) {  // the fetch method
            // set the serverQuery, which sets query string parameters
            request.serverQuery = {q: request.query.name};

            // and then operate as normal:
            return this.inherited("fetch", arguments);
        }
    }
);
```

The question now is, where to create this definition?

You have two options: you can inline the custom definition (less intuitive) and
connect the data store manually to the form element, or you can create an
actual javascript class file (slightly more work) and have your form element
setup the data store for you.

### Inlining a custom QueryReadStore class extension

Inlining is a bit tricky to accomplish, as you need to declare things in the
appropriate order. When using this technique, you need to do the following:

1. require the `dojox.data.QueryReadStore` class
2. define a global JS variable that will be used to identify your store
3. use `dojo.provide` and `dojo.declare` to create your custom data store extension
4. define an onLoad event that instantiates the data store and attaches it to the form element

We can do all the above within the same view script in which we spit out our form:

```php
<?php
$this->dojo()->requireModule("dojox.data.QueryReadStore");

// Define a new data store class, and 
// setup our autocompleter data store
$this->dojo()->javascriptCaptureStart() ?>
dojo.provide("custom.AutocompleteReadStore");
dojo.declare(
    "custom.AutocompleteReadStore", 
    dojox.data.QueryReadStore, 
    {
        fetch: function(request) {
            request.serverQuery = {q: request.query.name};
            return this.inherited("fetch", arguments);
        }
    }
);
var autocompleter;
<?php $this->dojo()->javascriptCaptureEnd();

// Once dijits have been created and all classes defined,
// instantiate the autocompleter and attach it to the element.
$this->dojo()->onLoadCaptureStart() ?>
function() {
    autocompleter = new custom.AutocompleteReadStore({
        url: "/test/autocomplete",
        requestMethod: "get"
    });
    dijit.byId("myAutoCompleteField").attr({
        store: autocompleter
    });
}
<?php $this->dojo()->onLoadCaptureEnd() ?>
<h1>Autocompletion Example</h1>
<div class="tundra">
<?php echo $this->form ?>
</div>
```

This works well, and is an expedient way to get autocompletion working for your
element. However, it breaks the DRY principle as you cannot re-use the custom
class in other areas. So, let's look at a better solution

### Creating a reusable custom QueryReadStore class extension

The recommendation by the Dojo developers is that you should create this class
as a *javascript* class, with your other *javascript* code. The reasons for
this are numerous: you can re-use the class elsewhere, and you can also include
it in custom builds — which will ensure that it is stripped of whitespace and
packed, leading to smaller downloads for your end users.

The process isn't as scary as it may initially sound. Assuming that your
`public/` directory has the following structure:

```
public/
    js/
        dojo/
            dojo.js
        dijit/
        dojox/
```

what we'll do here is to create a sibling to the `dojo` subdirectory, called
`custom"` and create our class file there:

```
public/
    js/
        dojo/
            dojo.js
        dijit/
        dojox/
        custom/
            AutocompleteReadStore.js
```

We'll use the definition as originally shown above, and simply save it as
`public/js/custom/AutocompleteReadStore.js`, with one addition: after the
`dojo.provide` call, add this:

```javascript
dojo.require("dojox.data.QueryReadStore");
```

This is analagous to a `require_once` call in PHP, and ensures that the class
has all dependencies prior to declaring itself. We'll leverage this fact later,
when we hint in our `ComboBox` element what type of data store to use.

On the framework side of things, we're going to alter our element definition
slightly to include information about the `dojo.data` store it will be using:

```php
$form->addElement('ComboBox', 'myAutoCompleteField', array(
    'label'     => 'My autocomplete field:',

    // The javascript identifier for the data store:
    'storeId'   => 'autocompleter',

    // The class type for the data store:
    'storeType' => 'custom.AutocompleteReadStore',

    // Parameters to use when initializint the data store:
    'storeParams' => array(
        'url'           => '/foo/autocomplete',
        'requestMethod' => 'get',
    ),
));
```

If you've been following along closely, you'll notice that the "storeParams"
are exactly the same as what we used to initialize the data store when
inlining. The difference is that now the `ComboBox` view helper will create all
the necessary Javascript for you.

The view script now becomes greatly simplified; we no longer need to setup any
javascript, and can literally simply echo the form:

```php
<?= $this->form ?>
```

Hopefully it should now be clear which method is easiest in the long run.

Next Steps
----------

`dojox.data.QueryReadStore` offers much more than simply specifying the query
string. As noted when introducing the component, it creates a JSON structure
that also includes keys for sorting, selecting how many results to display, and
offsets when pulling results. These, too, can be added to your query strings to
allow finer grained selection of results — for instance, you could ensure that
no more than 3 or 5 results are returned, to allow for a more manageable list
of matches, or specify a sort order that makes more sense to users.

Summary
-------

Learning new tools can be difficult, and Dojo and Zend Framework are no
exceptions. One compelling reason to learn Dojo if you're using Zend Framework,
however, is that its structure and design should be familiar: it uses the same
1:1 class name:filename mapping paradigm. Additionally, because it is written
to utilize strong OOP principles, familiar concepts such as extending classes
can be used to customize Dojo for your site's needs.

Hopefully this tutorial will shed a little light on both the subject of
autocompletion in Dojo, as well as class extensions in Dojo, and help get you
started creating your own custom Dojo libraries for use with your applications.
