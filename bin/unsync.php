<?php

if (!in_array($args[0], [2359, 2360, 2361, 2362, 2363]) && false !== get_post_status($args[0])) {
    $terms = wp_get_object_terms($args[0], 'post_translations');
    if (isset($terms[0])) {
        $term = $terms[0];
        $translations = maybe_unserialize($term->description);
        if (isset($translations['sync'])) {
            $sync = $translations['sync'];

            // Unsync all languages.
            $translations['sync'] = [];
            wp_update_term($term->term_id, 'post_translations', ['description' => serialize([])]);
            echo "Removed synchronization for $args[0].\n";

            // Delete all translations.
            foreach ($sync as $connection => $base) {
                if ($connection !== $base) {
                    wp_delete_post($translations[$connection]);
                }
            }

            echo "Deleted synchronized resources for $args[0].\n";
        }
    }
    echo "Processed $args[0].\n";
}
