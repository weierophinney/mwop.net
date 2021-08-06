# TODO

## Bugfixes

## New features

- [ ] Blog
  - [ ] Add media capabilities
    - [ ] Tooling to search for CC media based on post keywords
    - [ ] Include media in blog post as a banner with the title
    - [ ] Social media integration (Twitter cards, Facebook open graph, etc.)
- [ ] Github
  - [ ] Re-create PuSH functionality to have it update my feed(s)?
    Basically, I could have the initial deploy grab data from GitHub.
    A webhook would then get any updates and choose whether or not to update the internal feed.
    That way, instead of polling, I just get updates.
- [ ] API
  - [ ] Tweet latest blog post entry

## Deployment

- [ ] Switch to a docker-compose deployment model
  - Means I can have different PHP versions for each application
  - Simplifies rollback
  - Ensures I can test locally in a fashion that mimics production closely
