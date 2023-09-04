<!DOCTYPE html >
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta charset="UTF-8"/>
        <meta name="description" content="{{ $description }}"/>
        <meta name="referrer" content="no-referrer"/>
        <meta name="copyright"content="(c) Alan Doyle [me@alandoyle.com]"/>
        <meta name="engine"content="lorg Metasearch Engine [https://github.com/alandoyle/lorg/]"/>
        <meta name="ua"content="{{ $ua }}"/>
        <meta name="search_url"content="{{ $search_url }}"/>
        <meta name="base_url"content="{{ $baseurl }}"/>
        <meta name="apiurl"content="{{ $apiurl }}"/>
        <meta name="result_count"content="{{ $result_count }}"/>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
        <meta http-equiv="Pragma" content="no-cache"/>
        <meta http-equiv="Expires" content="0"/>
{% if (!empty($githash)): %}
        <meta name="git-commit" content="{{ $githash }}"/>
        <meta name="git-url" content="{{ $giturl }}"/>
{% endif; %}
        <link title="{{ $title }}" type="application/opensearchdescription+xml" href="opensearch.xml?method=POST" rel="search"/>
        <link rel="stylesheet" type="text/css" href="css/styles.css"/>
        <link rel="stylesheet" type="text/css" href="css/lorg.css"/>
        <link rel="stylesheet" type="text/css" href="custom/custom.css"/>
        <title>{{ $title }}</title>
    </head>
    <body>
{% yield content %}
        <div class="footer-container">
            <a href="/">{{ $title }}</a>
            <a href="https://github.com/alandoyle/lorg/" target="_blank">Github</a>
            <a href="./settings">Settings</a>
            <a href="./api">API</a>
            <a href="https://lorg.dev" target="_blank">About...</a>
        </div>
    </body>
</html>