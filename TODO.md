# TODO

## Refactoring

- [ ] Refactor site console tooling to symfony/console
- [ ] Refactor Contact subnamespace to a module
- [ ] Refactor Blog subnamespace to a module
  - [ ] Move document content retrieval and pagination into a Repository class
  - [ ] Refactor CLI tooling to symfony/console
- [ ] Refactor Github subnamespace to a module
  - [ ] Refactor CLI tooling to symfony/console
  - [ ] Remove PuSH subnamespace? or set it up to do something interesting, like
    update my feed?
- [ ] Move general site classes to new module/library "Mwop"
- [ ] Rename middleware that are handlers to handlers
- [ ] Upgrade to PSR-15 final specs

## New features

- [ ] Blog
  - [x] Add search capabilities
  - [ ] Add media capabilities
    - [ ] Tooling to search for CC media based on post keywords
    - [ ] Include media in blog post as a banner with the title
    - [ ] Social media integration (Twitter cards, Facebook open graph, etc.)
- [ ] Github
  - [ ] Update PuSH functionality to have it update my feed(s)?
- [ ] Contact
  - [ ] Use something like mailchimp instead of gmail?
  - [ ] Change this to a mailing list subscription?
- [ ] API
  - [ ] Tweet latest blog post entry
