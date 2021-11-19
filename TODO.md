# TODO

## New features

- [ ] Blog
  - [ ] Add media capabilities
    - [ ] Tooling to search for CC media based on post keywords
    - [ ] Include media in blog post as a banner with the title
    - [ ] Social media integration (Twitter cards, Facebook open graph, etc.)
  - [ ] Expand tweet API
    - [ ] Allow specifying a blog ID to tweet (`blog:tweet [-k apikey] <id>`) to tweet an arbitrary blog post.
- [ ] Github
  - [ ] Re-create PuSH functionality to have it update my feed(s)?
    Basically, I could have the initial deploy grab data from GitHub.
    A webhook would then get any updates and choose whether or not to update the internal feed.
    That way, instead of polling, I just get updates.
- [ ] Contact form
  Emails come from me, to me, so they are automatically marked "read" by gmail.
  On top of that, the reply-to does not work.
  I would like to make this work better.
