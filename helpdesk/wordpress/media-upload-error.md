# WordPress Media Upload Error

**Category**: WordPress / Server  
**Confidence**: 8/10  
**Pattern frequency**: Medium  
**Last seen**: 2026-05-19  

## Symptom

Client reports: "Server error. Upload directory isn't writable" when attempting to upload images or files via the WordPress Media Library or post editor.

## Root cause

The `wp-content/uploads` directory (or a dated subdirectory within it) has incorrect file system permissions. WordPress requires the web server user (typically `www-data` on Linux or the WP Engine service account) to have write access. This is broken by:

- Hosting migrations where file ownership is not re-mapped
- Manual SFTP uploads that set ownership to a different user
- Security hardening scripts that recursively `chmod 755` or `chown root` the `wp-content` tree
- WP Engine environment resets or pushes that don't preserve permissions

## Diagnosis steps

1. Log into WP Engine (or hosting provider) and check file permissions on `wp-content/uploads` via SFTP client or SSH:
   ```bash
   ls -la wp-content/uploads/
   ```
   Expected: `drwxrwxr-x` or `755` with owner matching the web server user.
2. Try creating a test upload to a specific month subdirectory to isolate whether it's the root `uploads` dir or a subdirectory.
3. Check `debug.log` (if `WP_DEBUG_LOG` is enabled) for the exact path WordPress is failing to write to.
4. Confirm the issue is not a disk space problem: `df -h`.

## Fix

1. Via SFTP: right-click `wp-content/uploads` > File Permissions > set to `755`, apply recursively to subdirectories.
2. Via SSH/WP-CLI:
   ```bash
   chmod -R 755 wp-content/uploads
   chown -R www-data:www-data wp-content/uploads
   ```
3. On WP Engine: use the WP Engine User Portal > File Manager or contact WP Engine support to reset directory ownership — they control the server user.
4. Test upload immediately after fix to confirm.

## Prevention

- After any site migration, verify uploads permissions before handing off.
- Include a post-deploy smoke test: attempt a media upload in the staging and production environments.
- Add to the site launch checklist: `uploads` directory write test.

## Agent script

> "We've found the issue — the folder WordPress uses to store uploaded files has lost its write permissions, which is preventing any uploads from going through. This is a quick fix on the server side. We'll have it restored within the hour and confirm back to you once a test upload is successful."

## Chatbot response

> **Can't upload images or files in WordPress?**  
> If you're seeing "Upload directory isn't writable," this is a server permissions issue our team needs to fix directly. Please [submit a support ticket](https://wordie.com.au/support) with your site URL — typical resolution time is under 1 hour.

## Sources

- Wesley Mission ticket (HubSpot) — uploads blocked after WP Engine environment push; confirmed via email chain
