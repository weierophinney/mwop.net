# TODO

## Error handling

Test a prototype for new Stratigility/Expressive error handling.

### 404 middleware

Add middleware between routing and dispatching that, if no route result is
present or it indicates a route miss, displays and returns a response using the
404 template

### Error middleware

See if you can write a middleware wrapper for the top of the stack/outermost
layer that does the following:

- catches all Throwables from calling $next(); anything caught results in error
  template rendering.
- in debug mode, delegates to whoops for the rendering.

    ### pass-through final handler

When done with the above, create a pass-through final handler that does nothing
but return the response provided to it.

### re-do error middleware

- Unauthorized could become a response type instead

## Docker for deployment

- Setup containers for:
  - shared volume
  - php-fpm
  - nginx
- Identify how to expose this via rancher or similar.
- Determine how to add secret files, such as SSL config, for the nginx
  configuration during deployment.

