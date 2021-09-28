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
- s3fs (for mounting S3 bucket or DO space to filesystem, for configuration retrieval)
  - See https://jianjye.medium.com/how-to-mount-digitalocean-spaces-to-droplets-on-ubuntu-dcba4cc16c78
  - Essentially:
    - Create `/etc/passwd-s3fs` with the key:token as a single line; make it 0600 owned by root
    - Add an entry to `/etc/fstab`:
      ```text
      space:/path /mnt/site-config fuse.s3fs _netdev,ro,user,umask=022,allow_other,use_cache=/tmp,url=https://nyc3.digitaloceanspaces.com,use_path_request_style 0 0
      ```
    - `sudo mount /mnt/site-config`
    On my first try, I needed to mount just the space, and then do a chmod operation on the subpath I was using before s3fs would mount the subpath.

### Deployment

- [x] Resolve "current" symlink to concrete directory and store value as "$previous"
- [x] Store repository sha1 for deployment as "$new"
- [x] Prepare new deployment
  - [x] Push or pull the repo at "$new"
    - In new directory based on "$new"
  - [x] Retrieve env
    - Resolve env version from value in repo
    - Fetch to `.env` file in new directory
  - [x] Build: `docker-compose build`
- [x] Deploy
  - [x] Stop current deployment
    - cd "$previous"
    - docker-compose down
  - [x] Start new deployment
    - cd "$new"
    - docker-compose up -d
- [x] On failure
  - [x] Stop deployment
    - cd "current"
    - docker-compose stop
  - [x] Redeploy
    - cd "current"
    - docker-compose up -d
- [x] On success
  - [x] Symlink "$previous" to "previous"
  - [x] Symlink "$new" to "current"
