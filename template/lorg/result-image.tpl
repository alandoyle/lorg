{% extends base.tpl %}
{% block content %}
    <form class="sub-search-container" method="get" autocomplete="off">
        <span class="logomobile">
            <a class="no-decoration" href="./" rel="nofollow"><span class="site-logo-search" title="{{ $description }}"></span></a>
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
            <a {{ $category['class'] }} href="./search?q={{ $query_encoded }}&t={{ $category['type'] }}" rel="nofollow"><img src="./template/{{ $template }}/images/{{ $category['typename'] }}_result.png" alt="{{ $category['description'] }} Result" title="{{ $category['description'] }} Result" />{{ $category['description'] }}</a>
{% endforeach; %}
        </div>
        <hr/>
    </form>
    <div class="time-result-container">
        <div class="text-result-wrapper"><span id="time">Fetched {{ $result_count }} result{{ ($result_count == 1) ? "" : "s" }} in {{ $end_time }} seconds ({{ $engine_name }}).</span></div>
    </div>
    <div class="image-result-container">
{% foreach($results as $item): %}
            <a title="{{ $item['title'] }}" href="{{ $item['url'] }}" target="_blank" rel="nofollow">
            <img src="{{ $item['thumbnail'] }}">
            </a>
{% endforeach; %}
{% if ($result_count == 0): %}
        <br/><h1>No more results.</h1>
{% endif; %}
    </div>
    <div class="next-page-button-wrapper">
{% if ($pagenum == 0): %}
        <a class="next-page-only-button" href="./search?q={{ $query_encoded }}&t={{ $type }}&p={{ $pagenum + 1 }}" aria-label="Next page" rel="nofollow">Next &gt;</a>
{% endif; %}
{% if ($pagenum > 0 && $result_count > 0): %}
        <div class="next-page-button-container">
            <a class="prev-page-button" href="./search?q={{ $query_encoded }}&t={{ $type }}&p={{ $pagenum - 1 }}" style="text-align:right" aria-label="Previous page" rel="nofollow">
                <span class="no-highlight">&lt;</span>
            </a>
            <span class="page-number">Page {{ $pagenum + 1 }}</span>
            <a class="next-page-button" href="./search?q={{ $query_encoded }}&t={{ $type }}&p={{ $pagenum + 1 }}" style="text-align:left" aria-label="Next page" rel="nofollow">
                <span class="no-highlight">&gt;</span>
            </a>
        </div>
{% endif; %}
{% if ($pagenum > 0 && $result_count == 0): %}
        <a class="next-page-only-button" href="./search?q={{ $query_encoded }}&t={{ $type }}&p={{ $pagenum - 1 }}" aria-label="Previous page" rel="nofollow">&lt; Previous</a>
{% endif; %}
    </div>
{% include footer.tpl %}
{% endblock %}