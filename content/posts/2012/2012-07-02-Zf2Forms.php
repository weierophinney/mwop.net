<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-07-02-zf2-beta5-forms');
$entry->setTitle('ZF2 Forms in Beta5');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-07-02 10:00', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-07-02 10:00', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  'php',
  'zf2',
  'zend framework',
));

$body =<<<'EOT'
<p>
    Forms are a nightmare for web development. They break the concept of 
    separation of concerns:
</p>

<ul>
    <li>They have a <em>display</em> aspect (the actual HTML form)</li>
    <li>They have a <em>validation</em> aspect</li>
    <li>And the two mix, as you need to display validation error messages.</li>
</ul>

<p>
    On top of that, the submitted data is often directly related to your domain
    models, causing more issues:
</p>

<ul>
    <li>Not all elements will have a 1:1 mapping to the domain model -- 
    buttons, CSRF protection, CAPTCHAs, etc. usually are application-level 
    concerns, but not domain issues.</li>
    <li>Names valid for your domain model may not be valid names for HTML 
    entities.</li>
</ul>

<p>
    Add to this that the validation logic may be re-usable outside of a forms 
    context, and you've got a rather complex problem.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

<h2>Forms in ZF2</h2>

<p>
    Starting in 2.0.0beta4, we offerred a completely rewritten Form component. 
    In fact, it's not just a Form component -- a new component, InputFilter, 
    was also added. InputFilter is a component that provides re-usable validation
    and normalization logic, and can be used with forms or your domain model.
    The Form component is basically a bridge between domain models/validation 
    and the view layer.
</p>

<p>
    However, this means a bit more complexity for the end-user. You now must:
</p>

<ul>
    <li>Create your form, which consists of elements and fieldsets.</li>
    <li>Create an input filter, consisting of inputs.</li>
    <li>Inform the form of the input filter.</li>
</ul>

<p>
    It's a bit of work. And there's more: we wanted to simplify the process of
    getting your validated values into your domain objects. For this, we added
    a concept of <em>hydrators</em>, which map the validated form values to
    an object you <em>bind</em> to the form. Now you have <em>three</em> pieces
    to keep track of -- form (and its elements), input filter (and its inputs),
    and a hydrator.
</p>

<p>
    So, a few developers had an idea: use annotations on the domain model objects
    to define these items, letting you keep it all in one place.
</p>

<p>
    While I'm not normally a fan of annotations, I immediately saw the appeal 
    in this particular situation.
</p>

<h2>An Example</h2>

<p>
    Let's consider a very simple example. The following domain object represents
    data for a user, and includes a variety of elements we'd represent in a form.
</p>

<div class="example"><pre><code language="php">
namespace MyVendor\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("user")
 */
class User
{
    /**
     * @Annotation\Attributes({"type":"text" })
     * @Annotation\Validator({"type":"Regex","options":{"regex":"/^[a-zA-Z][a-zA-Z0-9_-]{1,19}/"}})
     * @Annotation\Options({"label":"Username:"})
     */
    public $username;

    /**
     * @Annotation\Required(false)
     * @Annotation\Attributes({"type":"text" })
     * @Annotation\Options({"label":"Your full name:"})
     */
    public $fullname;

    /**
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Options({"label":"Your email address:"})
     */
    public $email;

    /**
     * @Annotation\Type("Zend\Form\Element\Url")
     * @Annotation\Options({"label":"Your home page:"})
     */
    public $uri;
}
</code></pre></div>

<p>
    So, what does the above do?
</p>

<ul>
    <li>The "name" annotation gives a form or element a specific name.</li>
    <li>The "attributes" annotation indicates what attributes to compose into 
        the form or element.</li>
    <li>Similarly, the "options" annotation specifies options to compose into
        an element. These typically include the label, but may include other
        configuration that doesn't have an exact analog in the HTML attributes.</li>
    <li>The "validator" annotation indicates a validator to compose for the 
        input for a given element. We also ship a "filter" annotation.</li>
    <li>The "type" annotation indicates a class to use for that particular form 
        or element. In the specific cases used above, the elements actually 
        provide default filters and validators, simplifying setup further!</li>
    <li>Last, but not least, the "hydrator" annotation indicates a 
        <code>Zend\Stdlib\Hydrator</code> implementation to use to relay data 
        between the form and the object.  I'll cover this more shortly.</li>
</ul>

<p>
    So, let's now turn to creating a form and consuming it.
</p>

<div class="example"><pre><code language="php">
use MyVendor\Model\User;
use Zend\Form\Annotation\AnnotationBuilder;

$user    = new User();
$builder = new AnnotationBuilder();
$form    = $builder->createForm($user);

$form->bind($user);
$form->setData($dataFromSomewhere);
if ($form->isValid()) {
    // $user is now populated!
    echo $form->username;
    return;
} else {
    // probably need to render the form now.
}
</code></pre></div>

<p>
    You're not quite done, really -- most likely, you'll need to include a 
    submit button of some sort, and it's always good practice to include a
    token to prevent CSRF injections. But with the above, you've accomplished
    the major headaches of setting up a form -- and using the data -- with 
    minimal fuss.
</p>


<h2>Much more!</h2>

<p>
    The form support in ZF2 offers a ton of other features, some of which
    are not specific to forms even.
</p>

<ul>
    <li>
        <p>
            ZF2 supports a variety of hydration strategies, which allow you to pass
            data to and from objects. The example above uses one that suggests a 1:1
            mapping between the inputs and the object properties; other strategies
            include using <code>ArrayObject</code>, using class mutator 
            methods, and more.
        </p>

        <p>
            At this point, you can hydrate an entire form, as well as individual
            fieldsets!
        </p>
    </li>

    <li>
        <p>
            You can provide custom annotations. While this feature is not 
            documented yet, you can tell the <code>AnnotationBuilder</code> 
            about additional annotation classes, as well as provide listeners
            for those annotations so that they can interact with the form
            construction process. As an example, one contributor has already
            used these features to utilize <a 
            href="http://doctrine-project.org">Doctrine</a> annotations to
            inform the builder about the name of a property, as well as
            indicate validators. (Side note: ZF2 now uses Doctrine's annotation
            syntax and parser by default.)
        </p>
    </li>

    <li>
        <p>
            There are a number of features targetting collections, so that
            your client-side code can return arbitrary numbers of a specific
            fieldset type (e.g., collecting addresses for an applicant), and
            the form will be able to validate each. You can <a 
            href="http://www.michaelgallego.fr/blog/?p=190">read more about 
            those features from the author himself</a>.
        </p>
    </li>
</ul>

<p>
    These features are all now available starting with the newly released 
    2.0.0beta5 version, which you can grab from the <a 
    href="http://packages.zendframework.com/">ZF2 packages site</a>. 
</p>

<p>
    I'm really excited with the solutions we've created in ZF2, and even more
    excited to see people put them to use!
</p>
EOT;
$entry->setExtended($extended);

return $entry;
