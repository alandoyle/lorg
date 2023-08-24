<!DOCTYPE html >
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta charset="UTF-8"/>
        <link title="{{ $title }}" type="application/opensearchdescription+xml" href="opensearch.xml?method=POST" rel="search"/>
        <meta name="description" content="{{ $description }}"/>
        <meta name="referrer" content="no-referrer"/>
        <link rel="stylesheet" type="text/css" href="css/styles.css"/>
        <link rel="stylesheet" type="text/css" href="css/lorg.css"/>
        <link rel="stylesheet" type="text/css" href="custom/custom.css"/>
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        <title>{{ $title }}</title>
    </head>
    <body>{% yield content %}        <div class="footer-container">
            <a href="/">{{ $title }}</a>
            <a href="https://github.com/alandoyle/lorg/" target="_blank">Github</a>
            <a href="./settings">Settings</a>
            <a href="./api">API</a>
            <a href="https://lorg.dev" target="_blank">About...</a>
        </div>
        <div class="git-container">{{ $githash }}</div>
    </body>
</html>