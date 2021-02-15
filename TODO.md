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
- [ ] API
  - [ ] Tweet latest blog post entry

## Deployment

- Ensure production server has latest swoole
- Ensure production server has latest Composer v2
  - Make sure all apps can use it!

- Create generic pheanstalk server/worker setup.
  - Listen as a webhook, queueing a deployment message based on information provided and filters specified.
    - Must validate webhook token
    - Filters:
      - which repository was pushed
      - which branch was pushed
  - Worker will then listen for deployment message(s), running deployer for the selected website.
    - Worker should likely "pull" the repo containing the deployer script
  - (See weierophinney/phly-deployment-webhook)

- Setup webhook on GitHub
  - push events
  - shared token
