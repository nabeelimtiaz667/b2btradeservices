<?php

if (! function_exists('validate_uploaded_image')) {
    /**
     * Validates an uploaded image by its actual content, not just its
     * client-supplied name/extension -- see BLOCKERS #28. Before this,
     * every image upload site called only isValid()/hasMoved() and trusted
     * getRandomName()'s extension, which comes from Mimes::guessExtensionFromType()
     * driven by finfo's detected MIME. finfo happily reports a `<?php ?>`
     * file as text/x-php, and Mimes.php maps that back to `.php` -- so an
     * uploaded "image" could land in the web-served uploads/ folder as an
     * executable PHP script with zero checks in between.
     *
     * This checks the real, finfo-detected MIME type (UploadedFile::getMimeType(),
     * which reads the file's actual bytes -- never the client's Content-Type
     * header) against an allowlist, and additionally confirms getimagesize()
     * can decode it as a real image. Both checks use the file's content, not
     * anything the uploader controls.
     *
     * Returns null when the file is a valid image safe to move into a
     * web-served directory, or a user-facing rejection message otherwise.
     */
    function validate_uploaded_image(\CodeIgniter\HTTP\Files\UploadedFile $file, int $maxSizeKB = 5120): ?string
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $detectedMime = $file->getMimeType();
        if (!in_array($detectedMime, $allowedMimes, true)) {
            return 'Only JPG, PNG, GIF and WEBP images are allowed.';
        }

        // Belt-and-suspenders: confirm it actually decodes as an image, not
        // just a file whose first bytes happen to match a magic number that
        // fooled finfo (rare, but this check is nearly free).
        if (@getimagesize($file->getTempName()) === false) {
            return 'The uploaded file is not a valid image.';
        }

        if ($file->getSizeByUnit('kb') > $maxSizeKB) {
            return 'Image is too large (max ' . number_format($maxSizeKB / 1024, 1) . ' MB).';
        }

        return null;
    }
}
