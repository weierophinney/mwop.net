<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('161-Submitting-Bug-Reports');
$entry->setTitle('Submitting Bug Reports');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1204995973);
$entry->setUpdated(1205072316);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'programming',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    Full disclosure: I am employed by <a href="http://www.zend.com/">Zend</a> to
    program <a href="http://framework.zend.com/">Zend Framework</a>. That said,
    the following is all my opinion, and is based on my experiences with Zend
    Framework, as well as answering questions on a variety of mailing lists and
    with other OSS projects (PEAR, Solar, and Cgiapp in particular).
</p>

<p>
    One of my biggest pet peeves in the OSS world is vague bug/issue reports and feature requests. I
    cannot count the number of times I've seen a report similar to the following:
</p>

<blockquote>
    &lt;Feature X&gt; doesn't work; you need to fix it <b>now!</b>
</blockquote>

<p>
    If such a report comes in on an issue tracker, it's invariably marked
    critical and high priority.
</p>

<p>
    What bothers me about it? Simply this: it gives those responsible for
    maintaining Feature X absolutely no information to work on: what result they
    received, what was expected, or how exactly they were using the feature. The
    reviewer now has to go into one or more cycles with the reporter fishing for
    that information -- wasting everyone's time and energy.
</p>

<p>
    Only slightly better are these reports:
</p>

<blockquote>
    &lt;Feature X&gt; doesn't work -- I keep getting &lt;Result X&gt; from it,
    which is incorrect.
</blockquote>

<p>
    At least this tells the reviewers what they reporter is receiving... but it
    doesn't tell them how they got there, or what they're expecting.
</p>

<p>
    So, the following should be your mantra when reporting issues or making feature requests:
</p>

<ul>
    <li>What is the minimum code necessary to reproduce the issue or show the desired API?</li>
    <li>What is the expected result?</li>
    <li>What is the actual result?</li>
</ul>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>What makes up a good issue report?</h2>

<p>
    A good issue report has to contain the above three points, plain and simple.
    Without this information, a reviewer simply does not have the tools with
    which to properly deal with the issue.
</p>

<h3>Reproduce code</h3>

<p>
    Quite often, you'll find that an application breaks. It is up to you to find
    the root cause of that breakage: what minimal amount of code do I need to
    write in order to cause the breakage to occur? Sometimes this will require a
    little digging -- you may have a result that is unexpected, but it may be a
    symptom of something breaking earlier.
</p>

<p>
    For example, in working on Zend_Form in the past couple weeks, I had a
    number of issues reported against how the new MultiCheckbox element was
    working. One issue noted that populate() was not properly populating the
    checkboxes.
</p>

<p>
    When pressed for reproduce code, the reporter provided the $_POST array with
    which they were trying to populate the form:
</p>

<div class="example"><pre><code lang="php">
$_POST = array(
    'foo' =&gt; array(
        0 =&gt; array(0 =&gt; 'bar'),
        1 =&gt; array(0 =&gt; 'baz'),
        2 =&gt; array(0 =&gt; 'bat')
    )
);
</code></pre></div>
    
<p>
    In looking at it, I knew immediately that it was related to another issue
    that had been reported. In that issue, the reporter noted that the input
    elements rendered by Zend_Form for MultiCheckbox elements had redundant
    array notation:
</p>

<div class="example"><pre><code lang="html">
    &lt;input type=\&quot;checkbox\&quot; value=\&quot;foo\&quot; name=\&quot;foo[][]\&quot; value=\&quot;bar\&quot; /&gt;
</code></pre></div>
<br />

<p>
    In the former case, we're seeing a symptom of the latter case: the redundant
    array notation was causing a form submission that simply could not populate
    the element. If the reporter of the former case had looked at the form used
    to send the $_POST data they posted in the tracker, either they likely would
    have noticed the similar issue already reported in the tracker -- or I, as
    the reviewer, would have been able to quickly mark the bug as a duplicate.
</p>

<p>
    Regardless, the main point is this: using the value of a POST request to
    reproduce an issue is not doing your homework. You need to look for the
    <em>minimal</em> code necessary to reproduce the issue, and the value
    provided in $_POST is typically a <em>symptom</em> of an issue that has
    already occurred.
</p>

<p>
    Another rule of thumb with creating reproduce code is to keep the
    environment minimal. Try writing up <em>fresh</em> code in a scratchpad that
    you can run over and over again until you get the result that you're trying
    to report. This does a few things: it helps simplify the use case causing
    the issue, and it often will help you track down exactly where things begin
    to break. Sometimes, and I can attest to this, it helps you find places
    where you're doing things wrong in your <em>own</em> code in the first
    place, alleviating the need entirely to submit a report.
</p>

<p>
    What does the reviewer do with this code? Well, a <em>good</em> developer
    will use it as a test case in the unit test suite -- which is another reason
    to keep the code down to the minimum required to reproduce the issue. This
    code will often end up in the test suite in order to document the issue
    report -- as well as to prove, once a solution is in place, that the issue
    has been resolved.
</p>

<p>
    The above advice is useful even when reporting a feature request, this
    information is useful. The reviewer then gets an idea of the desired API,
    and they can write a test case against it.
</p>

<h3>Expected Results</h3>

<p>
    In addition to the reproduce case, you should provide the expected results.
    These show clearly your expectations of the code. The reviewer can use this
    information in several ways:
</p>

<ul>
    <li>In the test suite, the reviewer can use the expected results in
    assertions to verify the issue (or prove that it is now corrected)</li>

    <li>To show where the reporter has flawed assumptions. In some cases, the
    expectations of the code are different than the documented assertions, and
    the reviewer can then point out where the differences lie -- which helps to
    educate the reporter in proper usage of the code.</li>

    <li>In the case of a feature request, this will indicate how the reporter
    expects the new feature to behave. The reviewer can then use that
    expectation as an assertion in the test suite.</li>
</ul>

<h3>Actual Results</h3>

<p>
    The actual results are important as they contrast against the expected
    results, showing where the breakage is. If the reviewer cannot recreate
    these results, then it likely means that the reproduce code provided is not
    the actual code needed to reproduce the issue, or it may mean that
    environmental differences -- differences in OS or PHP version, for instance
    -- may be a factor in recreating the issue.
</p>

<p>
    In the case of a feature request, you could omit the actual results, as
    there won't be any.
</p>

<h3>Always search for your issue or feature request</h3>

<p>
    Finally, one additional mantra to add to your repertoire: search the issue
    tracker and/or mailing lists <em>before</em> reporting an issue or
    requesting a feature. I cannot tell you how many bugs I've closed as
    duplicates, or how many times I've had to respond to an email with the
    phrase, "this is a known issue." It pays to do your homework: search and see
    if others have made the same request. In many cases, you may actually find a
    <em>solution</em> to your issue posted by others -- either a way to extend a
    class to get the behaviour you're expecting, a patch to the software, or
    even a note regarding what public release or snapshot contains a fix.
    There's no reason to waste people's time by reporting a known issue.
</p>

<p>
    The best time to search for your issue, believe it or not, is <em>after</em>
    you've done the other steps. Until you know exactly what code reproduces the
    issue, and have clearly defined your expectations and the real results, it
    can be difficult to identify when your issue matches another. 
</p>

<h2>In Conclusion</h2>

<ul>
    <li>What is the minimum code necessary to reproduce the issue?</li>
    <li>What is the expected result?</li>
    <li>What is the actual result?</li>
    <li>Have you searched for similar requests in public forums?</li>
</ul>

<p>
    If you can start answering the above questions <em>before</em> posting your
    issues, you'll start receiving more detailed and useful responses from those
    reviewing your issues or feature requests, and reduce the number of "I don't
    understand" or "I need more information" responses. Guaranteed.
</p>
EOT;
$entry->setExtended($extended);

return $entry;