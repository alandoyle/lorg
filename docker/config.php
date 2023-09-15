<?php

return [
    "base_url" => "https://search.lorg.dev", // This doesn't technically need to be set but doesn't hurt :)

    "google_domain"            => "com",
    "google_language_site"     => "en",
    "google_language_results"  => "en",
    "google_number_of_results" => "20",

    "opensearch_title"         => "lorg",
    "opensearch_description"   => "lorg is an API driven metasearch engine that respects your privacy.",
    "opensearch_encoding"      => "UTF-8",
    "opensearch_long_name"     => "lorg Metasearch Engine",

    'accept_langauge'          => 'en-GB',

    'template'                 => 'lorg',
    'use_client_ua'            => false,
    'use_specific_ua'          => '',
    'link_google_image'        => false,
    'use_image_proxy'          => true,
    'minify_output'            => true,
    'include_local_instance'   => true,

    "wikipedia_language"       => "en",

    "use_qwant_for_images"     => false,
    "use_invidious_for_videos" => false,
    "invidious_url" => "https://y.com.sb",
];