---
id: 245-Autoloading-Benchmarks
author: matthew
title: 'Autoloading Benchmarks'
draft: false
public: true
created: '2010-08-17T09:30:00-04:00'
updated: '2010-08-21T18:37:14-04:00'
tags:
    0: php
    1: pear
    3: 'zend framework'
---
During the past week, I've been looking at different strategies for
[autoloading](http://php.net/autoload) in [Zend Framework](http://framework.zend.com/).
I've suspected for some time that our class loading strategy might be one source
of performance degradation, and wanted to research some different approaches,
and compare performance.

In this post, I'll outline the approaches I've tried, the benchmarking stategy I
applied, and the results of benchmarking each approach.

<!--- EXTENDED -->

Autoloading Strategies
----------------------

I'm grouping strategies into two categories, PEAR/PSR-0 strategies, and classmap strategies.

I also started testing a third category. This included solutions that required
PECL extensions, specifically the
[SplClassLoader](http://github.com/metagoto/splclassloader) and
[Automap](http://pecl.php.net/package/automap) extensions. However, I ultimately
abandoned these solutions. In the case of `SplClassLoader`, I actually started
testing it, but immediately ran into segfaults. This unfortunate event made me
remember that I was looking for userland autoloaders that we could use within
Zend Framework; both `SplClassLoader` and `Automap` can be dropped in by users
at any point, but due to the requirement of compiling and installing for your
platform, could never be an explicit requirement for using Zend Framework.

### PEAR/PSR-0

Those of you familiar with Zend Framework are aware that we follow
[PEAR Coding Standards](http://pear.php.net/manual/en/coding-standards.php),
and, specific to this exercise, their 1-class-1-file naming convention. The PEAR
naming conventions dictate a 1:1 relation between the filesystem and the class.
As an example, the class `Foo_Bar_Baz` would be found in the file
`Foo/Bar/Baz.php` on your `include_path`.

This is a trivially easy convention to remember, and has been widely adopted in
the PHP world. The one and only vote the PHP Framework Interoperability Group
has held so far,
[http://groups.google.com/group/php-standards/web/psr-0-final-proposal](PSR-0.html),
has simply ratified this standard going forward (with some additional verbiage
regarding namespaces). Zend Framework's autoloader has been PSR-0 compliant
since 1.10.0 (and was PEAR-compliant prior to that).

So, the very first approach has been simply to use the *status quo*.

That said, I also looked at other PSR-0-compliant approaches for some
inspiration. The `SplClassLoader` proposed by Jon Wage, of the Doctrine project,
as a [GitHub gist](http://gist.github.com/221634) takes a couple of departures
from the ZF implementation:

- It allows specifying a specific directory under which to look for a specific
  namespace.
- Instead of acting as a singleton, you create a single instance per namespace
  you want to load, and then call its `register()` method to register with the
  `spl_autoload` registry.

Additionally, I looked at the Symfony2 `UniversalClassLoader`. While it has its
basis in the `SplClassLoader` implementation, it offers a feature we'd already
added to the ZF2 autoloader: the ability to register both namespaces and vendor
prefixes to autoload. I [combined these ideas into a custom PSR-0 implementation](http://github.com/weierophinney/zf2/blob/autoloading/library/Zend/Loader/Psr0Autoloader.php).

(Note: the `SplClassLoader` extension is a literal port of the class from the
`GitHub` gist to a C extension.)

### Classmaps

The next category of autoloader solutions I looked at are best characterized by
the term "classmaps." In this strategy, you create a map of classname/filename
pairs, and feed it to your autoloader.

To my thinking, the key benefits of this strategy include:

- Ability to deviate from the PSR-0 standard if desired (as an example, application resources).
- Ability to drop in a classmap per component. For a library such as ZF, this
  opens up possibilities for distributing individual components with fewer
  dependencies, as you do not need to also ship artifacts such as your
  autoloader.
- Fail early. If the class does not exist in the map, the autoloader can exit
  early, allowing you to drop to another autoloader in the chain — or simply
  have PHP raise its `E_FATAL` report of class-not-found.
- Drop in at will. You can use dynamic autoloaders during development, but then
  run a script during deployment or build time to generate a classmap
  autoloader. This allows you to have the benefits of a RAD cycle, while also
  reaping the benefits of a performance-optimized autoloader.

I will elaborate on the last point later, when I examine the benchmarks.

Despite the prevalence of PSR-0 style autoloaders, there are a number of
classmap autoloaders in the wild. [ez/zeta components](http://incubator.apache.org/zetacomponents/)
has used one for years, in part due to using a non-PSR-0-compliant naming
convention, but also in part due to performance considerations.
[Arne Blankerts](http://www.google.com/search?q=arne+blankerts) also introduced
me to one such solution in the form of his
[Autoload library on GitHub](http://github.com/theseer/Autoload).

When building classmaps, you can either build them manually as you develop, or
utilize a script. I like the script-based approach, as it ensures I don't forget
to add items to the map, and because it's something I can run over existing
libraries and then drop in.

When building such a script, the algorithm is quite simple:

- Recursively scan a filesystem tree
- For each PHP file found, scan for interfaces, classes, and abstract classes
- For each match, store the fully qualified class name and the file path

I [blogged about my solution](/blog/244-Applying-FilterIterator-to-Directory-Iteration) to scanning the filesystem tree earlier; an example of the script that consumes the `ClassFileLocater` class referenced in that blog and generates the actual classmap [can be found in my GitHub account](http://github.com/weierophinney/zf2/blob/autoloading/bin/zfal.php).

I took three different approaches to generating classmaps:

- Store file paths as relative to the `include_path`
- Store file paths using `__DIR__` to prefix the path
- Store file paths using `__DIR__` to prefix the path, and pass the map directly to a closure registered with `spl_autoload`.

In the first two cases, the map is stored in an array that is returned by the
script. I then pass the map file's location to an autoloader that performs an
`include` on that file, stores the map (optionally merging with one it already
contains), and then uses that map for class lookups. You [can find this autoloader on GitHub](http://github.com/weierophinney/zf2/blob/autoloading/library/Zend/Loader/ClassMapAutoloader.php).

The third case was a trick borrowed from Arne Blankert's Autoload library. I
deviated from his design in a couple of ways. First, Arne was defining the map
as a static member of his closure. Theoretically, this should ensure the map is
defined only once per request. However, in my tests, I discovered that the map
was actually being constructed in memory each and every time the closure was
invoked, and led to a serious degredation in performance. As a result, my
version creates a variable in the local scope which is then passed in to the
closure via a `use` statement:

```php
namespace test;
$_map = array( /* ... */ );
spl_autoload_register(function($class) use ($_map) {
    if (array_key_exists($class, $_map)) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . $_map[$class];
    }
});
```

Note the `namespace` declaration; this allows you to create multiple autoload
class map files without trampling on previously loaded maps.

Benchmarking Strategy
---------------------

Benchmarking autoloading is somewhat difficult; once you've autoloaded a class,
you can't autoload it again. Additionally, libraries tend to have a finite
number of classes in them, giving a limited set of data to benchmark against.
Finally, no good autoloading benchmark should be done without also testing
against an opcode cache — and if you have too many class files, you can easily
defeat the cache.

My solution was twofold:

- Create a script to generate finite, large numbers of class files
- Determine how many class files provide a reasonable set such that an opcode cache is not defeated, but also such that measurable differences may be observed.

The script for generating class files was easy to create. All I needed was a
recursive function that would iterate through the letters of the alphabet and
generate classes until a specific depth was reached;
[you can examine the script on GitHub](http://github.com/weierophinney/zf2/blob/autoloading/bin/createAutoloadTestClasses.php).

I started off by looking at `26^3` class files, and this was excellent for
getting some initial statistics. However, once I started benchmarking against an
opcode cache, I discovered that I was defeating both the realpath cache as well
as memory limits for my opcode cache. I then reduced my sampling size to `16^3`
files. This sampling size proved to be sufficiently large to both produce
reliable results on multiple runs while allowing caching to work normally.

When benchmarking, I ran the following strategies:

- Baseline: no autoloader (all `class_exists()` calls fail)
- ZF1 autoloader (PSR-0 compliant, uses `include_path`)
- PSR-0 autoloader (uses explicit namespace/path pairs, no `include_path`
- `ClassMapAutoloader` (using `include_path`)
- `ClassMapAutoloader` (using `__DIR__`-prefixed paths)
- Class map using `__DIR__`-prefixed paths, with closure registered directly to `spl_autoload`

My test algorithm was as follows:

- Load a list of all classes in the tree
- Perform any setup necessary to prepare the given autoloader
- Iterate over all classes in the list, and utilize `class_exists` to autoload

Timing was performed only over the loop. Benchmarks were run 10 times for each
strategy, in order to determine an average, as well as to correct for outliers.
Additionally, the benchmarks were performed both with and without bytecode
caching, to see what differences might occur in both environments. A sample of
such a script is as follows:

```php
include 'benchenv.php';
require_once '/path/to/zf/library/Zend/Loader/ClassMapAutoloader.php';

$loader = new Zend\Loader\ClassMapAutoloader();
$loader->registerAutoloadMap(__DIR__ . '/test/map_abs_autoload.php');
$loader->register();

echo "Starting benchmark of ClassFile map autoloader using absolute paths...\n";
$start = microtime(true);
foreach ($classes as $class) {
    if (!class_exists($class)) {
        echo "Aborting test; could not find class $class\n";
        exit(2);
    }
}
$end = microtime(true);
echo "total time: " . ($end - $start) . "s\n";
```

I had one such script for each test case. To automate running all such scripts,
and doing 10 iterations of each, I wrote the following scripts:

```bash
#!/usr/bin/zsh
# benchmark_noaccel.sh
# No opcode caching
for TYPE in baseline.php classmap_abs.php classmap_inc.php spl_autoload.php psr0_autoload.php zf1_autoload.php;do
    echo "Starting $TYPE"
    for i in {1..10};do
        curl http://autoloadbench/$TYPE
    done
done

#!/usr/bin/zsh
# benchmark_accel.sh
# With opcode caching
for TYPE in baseline.php classmap_abs.php classmap_inc.php spl_autoload.php psr0_autoload.php zf1_autoload.php;do
    echo "Starting $TYPE"
    # Clear Optimizer+ cache
    curl http://autoloadbench/optimizer_clear.php
    for i in {1..10};do
        curl http://autoloadbench/$TYPE
    done
done
```

I reran the tests a few times to ensure I wasn't seeing anomalies. While the
actual numbers I received differed between iterations, statistically, they only
varied within a few percentage points between runs.

The Results
-----------

### No Opcode Cache

| Strategy                  | Average Time (s)     |
| :------------------------ | -------------------: |
| Baseline                  | 0.0067               |
| ZF1 autoloader (inc path) | 1.2153               |
| PSR-0 (no include_path)   | 1.0758               |
| Class Map (include_path)  | 0.9796               |
| Class Map (`__DIR__`)     | 0.9800               |
| SPL closure               | 0.9520               |

### With Opcode Cache

| Strategy | Average over all (s) | Unaccel | Shortest | Ave. Accelerated |
| :------- | :------------------: | :-----: | :------: | :--------------: |
| Baseline                  | 0.0061 | 0.0053 | 0.0052 | 0.0062 |
| ZF1 autoloader (inc_path) | 0.4855 | 1.4444 | 0.3653 | 0.3789 |
| PSR-0 (no include_path)   | 0.4021 | 1.5477 | 0.2437 | 0.2748 |
| Class Map (include_path)  | 0.3022 | 1.2755 | 0.1724 | 0.1941 |
| Class Map (`__DIR__`)     | 0.2568 | 1.2253 | 0.1362 | 0.1492 |
| SPL closure               | 0.2630 | 1.2971 | 0.1341 | 0.1481 |

### Analysis

The three class map variants are the clear winners here, showing around a 25%
improvement on the ZF1 autoloader when no acceleration is present, and 60–85%
improvements when an opcode cache is in place. The three classmap approaches are
roughly equivalent when no opcode caching is in place, but the variants that do
not use the `include_path` are statistically faster when the opcode cache is
present.

Perhaps the most interesting finding for myself was seeing how much the
`include_path` affects performance, particularly when using an opcode cache. In
each case, I had the directory with my test classes listed as the first item in
the `include_path` — which is the optimal location (previous benchmarking and
profiling I've done shows that performance degrades quickly the deeper the
matching entry is within the `include_path`). Even in the non-accelerated tests,
the PSR-0 implementation, which does not use the `include_path`, is &gt;10%
faster, a difference that jumps to almost 40% with acceleration. The same
differences are true also with the class map implementations.

While these changes are very much micro-optimizations (remember, the numbers
above indicate `16^3` classes loaded — a single class loads in matters of
1/10,000th of a second), if you are loading hundreds or thousands of unique
classes over your application lifecycle, the evidence clearly shows some
significant performance benefits from the usage of class maps and
fully-qualified paths.

Conclusions
-----------

Each approach has its merits. During development, you don't want to necessarily
run a script to generate the class map or manually update the class map every
time you add a new class. That said, if you expect a lot of traffic to your
site, it's trivially easy to run a script during deployment to build the class
map for you, and thus let you eke out a little extra performance from your
application.

One clear realization from this experiment is that the `include_path` is a poor
way to resolve files in current incarnations of PHP. While the degradation is
not huge when your initial path matches, it still introduces a performance hit.
Additionally, it makes setup of the applications harder, as you then must
document the proper usage of the `include_path`; using `__DIR__` with either a
PSR-0-style or class map autoloader is more easily portable and requires less
education of end-users.

One interesting use case for a classmap based autoloader is to assist in
optimizing existing ZF1 applications. The script could be run over your
"application" directory to build a classmap of your application resource
classes. This would allow you to bypass the various resource autoloaders and
custom logic of the dispatcher for finding controller classes. It could also
potentially serve as one part of a migration suite between ZF1 and ZF2.

I will be recommending that Zend Framework 2.0 use both PSR-0 and class map
strategies, without reliance on the `include_path`, and provide tools and
scripts to aid deployment.
