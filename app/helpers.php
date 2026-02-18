<?php

if (! function_exists('storage_url')) {
    /**
     * Generate URL for storage files (profile pics, e-signatures).
     * Uses /media/ route to avoid conflict with physical storage directory.
     */
    function storage_url(?string $path): string
    {
        if (empty($path) || ! is_string($path)) {
            return '';
        }

        $path = preg_replace('#^storage/#', '', trim($path));

        return $path ? url('media/'.$path) : '';
    }
}
