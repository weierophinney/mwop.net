---
id: 2022-04-26-tailwind
author: matthew
title: 'TailwindCSS Tips'
draft: false
public: true
created: '2022-04-26T14:15:00-05:00'
updated: '2022-04-26T14:15:00-05:00'
tags:
    - css
    - php
    - tailwind
    - tailwindcss
image:
    url: /images/tailwindcss.svg
    creator: 'Tailwind Labs Inc.'
    attribution_url: https://tailwindcss.com
    alt_text: 'TailwindCSS'
    license: 'Proprietary'
    license_url: https://tailwindcss.com/brand
---

I've been dabbling in CSS my entire career.
In the early days, it was relatively simple, as our browsers were fairly limited.
But over the years, CSS has become more and more capable, allowing styles to target only tags with specific tag attributes, only apply at specific screen sizes, and even perform complex layouts using things like flexbox and grid.
I take a bit of time every now and then to understand these things... but since I don't use CSS a ton, it's hard to keep up.

As such, over the past decade, I've tended to use CSS frameworks, and generally [Bootstrap](https://getbootstrap.com).
Every couple years, a new major version is dropped, and I have to learn new structures and components, and struggle to get things to look the way I want.
Worse, when I use these frameworks, injecting custom CSS often means understanding how the framework is already styling components so I can ensure things don't conflict.

The last couple years, I've been keeping an eye on [TailwindCSS](https://tailwindcss.com/).
I've been a bit skeptical, as its declarative, utility-first approach looks a lot like doing CSS _inside_ your HTML, which, honestly, feels off.
I've typically subscribed to the idea of [semantic HTML](https://en.wikipedia.org/wiki/Semantic_HTML), which advocates separating style from markup.
Having styles directly that mimic CSS directives associated with every tag feels like an unholy mix, and a far cry from semantic HTML.

And then there's the hype.
The original author and project lead of Tailwind is a huge hype man, and hype tends to turn me off.
That's on me, not them, but having been in the business for over two decades, I'm finding I'm more and more likely to discount hyped products and projects, because I've seen them come and go so frequently; there's often an indirect relationship between the amount of hype and the long-term viability of a project.
I've also often observed that hype serves as a way for users to deflect reasonable critique, and the more vehement the user base, the less interested I am in engaging with them because of this.
Clearly, YMMV, but it was definitely something keeping me from really investigating Tailwind.

However, recently, in helping out [the PHP Foundation](https://opencollective.com/phpfoundation), I volunteered to setup their static website, and the team requested using TailwindCSS, so I dove in.

And I discovered... I kind of love it.

This is one of those "I'll be googling this in the future" posts.

<!--- EXTENDED -->

## How it works

I won't go into how to get started with Tailwind; their docs do a great job of doing that for you.
However, I will give a quick overview of how things work, so you can see (a) where I'm coming from, and (b) what you'll be doing when you start with Tailwind.

The way I've always learned to do CSS is to first write HTML, and then write CSS to style that HTML the way you want it to appear.

```html
<div class="box">
  <h2>Title</h2>
  <p>Some content</p>
</div>

<style>
.box {
  margin: 0.5rem;
  background-color: #333333;
  border: #000000
  padding: 0.5rem;
}

.box h2 {
  background-color: #dedede;
  padding: 0.5rem;
  color: #111111;
  font-size: 2rem;
  min-width: max-content;
}

.box p {
  padding: 0.5rem;
  font-size: 1.2rem;
  min-width: max-content;
}
</style>
```

Tailwind instead provides a ton of "utility" classes, targetting just about every CSS directive.
You then add these HTML classes to elements to style them:

```html
<div class="m-2 p-2 bg-neutral-600 border-black">
  <h2 class="min-w-full p-2 bg-neutral-300 text-4xl text-neutral-800">Title</h2>
  <p class="min-w-full p-2 text-xl">Some content</p>
</div>
```

Behind the scenes, you run the Tailwind CLI tool, and it analyzes your HTML to generate CSS for you.
You can even have it watching for filesystem changes to files you're interested in (templates, JS scripts, etc.), and it will regenerate the CSS automatically.

## Tip 1: Layers

Out of the box, Tailwind does a CSS reset that removes **all** styles.
This is great, because it means that any changes you make, you can anticipate exactly what will hapen.
It's also a pain in the ass, because everything is unstyled: your headings look just like your paragraphs, your lists look just like your paragraphs, and there's no way to tell where one paragraph ends and another begins.

So, the first thing you'll want to do is define your base styles.
Tailwind has a very specific suggestion for where and how to do this: in the "base" layer.

Opening your site CSS file, you'll see these directives when you begin:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

Each of these is a Tailwind "layer".
Tailwind defines these to allow grouping items based on how they are normally used, and defining styles in these layers allows (a) the ability to override them with other utility classes, and (b) the ability to strip them from the CSS file if they are never used in your application.

The "base" layer is the one that should be used for your base styles, the ones that a CSS reset usually touches, such as paragraphs, headings, links, lists, etc.

Within your declarations, you can use a directive called `@apply`.
This allows you to use the same utility "classes" you'd use in your HTML markup, which means you end up using the same language everywhere when creating your design.
So, as an example:

```css
@layer base {
    html {
        @apply font-serif;
    }

    p {
        @apply mb-4 leading-relaxed tracking-wide;
    }

    a {
        @apply underline decoration-1 decoration-dotted transition duration-300;
    }

    a:hover {
        @apply font-semibold decoration-solid;
    }

    h1, h2, h3, h4, h5, h6 {
        @apply font-sans;
    }

    h1 {
        @apply font-bold text-8xl mb-2;
    }

    /* and so on */
}
```
As your project matures, you may notice that there are certain styles you are using and repeating everywhere, and they are getting untenable to remember and type consistently.
For me, these were things like buttons and form input.
Styles like these go into your "components" layer:

```css
@layer components {
    .btn {
        @apply rounded p-2 bg-action-bg border-action-border text-site-bg hover:bg-action-active active:bg-action-active active:text-site-bg transition duration-300
    }

    .text-input {
        @apply w-full mb-4 border-site-bg p-2 rounded bg-site-fg text-site-bg;
    }

    .label {
        @apply block mb-2 font-semibold;
    }

    .textarea {
        @apply w-full mb-4 border-site-bg p-2 rounded-lg bg-site-fg text-site-bg;
    }
}
```

Adding those allows me to use more succinct classes:

```html
<label class="label" for="email">Email address</label>
<input class="text-input" name="email" type="email" />
```

> #### Classes only!
>
> The "components" layer allows class definitions only!
> The documentation mentions this in passing, but my immediate assumption was that I could use any CSS selector within the components layer.
> What I found was that specifying element types (e.g. `label` or `input[type="text"], input[type="email"]`) did not work; no styles were generated.
> Once I switched to creating CSS class definitions only, everything worked.
>
> I'm sure somebody will point out I am wrong and/or detail how to do it correctly; until then, this was what worked.

Sometimes, however, using layers doesn't work.

When?

Typically, when the content is being generated.
The two times I ran into problems:

- I have content that I generate via either cronjob or webhook, and the HTML is not in any source file I can rely on when running Tailwind.
- I have other content being generated on-the-fly via JavaScript, and these are generating just regular HTML tags.

For these, you **create declarations outside all layers**.
Doing so ensures that Tailwind **always** includes the definitions in the generated CSS, regardless of whether or not it found any markup with them.

Luckily, you can continue to use `@apply` even with these.
And, better, you can use any CSS selectors you want, which allows you to be as specific as you need.

## Tip #2 Use a preprocessor

I often have 3rd party CSS I want to include, with [PrismJS](https://prismjs.com) and [FontAwesome](https://fontawesome.com) being typical examples.
With normal CSS or even a preprocessor, I can use `@import` to slurp them in, but Tailwind doesn't know how to do this on its own (and, honestly, shouldn't).
Additionally, minimizing CSS is a great way to get a small performance boost, as it ensures smaller file sizes for your browser to fetch.

Fortunately, you can use Tailwind with preprocessors as well, and the documentation [details configuration with a variety of them](https://tailwindcss.com/docs/using-with-preprocessors).

I ended up going with PostCSS, as it has very few dependencies, and is trivial to setup.
However, in doing so, I couldn't just use the Tailwind CLI to watch my CSS anymore, and needed to setup additional tooling.

The results were not difficult to achieve.
I installed PostCSS locally, as well as the PostCSS CLI tool globally.
I also installed the postcss-import plugin, autoprefixer, and cssnano.
My `postcss.config.js` ended up like this:

```javascript
module.exports = {
  plugins: {
    'postcss-import': {},
    tailwindcss: {},
    autoprefixer: {},
    ...(process.env.IS_PROD ? { cssnano: {} } : {})
  }
}
```

From there, I installed chokidar-cli, and added the following script definitions to my `package.json`:

```json
"scripts": {
  "dev:css": "postcss css/site.css -o dist/css/site.css",
  "watch": "npx chokidar \"./css/*.css\" \"../templates/**/*.phtml\" \"../src/**/*.phtml\" -c \"npm run dev:css\""
}
```

This allows me to run `npm run watch` when I'm actively updating markup and stylesheets in order to generate my CSS.

## Results

The results were pretty astonishing for me.

- I was able to reduce my CSS by 50%.
- I was able to reduce my JS from around 10k to less than 1k, as I was able to remove jQuery and the Bootstrap JS entirely.
- I also reduced the overall size of my templates, which suprised me, as I was producing far more HTML classes; the flip side of that was I was able to reduce the amount of markup I was actually using (more on that below).

## Pros and Cons

I'm going to start with the cons, because, honestly, I can live with them, particularly when I consider the benefits.

### The Cons

**First**, there are So. Many. Classes. On. Every. Element.

I mean, this is not entirely uncommon:

```html
<a class="block border-t border-mwop-light border-dotted px-2 py-4 no-underline text-sm font-semibold text-mwop-light transition duration-300 hover:bg-mwop-fg hover:text-mwop-bg" href="<?= $this->url('blog') ?>">Blog</a>
```

Yes, it perfectly describes how the link should act, both in normal and hover states, but sheesh.

**Second**, I still needed a stylesheet, because Tailwind has a very comprehensive reset that strips all base styles off EVERYTHING.
Every. Thing.

When you first start out, when you view through a browser, you cannot distinguish one type of content from another.
It all blends together.

So I have a stylesheet defining my base styles, so that things like paragraphs and headings and list items at least have some default layout and styling.
In the end, this is a net "meh"; I kind of assume I should have base styling anyways, and at least I know _exactly_ how things are styled initially, instead of having to compensate for how different browsers define the base styles.
It's just a bit disconcerting at first.

**Third**, I found myself repeating certain combinations all over the place.
This really irks the part of me that likes to adhere to the [DRY principle](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself), as this sort of repetition can lead to disconnect later when I make a change in one place, and forget the umpteen other places I'd used that combination.
On the flip side, this is exactly what the component layer is about, and I found once I'd decided I liked a particular element styling, I'd create a component style that uses `@apply` to recreate it, and this would allow me to re-use the style everywhere else.
But, you know... that also takes away from the base core principle of Tailwind, which is to avoid that at all cost.

**Fourth**, I find it kind of crazy that you have to have your templates available to build your CSS.
I've been doing multi-stage Docker builds for some time now, and generally build assets in one stage, to copy into the final production container.
Previously, this could be done in a minimal way (copying just the asset directory to the intermediary container), but now I have to copy in a bunch of other items in the tree to ensure CSS builds.
It's fine, but it feels like an antipattern.
I could likely build and commit the assets when I need to regenerate them, but then I need to worry about accidentally pushing a dev build versus a production build, and I want to avoid that as much as possible.

### The pros

With the cons out of the way, I'll turn to what I liked.


First, it was much easier to prototype changes, particularly once I learned the mnemonics for the various utility classes and sizing.
I would consider what I wanted an element to look like, start typing away, save, refresh the page, and see the results immediately.

Second, I was surprised immediately that my production builds were approximately 50% less CSS in the end.
Tailwind does some intelligent optimizations to only include the classes you actually use, which reduces the CSS size dramatically.

Third, because of the utility classes, I was able to mix together styles for multiple screen sizes, which meant I was able to get rid of most of the JS I was using.
I ended up only needing JS for toggling mobile navigation and for displaying search results, and both of these were trivial to write in vanilla Javascript.
I was able to get rid of jQuery entirely, as well as the Bootstrap JS.

This leads to my fourth point: I have far fewer dependencies in my asset build chain, and zero that have security vulnerabilities now.
Previously, I was using Gulp to build my CSS and JS, and was using SASS as a pre-processor; it was a ridiculous amount of dependencies, and I _always_ had multiple items installed as sub-dependencies that Dependabot was flagging with security vulnerabilities.
I was able to ditch Gulp entirely.
My CSS is built using PostCSS and Tailwind; my JS is too small to need dedicated build tools, and I can thus use a very plain `Makefile` to create my production assets.

Fifth, I was pleasantly surprised when I was able to move to **better** semantic HTML structure.
Bootstrap is essentially "div soup", utilizing different combinations of divs to achieve layout; with Tailwind, I was able to revert to semantic HTML to achieve these same effects.
Yes, I have some ridiculously long HTML element class declarations, but the actual HTML **structure** is minimal.
I estimate I reduced overall HTML structure by around 30%.

Sixth, I (typically — see above comments about adding base styles and creating components to reduce repetition) no longer needed to look up a style in the stylesheet to understand how an element would look.
The class declarations serve as a shorthand.
It was much easier to iterate as a result.

Seventh, I found I was learning, applying, and understanding CSS, particularly complex things like flexbox and positioning, better than I have in the past, largely because I was applying it directly on the structures.

Which leads to my eighth and last point: even with CSS frameworks like Bootstrap, I've struggled to have decent responsive design.
I'm well aware that folks view my site on a variety of devices (heck, I go from desktop to tablet to phone several times a day myself); a responsive design that is readable is absolutely critical in today's world.
While Bootstrap has responsive breakpoints, I always struggled to understand how they applied and when, and often got things wrong.
Tailwind's documentation does a fabulous job of detailing what the breakpoints are, and, more importantly, provides examples on how to combine different breakpoints with CSS classes to achieve your designs.
I had portions of my site I never fully made responsive under Bootstrap that I was able to get working beautifully with very little effort with Tailwind.
Even more important: it's trivially easy to define your own breakpoints if you want — though I really do not think I'll ever need to.

## Parting notes

In the end, I found myself quite happy with the change.

I had a realization during the process as well: with CSS frameworks like Bootstrap... I was being left with a a lot of terribly _unsemantic_ markup, despite the fact that I explicitly prefer semantic HTML.
I was able to recreate the styles I'd previously had... and then, miraculously, start improving on and polishing them — which I'd largely not done before due to the need to battle with pre-conceived components in Bootstrap.
Being able to be fully free of these structures means more freedom to experiment and play with other layouts and design options, as I'm not locked into a specific design framework.

So, hat's off to Tailwind.
Well done!
