# Public Upload Storage

All admin and form uploads use `PUBLIC_UPLOADS_DISK`. Existing views resolve files through `App\Support\PublicStorage`, so switching storage does not require changing every image tag.

## VPS or server with SSH

Use Laravel's public disk:

```env
PUBLIC_UPLOADS_DISK=public
```

Run once after deploy:

```bash
php artisan storage:link
php artisan config:clear
```

Files are stored in `storage/app/public` and served from `/storage`.

## Shared hosting with no terminal or SSH

Use the cPanel-safe disk:

```env
PUBLIC_UPLOADS_DISK=cpanel_public
```

Create this folder manually in the hosting file manager:

```text
public/uploads
```

Files are stored directly under `public/uploads`, so no `php artisan storage:link` command is required.

## Cloudinary image/CDN storage

Use Cloudinary when local storage is unreliable or you want images served from an external CDN:

```env
PUBLIC_UPLOADS_DISK=cloudinary
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
CLOUDINARY_FOLDER=kimmex
CLOUDINARY_RESOURCE_TYPE=auto
CLOUDINARY_UPLOAD_RESOURCE_TYPE=auto
```

After changing `.env`, clear config cache if terminal access exists:

```bash
php artisan config:clear
```

If shared hosting has no terminal, edit `.env` and upload the project with a fresh `bootstrap/cache` folder that does not contain old cached config files.

## R2, S3, or another external object store

Use the existing `r2` or `s3` disks:

```env
PUBLIC_UPLOADS_DISK=r2
```

Set the matching access key, secret, bucket, endpoint, and URL values in `.env`.
