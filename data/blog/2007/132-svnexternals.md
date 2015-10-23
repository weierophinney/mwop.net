---
id: 132-svnexternals
author: matthew
title: 'svn:externals'
draft: false
public: true
created: '2007-01-05T09:58:00-05:00'
updated: '2007-01-10T14:08:35-05:00'
tags:
    - programming
    - php
---
I was recently working with someone who was using Zend Framework in their
project. To keep things stable and releasable, he was doing an export of
framework into his repository and checking it in. Since files change so much in
the ZF project currently, instead of doing an rsync from a checkout into his
own repository, he decided instead to delete the directory from the repository
and re-add it everytime he was updating framework.

This seemed really inefficient to me, especially considering that it made it
incredibly difficult to merge changes from his development branch into his
production branch (deleting and re-adding directories breaks the merge process
considerably). I knew there had to be a better way.

I'd heard of the `svn:externals` property before, but never really played with
it. As it turns out, it exists for just this very type of situation. The
problem is that the [documentation of svn:externals](http://svnbook.red-bean.com/nightly/en/svn-book.html#svn.advanced.externals)
in the SVN book doesn't indicate at all how the property should be set, and
most howto's I've read omit one or more very important details. I finally
figured things out through some trial and error of my own, so I'm going to
share the process so others hopefully can learn from the experience as well.

It's actually pretty easy. This assumes that your project layout looks something like this:

    project/
        branch/
            production/
        tag/
        trunk/

- In the top of your project trunk, execute the following:

  ```bash
  $ svn propedit svn:externals .
  ```

- This will open an editor session. In the file opened by your editor, each
  line indicates a different external svn repo to pull. The first segment of
  the line is the directory where you want the pull to exist. The last segment
  is the svn repo URL to pull. You can have an optional middle argument
  indicating the revision to use. Some examples:
  - Pull framework repo from head:

    ```
    framework http://framework.zend.com/svn/framework/trunk
    ```

    - Pull framework repo from revision 2616:

    ```
    framework -r2616 http://framework.zend.com/svn/framework/trunk
    ```

- After saving and exiting, update the repo:

  ```bash
  $ svn up
  ```

- Commit changes:

  ```bash
  $ svn commit
  ```

One thing to note: any directory you specify for an `svn:externals` checkout
should **not** already exist in your repository. If it does, you will get an
error like the following:

    svn: Working copy 'sharedproject' locked
    svn: run 'svn cleanup' to remove locks

I show using revisions above; you could also pin to tags by simply checkout the
external repository from a given tag. Either way works well.

Then, when moving from one branch to another, or from the trunk to a branch,
you simply set a different `svn:externals` for each branch. For instance, your
current production might check from one particular revision, but your trunk
might simply track head; you then simply determine what the current revision
being used is on your trunk, and update svn:externals in your production branch
when you're ready to push changes in.

Hope this helps some of you out there!
