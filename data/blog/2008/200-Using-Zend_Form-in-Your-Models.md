---
id: 200-Using-Zend_Form-in-Your-Models
author: matthew
title: 'Using Zend_Form in Your Models'
draft: false
public: true
created: '2008-12-22T08:30:00-05:00'
updated: '2008-12-27T20:53:45-05:00'
tags:
    0: php
    2: 'zend framework'
---
A [number](http://blog.astrumfutura.com/index.php?url=archives/373-The-M-in-MVC-Why-Models-are-Misunderstood-and-Unappreciated.html)
of [blog](http://akrabat.com/2008/12/13/on-models-in-a-zend-framework-application/)
[posts](http://codeutopia.net/blog/2008/12/17/the-problems-faced-by-a-common-model-interface-in-frameworks/)
have sprung up lately in the Zend Framework community discussing the Model in
the [Model-View-Controller pattern](http://en.wikipedia.org/wiki/Model-view-controller).
[Zend Framework](http://framework.zend.com/) has never had a concrete Model class or
interface; our stand has been that models are specific to the application, and
only the developer can really know what would best suit it.

Many other frameworks tie the Model to data access — typically via the
[ActiveRecord](http://en.wikipedia.org/wiki/Active_record_pattern) pattern or a
[Table Data Gateway](http://martinfowler.com/eaaCatalog/tableDataGateway.html)
— which completely ignores the fact that this is tying the Model to the method
by which it is persisted. What happens later if you start using memcached? or
migrate to an SOA architecture? What if, from the very beginning, your data is
coming from a web service? What if you *do* use a database, but your business
logic relies on associations *between* tables?

While the aforementioned posts do an admirable job of discussing the various
issues, they don't necessarily give any concrete approaches a developer *can*
use when creating their models. As such, this will be the first in a series of
posts aiming to provide some concrete patterns and techniques you can use when
creating your models. The examples will primarily be drawing from Zend
Framework components, but should apply equally well to a variety of other
frameworks.

<!--- EXTENDED -->

Input Filtering and Forms
-------------------------

In most cases, you want your model to perform its own input filtering. The
reason is because input filtering is domain logic: it's the set of rules that
define what input is valid, and how to normalize that input.

However, how does that fit in with forms? Zend Framework has a `Zend_Form`
component, which allows you to specify your validation and filter chains, as
well as rules for how to render the form via decorators. The typical pattern is
to define a form, and in your controller, pass input to it; if it validates,
you then pass the values to the model.

What if you were to instead attach the *form* to the *model*?

Some argue that this violates the concept of "separation of concerns", due to
the fact that it mixes rendering logic into the model. I feel this is a
pedantic argument. When attached to a form, `Zend_Form` can be used strictly as
an input filter; you would pull the form *from* the model when you wish to
render it, and perform any view-specific actions — configuring decorators,
setting the action and method, etc — within your *view* script. Additionally,
the various plugins — validators, filters, decorators — are not loaded until
they are *used* — meaning there is little to no overhead from the decorators
when you merely use `Zend_Form` as an input filter.

Basically, this approach helps you adhere to the DRY principle (one
validation/filter chain), while simultaneously helping you keep a solid
separation of business and view logic. Finally, you gain one or more form
representations of your model, which helps with rapid application development,
as well as providing a solid, semantic tie between the model and the view.

So, on to the technique.

Attaching Forms to Models
-------------------------

What I've been doing is adding a `getForm()` accessor to my models that takes
an optional argument, the type of form to retrieve. This is then used within
the model any time validation is necessary. (Some models require multiple
forms, so best to plan for it early. A good example is a model that represents
a user — you will need a login *and* registration form.) Let's look at it in
action:

```php
class Spindle_Model_Bug
{
    protected $_forms = array();

    public function getForm($type = 'bug')
    {
        $type  = ucfirst($type);
        if (!isset($this->_forms[$type])) {
            $class = 'Spindle_Model_Form_' . $type;
            $this->_forms[$type] = new $class;
        }
        return $this->_forms[$type];
    }

    public function save(array $data)
    {
        $form = $this->getForm();
        if (!$form->isValid($data)) {
            return false;
        }

        $storage = $this->getStorage();
        if ($form->getValue('id')) {
            $id = $form->getValue('id');
            $storage->update($form->getValues(), $id));
        } else {
            $id = $storage->insert($form->getValues());
        }

        return $id;
    }
}
```

As the above code snippet demonstrates, the form acts as an input filter: you
use it first to ensure the data provided is valid, and then to ensure the data
you pass to your persistence layer is normalized according to your rules. You
can also use it to verify the existence of certain optional values, as done
here, in order to ascertain the actual action necessary to persist the data.

What Happens in the Controller and View?
----------------------------------------

Within your controller actions, you then have a slight paradigm shift. Instead
of validating a form and then passing filtered data to the model, you simply
attempt to save data to the model:

```php
class BugController
{
    public function processAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->_helper->redirector('new');
        }

        if (!$id = $this->model->save($request->getPost())) {
            // Failed validation; re-render form page
            $this->view->model = $model;
            return $this->render('new');
        }

        // redirect to view newly saved bug
        $this->_helper->redirector('view', null, null, array('id' => $id));
    }
}
```

There's very little logic there, and no mention of forms whatsoever. So, how do
we actually render the form? Note that the model is passed to the view — which
ultimately gives us access to the form.

```php
$form = $this->model->getForm();
$form->setMethod('post')
     ->setAction($this->url(array('action' => 'process')));
echo $form;
```

This makes semantic sense; you're rendering a form that will be used to filter
data for a given model. Note that some view logic is given — the form method
and action are set here in the view layer. This is appropriate, as we're now
performing display-related logic.

Summary
-------

There are of course other ways to solve the problem, but this is a convenient
and expedient solution that maximizes use of the various existing components.
Attaching forms to your models keeps all logic related to input validation —
including error reporting — in one place, and ensures that your forms do not go
out of date when you change your model — as you will be updating your
validation rules and list of allowed input in the form itself.

In the next post, we'll look at
[using and applying Access Control Lists (ACLs) in your models](/blog/201-Applying-ACLs-to-Models.html).
