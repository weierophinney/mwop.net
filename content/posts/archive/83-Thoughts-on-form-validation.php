<?php
use PhlyBlog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('83-Thoughts-on-form-validation');
$entry->setTitle('Thoughts on form validation');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1121048243);
$entry->setUpdated(1121079414);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
));
$entry->setTags(array (
  0 => 'php',
));

$body =<<<'EOT'
<p>
    I've been doing a lot of thinking on form validation recently. Among other
    things, I want to be using a standard set of tools for validating form input
    at work; I'm also rewriting the family website in PHP, and want to have
    consistency there as well. Finally, I truly buy into <a
        href="http://shiflett.org/">Chris Shiflett's</a> <a
        href="http://shiflett.org/archive/97">top two security practices</a>:
    filter input, escape output. Validation should always be done, and should be
    done rigorously; don't allow anything more than is necessary to get the work
    done.
</p>
<p>
    I flirted briefly in the past month with <a
        href="http://pear.php.net/packages/HTML_QuickForm">HTML_QuickForm</a>.
    Being an observer on the CGI::Application mailing lists, HQF looks like
    PHP's answer to perl's <a
        href="http://search.cpan.org/search?query=Data%3A%3AFormValidator&mode=module">Data::FormValidator</a>.
    HQF has a high frequency of posts on the php-pear-general lists. A lot of
    people seem happy with it. I decided to try it out as an example plugin for
    <a href="http://cgiapp.sourceforge.net/">Cgiapp</a> for the latest release.
</p>
<p>
    My problem is that I want to be able to define form validation in a file
    outside my script. The reason for this is that as I extend and reuse
    classes, I often find that I can use the same general run-modes for a
    method... just so long as the form validation logic is separate. This allows
    me, for instance, to decide that in one application instance I will require
    fields A-M, but in another, I only need A-E (or vice versa). But it requires
    no changes to the actual application logic, as the validations are kept
    separately, and  I have the application instance indicate which validation
    file to utilize.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<p>
    My approach with HQF was to create some utility methods for setting up forms
    from configuration files. This would allow the programmer to define the form
    in a file, and then pass the location of that file to the class in the
    instance script. Then, in the application class, the programmer would simply
    pass the parameter to the utility methods, and voila! form validation is
    done.
</p>
<p>
    Unfortunately, HQF is, quite simply, next to impossible to code this way. I
    went to some pretty serious effort to do so, but the best I got was to
    utilize nested arrays in a file that gets eval()'d -- not a viable solution
    for a security conscious programmer. The problems I saw were:
</p>
<ul>
    <li>Validation and filtering are kept separate from the actual element
    definitions. To my thinking, the validation and filtering are on an element;
    the element should be the basic block, and the validations and filters are
    attributes or properties of it. While I understand the idea behind HQF's
    decision, I found it non-intuitive in practice, and also felt it created
    more code.</li>
    <li>Elements, validations, and filters often accepted parameters that were
    difficult to define in a static file (things like the form action attribute,
    or configuration arrays).  Special elements were too difficult to create.
    Select elements with options and such were simply too difficult to create
    via a definition file.</li>
</ul>
<p>
    In the end, the code I created to parse a file that contained a form
    validation was much larger than any code I could hand write with HQF. And
    the form validation file itself was of similar size to hand-coding
    equivalent HQF code.
</p>
<p>
    I started working on a form validation library this past week, and after
    many hours of effort, realized I was creating something as large in scope as
    HQF. Granted, I was building it with the idea of using a SimpleXML file to
    contain the validation logic, and it was going to accomodate that, but in
    the end, it was a hairy piece of code, and for most of my forms, overkill.
</p>
<p>
    And then it hit me: just about every form I create is slightly different,
    and, in general, I find one of the following occur:
</p>
<ul>
    <li>The amount of input is so small that I can validate it myself in fewer
    lines than utilizing an established library.</li>
    <li>There's a lot of data, but much of it is in radios, checkboxes, or
    dropdowns, and can be validated by checking against arrays.</li>
    <li>The amount of data is highly specialized, and I have to validate by hand
    anyways.</li>
</ul>
<p>
    In summary: it's rare that I get any development benefit from using a
    monolithic validation library. By <em>development</em> benefit, I mean
    savings in time or effort.
</p>
<p>
    Where does that leave me? Well, on further analysis, I realized that the
    main reason I could see to using a library would be for those sets of data
    that I often need to validate, but for which there isn't a built-in way in
    PHP to do so: email addresses, URIs, phone numbers, dates, etc.
    Additionally, I may want a few pre-filters -- things like stripping all
    non-numerics or non-alphas, stripping tags, trim()ing, etc. I still want to
    automate as much as possible, but only the common types.
</p>
<p>
    I envison being able to use an INI-style file like the following:
</p>
<pre>
[name]
label="Name:"
error="Please provide your name; use only alphabetical characters, commas, hyphens, periods, and single quotes"
required=true
rule1type=regex
rule1data="/^[a-z .,'-]+$/"
filter1type=trim
filter2type=htmlentities

[email]
label="Email:"
error="Please provide a valid email address"
required=true
rule1type=email
filter1type=trim

[state]
label="State of residence:"
error="Please select your state from the drop-down list provided"
required=false
rule1type=in_array
rule1data=ME,NH,VT,MA,NY,CT,RI
</pre>
<p>
    This style would catch 80% of the cases I have, which would simplify and
    expedite my development by leaving me to deal with only the other 20%.
</p>
<p>
    I was considering how I was going to code this up -- what structure to use
    in the class, whether to require class instantiation or use static methods,
    etc. -- and then realized that <a
        href="http://paul-m-jones.com/blog/">Paul M. Jones</a> had given me some
    pointers on the use of <a
        href="http://www.solarphp.com/home/index.php?area=Solar_Valid">Solar_Valid</a>
    when I suggested to him that I'd like to include it as a plugin on the next
    release of <a href="http://cgiapp.sourceforge.net/">Cgiapp</a>. I looked at
    his class, and it does exactly what I was considering coding for the
    validations. With a similar class for filtering (yes, Paul, I'll contribute
    that code, if you'd like!), it should become fairly trivial to write a
    validation routine that could parse a file like the above and then perform
    as I desire.
</p>
<p>
    This wasn't meant to be a plug for Paul, however, nor a call for developers.
    I want to stimulate discussion: how do others validate forms? Do we all come
    to the same conclusions after having done hundreds of form validations --
    that there is no magic bullet? Or have I missed the magic bullet? Is some
    automation a good thing? Or should every form have its own specific
    programmatic logic? Is there a nice lean library already that does this
    stuff well and simply? Or is that unattainable? Did I miss the boat on HQF?
    Or is it bloat?
</p>
<p> 
    Leave your comment!  
</p>
EOT;
$entry->setExtended($extended);

return $entry;