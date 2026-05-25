# Deployment Guide

This project is prepared for Docker-based hosting on both Railway and Render.

## Requirements

- PHP 8.4 or newer in the container
- A database service such as MySQL or PostgreSQL
- A valid `APP_KEY`
- Writable `storage/` and `bootstrap/cache/`

## Railway

1. Push the repository to GitHub.
2. In Railway, create a new project and deploy from the GitHub repo.
3. Railway will detect the `Dockerfile` automatically.
4. Add environment variables:
   - `APP_NAME`
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL`
   - `APP_KEY`
   - `DB_CONNECTION`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`
5. Connect a database plugin or external database.
6. Run migrations after deploy:

```bash
php artisan migrate --force
```

## Render

1. Push the repository to GitHub.
2. In Render, create a new Web Service and connect the repo.
3. Choose Docker as the runtime or let Render detect the `Dockerfile`.
4. Set the same environment variables listed above.
5. Use a database service or external managed database.
6. Set the health check path to `/student-courses`.
7. Run migrations after the first deploy.

## Local Docker Test

```bash
docker build -t cmsmbproject1 .
docker run --rm -p 10000:10000 --env PORT=10000 cmsmbproject1
```

Then open:

```text
http://localhost:10000/student-courses
```
