{% extends base.tpl %}

{% block content %}
    <form class="sub-search-container" method="get" autocomplete="off">
        <span class="logomobile">
            <a class="no-decoration" href="./"><span class="{{ $sitelogo }}" title="{{ $description }}"></span></a>
        </span>
        <div class="search-button-wrapper">
            <div class="sub-searchbox">
                <svg aria-hidden="true" viewBox="0 0 24 24">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input
                    aria-label="Search the Web."
                    autocomplete="off"
                    inputmode="search"
                    placeholder="Search for something..."
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    autofocus
                />
            </div>
        </div>
        <button class="hide" name="t" value="{{ $type }}" />
        <button type="submit" class="hide"></button>
        <input type="hidden" name="p" value="0">
        <div class="sub-search-button-wrapper">
{% foreach($categories as $category): %}
            <a {{ $category['class'] }} href="/search?q={{ $query }}&t={{ $category['type'] }}"><img src="/images/{{ $category['typename'] }}_result.png" alt="{{ $category['description'] }} Result" title="{{ $category['description'] }} Result" />{{ $category['description'] }}</a>
{% endforeach; %}
        </div>
        <hr/>
    </form>
    <p id="time">Fetched the results in {{ $end_time }} seconds using {{ $engine }}.</p>
    <div class="text-result-container">
        VIDEOS HERE
{% foreach($results as $item): %}

{% endforeach; %}
    </div>
    <div class="next-page-button-wrapper">
{% if ($pagenum == 0): %}
        <a class="next-page-only-button" href="/search?q={{ $query }}&t={{ $type }}&p={{ $pagenum + 10 }}" aria-label="Next page">Next &gt;</a>
{% endif; %}
{% if ($pagenum != 0): %}
        <div class="next-page-button-container">
            <a class="prev-page-button" href="/search?q={{ $query }}&t={{ $type }}&p={{ $pagenum - 10 }}" style="text-align:right" aria-label="Previous page">
                <span class="no-highlight">&lt;</span>
            </a>
            <span class="page-number">Page 2</span>
            <a class="next-page-button" href="/search?q={{ $query }}&t={{ $type }}&p={{ $pagenum + 10 }}" style="text-align:left" aria-label="Next page">
                <span class="no-highlight">&gt;</span>
            </a>
        </div>
{% endif; %}
    </div>
{% endblock %}