# TODO

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

### Architecture

A Caddy server sits in front of all sites and reverse proxies to each.
As such, this can be a relatively simple setup:

- services
  - php
  - redis
- volumes
  - mwop_net_redis (for persisting session and blog cache data)

### Prerequisites

- Docker
- Docker-compose
- `docker volume create mwop_net_redis`

### Deployment

- [ ] Resolve "previous" symlink to concrete directory and store value as "$old"
- [ ] Resolve "current" symlink to concrete directory and store value as "$previous"
- [ ] Store repository sha1 for deployment as "$new"
- [ ] Prepare new deployment
  - [ ] Push or pull the repo at "$new"
    - In new directory based on "$new"
  - [ ] Retrieve env
    - Resolve env version from value in repo
    - Fetch to `.env` file in new directory
  - [ ] Build: `docker-compose build`
- [ ] Deploy
  - [ ] Stop current deployment
    - cd "current"
    - docker-compose down
  - [ ] Symlink new deployment directory to "current"
  - [ ] Start new deployment
    - cd "current"
    - docker-compose up -d
- [ ] On failure
  - [ ] Stop deployment
    - cd "current"
    - docker-compose stop
  - [ ] Symlink "$previous" to "current"
  - [ ] Redeploy
    - cd "current"
    - docker-compose up -d
- [ ] On success
  - [ ] Symlink "$previous" to "previous"
  - [ ] Symlink "$new" to "current"
