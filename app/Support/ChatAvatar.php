<?php

namespace App\Support;

/**
 * Resolves a chat participant's avatar to a usable URL.
 *
 * The three sender tables store the `image` column in three incompatible shapes,
 * which is why a naive `asset($image)` renders a broken avatar for most senders:
 *
 *   users     → bare filename, living in public/assets/media/avatars
 *               (see Admin\ProfileController, which moves uploads there)
 *   teachers  → a fully-qualified absolute URL, e.g. http://host/uploads/image/x.png
 *   students  → a public-relative path (or null)
 *
 * Every chat surface — the admin monitor, the student/teacher bubble, and the
 * Pusher payload that feeds live messages — goes through here, so an avatar
 * resolves identically no matter which path renders it.
 */
class ChatAvatar
{
    /** Directories a bare filename may live in, tried in order. */
    private const LOOKUP_DIRS = [
        'assets/media/avatars',
        'uploads/image',
        'uploads/images',
    ];

    public static function default(): string
    {
        return asset('assets/oxford/images/user-avatar.png');
    }

    /**
     * @param  string|null $image     raw value of the sender's `image` column
     * @param  int|null    $userType  0 = student, 1 = teacher, 2 = admin
     */
    public static function url($image, $userType = null): string
    {
        $image = is_string($image) ? trim($image) : '';
        if ($image === '') {
            return self::default();
        }

        // Teachers store an absolute URL. Keep the path but re-point it at the
        // current host, so a row written on 127.0.0.1:8000 still resolves in
        // production instead of pointing at a dev machine nobody can reach.
        if (preg_match('#^https?://#i', $image)) {
            $path = ltrim((string) parse_url($image, PHP_URL_PATH), '/');
            if ($path !== '' && file_exists(public_path($path))) {
                return asset($path);
            }
            return $path !== '' ? asset($path) : self::default();
        }

        $relative = ltrim($image, '/');

        // Already a public-relative path.
        if (str_contains($relative, '/') && file_exists(public_path($relative))) {
            return asset($relative);
        }

        // Bare filename — admins first when we know the sender is one.
        $dirs = self::LOOKUP_DIRS;
        if ((int) $userType === 2) {
            array_unshift($dirs, 'assets/media/avatars');
        }
        foreach (array_unique($dirs) as $dir) {
            if (file_exists(public_path($dir . '/' . $relative))) {
                return asset($dir . '/' . $relative);
            }
        }

        // A path we cannot verify on disk (e.g. offloaded storage) is still worth
        // emitting when it looks like a path; a stray filename is not.
        return str_contains($relative, '/') ? asset($relative) : self::default();
    }

    /**
     * Initial used by Metronic's letter-avatar fallback when there is no image.
     */
    public static function initial($name): string
    {
        $name = trim((string) $name);
        return $name === '' ? '؟' : mb_substr($name, 0, 1);
    }
}
