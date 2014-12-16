<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('245-Autoloading-Benchmarks');
$entry->setTitle('Autoloading Benchmarks');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1282051800);
$entry->setUpdated(1282430234);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'php',
  1 => 'pear',
  3 => 'zend framework',
));

$body =<<<'EOT'
<p>
During the past week, I've been looking at different strategies for <a href="http://php.net/autoload">autoloading</a> in <a href="http://framework.zend.com/">Zend Framework</a>. I've suspected for some time that our class loading strategy might be one source of performance degradation, and wanted to research some different approaches, and compare performance.
</p>

<p>
In this post, I'll outline the approaches I've tried, the benchmarking stategy I applied, and the results of benchmarking each approach.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2> Autoloading Strategies </h2>

<p>
I'm grouping strategies into two categories, PEAR/PSR-0 strategies, and classmap strategies.
</p>

<p>
I also started testing a third category. This included solutions that required PECL extensions, specifically the <a href="http://github.com/metagoto/splclassloader">SplClassLoader</a> and <a href="http://pecl.php.net/package/automap">Automap</a> extensions. However, I ultimately abandoned these solutions. In the case of <code>SplClassLoader</code>, I actually started testing it, but immediately ran into segfaults. This unfortunate event made me remember that I was looking for userland autoloaders that we could use within Zend Framework; both <code>SplClassLoader</code> and <code>Automap</code> can be dropped in by users at any point, but due to the requirement of compiling and installing for your platform, could never be an explicit requirement for using Zend Framework.
</p>

<h3> PEAR/PSR-0 </h3>

<p>
Those of you familiar with Zend Framework are aware that we follow <a href="http://pear.php.net/manual/en/coding-standards.php">PEAR Coding Standards</a>, and, specific to this exercise, their 1-class-1-file naming convention. The PEAR naming conventions dictate a 1:1 relation between the filesystem and the class. As an example, the class <code>Foo_Bar_Baz</code> would be found in the file "Foo/Bar/Baz.php" on your <code>include_path</code>. 
</p>

<p>
This is a trivially easy convention to remember, and has been widely adopted in the PHP world. The one and only vote the PHP Framework Interoperability Group has held so far, <a href="PSR-0.html">http://groups.google.com/group/php-standards/web/psr-0-final-proposal</a>, has simply ratified this standard going forward (with some additional verbiage regarding namespaces). Zend Framework's autoloader has been PSR-0 compliant since 1.10.0 (and was PEAR-compliant prior to that).
</p>

<p>
So, the very first approach has been simply to use the <em>status quo</em>.
</p>

<p>
That said, I also looked at other PSR-0-compliant approaches for some inspiration. The <code>SplClassLoader</code> proposed by Jon Wage, of the Doctrine project, as a <a href="http://gist.github.com/221634">GitHub gist</a> takes a couple of departures from the ZF implementation:
</p>

<ul>
<li> It allows specifying a specific directory under which to look for a specific namespace</li>
<li> Instead of acting as a singleton, you create a single instance per namespace you want to load, and then call its <code>register()</code> method to register with the <code>spl_autoload</code> registry.</li>
</ul>

<p>
Additionally, I looked at the Symfony2 <code>UniversalClassLoader</code>. While it has its basis in the <code>SplClassLoader</code> implementation, it offers a feature we'd already added to the ZF2 autoloader: the ability to register both namespaces and vendor prefixes to autoload. I <a href="http://github.com/weierophinney/zf2/blob/autoloading/library/Zend/Loader/Psr0Autoloader.php">combined these ideas into a custom PSR-0 implementation</a>.
</p>

<p>
(Note: the <code>SplClassLoader</code> extension is a literal port of the class from the <code>GitHub</code> gist to a C extension.)
</p>

<h3> Classmaps </h3>

<p>
The next category of autoloader solutions I looked at are best characterized by the term "classmaps." In this strategy, you create a map of classname/filename pairs, and feed it to your autoloader.
</p>

<p>
To my thinking, the key benefits of this strategy include:
</p>

<ul>
<li> Ability to deviate from the PSR-0 standard if desired (as an example, application resources).</li>
<li> Ability to drop in a classmap per component. For a library such as ZF, this opens up possibilities for distributing individual components with fewer dependencies, as you do not need to also ship artifacts such as your autoloader.</li>
<li> Fail early. If the class does not exist in the map, the autoloader can exit early, allowing you to drop to another autoloader in the chain -- or simply have PHP raise its <code>E_FATAL</code> report of class-not-found.</li>
<li> Drop in at will. You can use dynamic autoloaders during development, but then run a script during deployment or build time to generate a classmap autoloader. This allows you to have the benefits of a RAD cycle, while also reaping the benefits of a performance-optimized autoloader.</li>
</ul>

<p>
I will elaborate on the last point later, when I examine the benchmarks.
</p>

<p>
Despite the prevalence of PSR-0 style autoloaders, there are a number of classmap autoloaders in the wild.  <a href="http://incubator.apache.org/zetacomponents/">ez/zeta components</a> has used one for years, in part due to using a non-PSR-0-compliant naming convention, but also in part due to performance considerations.  <a href="http://www.google.com/search?q=arne+blankerts">Arne Blankerts</a> also introduced me to one such solution in the form of his <a href="http://github.com/theseer/Autoload">Autoload library on GitHub</a>.
</p>

<p>
When building classmaps, you can either build them manually as you develop, or utilize a script. I like the script-based approach, as it ensures I don't forget to add items to the map, and because it's something I can run over existing libraries and then drop in.
</p>

<p>
When building such a script, the algorithm is quite simple:
</p>

<ul>
<li> Recursively scan a filesystem tree</li>
<li> For each PHP file found, scan for interfaces, classes, and abstract classes</li>
<li> For each match, store the fully qualified class name and the file path</li>
</ul>

<p>
I <a href="http://weierophinney.net/matthew/archives/244-Applying-FilterIterator-to-Directory-Iteration">blogged about my solution</a> to scanning the filesystem tree earlier; an example of the script that consumes the <code>ClassFileLocater</code> class referenced in that blog and generates the actual classmap <a href="http://github.com/weierophinney/zf2/blob/autoloading/bin/zfal.php">can be found in my GitHub account</a>.
</p>

<p>
I took three different approaches to generating classmaps:
</p>

<ul>
<li> Store file paths as relative to the <code>include_path</code></li>
<li> Store file paths using <code>__DIR__</code> to prefix the path</li>
<li> Store file paths using <code>__DIR__</code> to prefix the path, and pass the map directly to a closure registered with <code>spl_autoload</code>.</li>
</ul>

<p>
In the first two cases, the map is stored in an array that is returned by the script. I then pass the map file's location to an autoloader that performs an <code>include</code> on that file, stores the map (optionally merging with one it already contains), and then uses that map for class lookups. You <a href="http://github.com/weierophinney/zf2/blob/autoloading/library/Zend/Loader/ClassMapAutoloader.php">can find this autoloader on GitHub</a>.
</p>

<p>
The third case was a trick borrowed from Arne Blankert's Autoload library. I deviated from his design in a couple of ways. First, Arne was defining the map as a static member of his closure. Theoretically, this should ensure the map is defined only once per request. However, in my tests, I discovered that the map was actually being constructed in memory each and every time the closure was invoked, and led to a serious degredation in performance. As a result, my version creates a variable in the local scope which is then passed in to the closure via a <code>use</code> statement:
</p>

<div class="example"><pre><code class="language-php">
namespace test;
$_map = array( /* ... */ );
spl_autoload_register(function($class) use ($_map) {
    if (array_key_exists($class, $_map)) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . $_map[$class];
    }
});
</code></pre></div>

<p>
Note the <code>namespace</code> declaration; this allows you to create multiple autoload class map files without trampling on previously loaded maps.  
</p>

<h2> Benchmarking Strategy </h2>

<p>
Benchmarking autoloading is somewhat difficult; once you've autoloaded a class, you can't autoload it again. Additionally, libraries tend to have a finite number of classes in them, giving a limited set of data to benchmark against.  Finally, no good autoloading benchmark should be done without also testing against an opcode cache -- and if you have too many class files, you can easily defeat the cache.
</p>

<p>
My solution was twofold:
</p>

<ul>
<li> Create a script to generate finite, large numbers of class files</li>
<li> Determine how many class files provide a reasonable set such that an opcode cache is not defeated, but also such that measurable differences may be observed.</li>
</ul>

<p>
The script for generating class files was easy to create. All I needed was a recursive function that would iterate through the letters of the alphabet and generate classes until a specific depth was reached; <a href="http://github.com/weierophinney/zf2/blob/autoloading/bin/createAutoloadTestClasses.php">you can examine the script on GitHub</a>.
</p>

<p>
I started off by looking at <code>26^3</code> class files, and this was excellent for getting some initial statistics. However, once I started benchmarking against an opcode cache, I discovered that I was defeating both the realpath cache as well as memory limits for my opcode cache. I then reduced my sampling size to <code>16^3</code> files. This sampling size proved to be sufficiently large to both produce reliable results on multiple runs while allowing caching to work normally.
</p>

<p>
When benchmarking, I ran the following strategies:
</p>

<ul>
<li> Baseline: no autoloader (all class_exists() calls fail)</li>
<li> ZF1 autoloader (PSR-0 compliant, uses <code>include_path</code>)</li>
<li> PSR-0 autoloader (uses explicit namespace/path pairs, no <code>include_path</code></li>
<li> <code>ClassMapAutoloader</code> (using <code>include_path</code>)</li>
<li> <code>ClassMapAutoloader</code> (using <code>__DIR__</code>-prefixed paths)</li>
<li> Class map using <code>__DIR__</code>-prefixed paths, with closure registered directly to <code>spl_autoload</code></li>
</ul>

<p>
My test algorithm was as follows:
</p>

<ul>
<li> Load a list of all classes in the tree</li>
<li> Perform any setup necessary to prepare the given autoloader</li>
<li> Iterate over all classes in the list, and utilize <code>class_exists</code> to autoload</li>
</ul>

<p>
Timing was performed only over the loop. Benchmarks were run 10 times for each strategy, in order to determine an average, as well as to correct for outliers.  Additionally, the benchmarks were performed both with and without bytecode caching, to see what differences might occur in both environments. A sample of such a script is as follows:
</p>

<div class="example"><pre><code class="language-php">
include 'benchenv.php';
require_once '/path/to/zf/library/Zend/Loader/ClassMapAutoloader.php';

$loader = new Zend\Loader\ClassMapAutoloader();
$loader-&amp;gt;registerAutoloadMap(__DIR__ . '/test/map_abs_autoload.php');
$loader-&amp;gt;register();

echo \&quot;Starting benchmark of ClassFile map autoloader using absolute paths...\n\&quot;;
$start = microtime(true);
foreach ($classes as $class) {
    if (!class_exists($class)) {
        echo \&quot;Aborting test; could not find class $class\n\&quot;;
        exit(2);
    }
}
$end = microtime(true);
echo \&quot;total time: \&quot; . ($end - $start) . \&quot;s\n\&quot;;
</code></pre></div>

<p>
I had one such script for each test case. To automate running all such scripts, and doing 10 iterations of each, I wrote the following scripts:
</p>

<div class="example"><pre><code class="language-bash">
#!/usr/bin/zsh
# benchmark_noaccel.sh
# No opcode caching
for TYPE in baseline.php classmap_abs.php classmap_inc.php spl_autoload.php psr0_autoload.php zf1_autoload.php;do
    echo \&quot;Starting $TYPE\&quot;
    for i in {1..10};do
        curl http://autoloadbench/$TYPE
    done
done
</code></pre></div>

<div class="example"><pre><code class="language-bash">
#!/usr/bin/zsh
# benchmark_accel.sh
# With opcode caching
for TYPE in baseline.php classmap_abs.php classmap_inc.php spl_autoload.php psr0_autoload.php zf1_autoload.php;do
    echo \&quot;Starting $TYPE\&quot;
    # Clear Optimizer+ cache
    curl http://autoloadbench/optimizer_clear.php
    for i in {1..10};do
        curl http://autoloadbench/$TYPE
    done
done
</code></pre></div>

<p>
I reran the tests a few times to ensure I wasn't seeing anomalies. While the actual numbers I received differed between iterations, statistically, they only varied within a few percentage points between runs.
</p>

<h2> The Results </h2>

<h3> No Opcode Cache </h3>

<table>
<tr>
<td><strong>Strategy</strong>               </td>
<td><strong>Average Time (s)</strong></td>
</tr>
<tr>
<td>Baseline                 </td>
<td>0.0067            </td>
</tr>
<tr>
<td>ZF1 autoloader (inc path)</td>
<td>1.2153            </td>
</tr>
<tr>
<td>PSR-0 (no include_path)  </td>
<td>1.0758            </td>
</tr>
<tr>
<td>Class Map (include_path) </td>
<td>0.9796            </td>
</tr>
<tr>
<td>Class Map (<code>__DIR__</code>)    </td>
<td>0.9800            </td>
</tr>
<tr>
<td>SPL closure              </td>
<td>0.9520            </td>
</tr>
</table>

<h3> With Opcode Cache </h3>

<table>
<tr>
<td><strong>Strategy</strong>               </td>
<td><strong>Average over all (s)</strong></td>
<td><strong>Unaccel</strong></td>
<td><strong>Shortest</strong></td>
<td><strong>Ave. Accelerated</strong></td>
</tr>
<tr>
<td>Baseline                 </td>
<td>0.0061                </td>
<td>0.0053   </td>
<td>0.0052    </td>
<td>0.0062</td>
</tr>
<tr>
<td>ZF1 autoloader (inc_path)</td>
<td>0.4855                </td>
<td>1.4444   </td>
<td>0.3653    </td>
<td>0.3789</td>
</tr>
<tr>
<td>PSR-0 (no include_path)  </td>
<td>0.4021                </td>
<td>1.5477   </td>
<td>0.2437    </td>
<td>0.2748</td>
</tr>
<tr>
<td>Class Map (include_path) </td>
<td>0.3022                </td>
<td>1.2755   </td>
<td>0.1724    </td>
<td>0.1941</td>
</tr>
<tr>
<td>Class Map (<code>__DIR__</code>)    </td>
<td>0.2568                </td>
<td>1.2253   </td>
<td>0.1362    </td>
<td>0.1492</td>
</tr>
<tr>
<td>SPL closure              </td>
<td>0.2630                </td>
<td>1.2971   </td>
<td>0.1341    </td>
<td>0.1481</td>
</tr>
</table>

<h3> Analysis </h3>

<p>
The three class map variants are the clear winners here, showing around a 25% improvement on the ZF1 autoloader when no acceleration is present, and 60-85% improvements when an opcode cache is in place. The three classmap approaches are roughly equivalent when no opcode caching is in place, but the variants that do not use the <code>include_path</code> are statistically faster when the opcode cache is present.  
</p>

<p>
Perhaps the most interesting finding for myself was seeing how much the <code>include_path</code> affects performance, particularly when using an opcode cache. In each case, I had the directory with my test classes listed as the first item in the <code>include_path</code> -- which is the optimal location (previous benchmarking and profiling I've done shows that performance degrades quickly the deeper the matching entry is within the <code>include_path</code>). Even in the non-accelerated tests, the PSR-0 implementation, which does not use the <code>include_path</code>, is &gt;10% faster, a difference that jumps to almost 40% with acceleration. The same differences are true also with the class map implementations.
</p>

<p>
While these changes are very much micro-optimizations (remember, the numbers above indicate <code>16^3</code> classes loaded -- a single class loads in matters of 1/10,000th of a second), if you are loading hundreds or thousands of unique classes over your application lifecycle, the evidence clearly shows some significant performance benefits from the usage of class maps and fully-qualified paths.
</p>

<h2> Conclusions </h2>

<p>
Each approach has its merits. During development, you don't want to necessarily run a script to generate the class map or manually update the class map every time you add a new class. That said, if you expect a lot of traffic to your site, it's trivially easy to run a script during deployment to build the class map for you, and thus let you eke out a little extra performance from your application.
</p>

<p>
One clear realization from this experiment is that the <code>include_path</code> is a poor way to resolve files in current incarnations of PHP. While the degradation is not huge when your initial path matches, it still introduces a performance hit.  Additionally, it makes setup of the applications harder, as you then must document the proper usage of the <code>include_path</code>; using <code>__DIR__</code> with either a PSR-0-style or class map autoloader is more easily portable and requires less education of end-users.
</p>

<p>
One interesting use case for a classmap based autoloader is to assist in optimizing existing ZF1 applications. The script could be run over your "application" directory to build a classmap of your application resource classes. This would allow you to bypass the various resource autoloaders and custom logic of the dispatcher for finding controller classes. It could also potentially serve as one part of a migration suite between ZF1 and ZF2.
</p>

<p>
I will be recommending that Zend Framework 2.0 use both PSR-0 and class map strategies, without reliance on the <code>include_path</code>, and provide tools and scripts to aid deployment. 
</p>
EOT;
$entry->setExtended($extended);

return $entry;
