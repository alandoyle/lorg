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
            <a {{ $category['class'] }} href="./search?q={{ $query }}&t={{ $category['type'] }}" rel="nofollow"><img src="./images/{{ $category['typename'] }}_result.png" alt="{{ $category['description'] }} Result" title="{{ $category['description'] }} Result" />{{ $category['description'] }}</a>
{% endforeach; %}
        </div>
        <hr/>
    </form>
    <div class="text-result-container">
{% if (!empty($special['response'])): %}
        <div class="text-result-wrapper">
            <div class="special-row">
{% if ($special['image_url']): %}
                <div class="special-column"><img class="special-round-image-corners" style="max-height: 500px;" src="{{ $special['image_url'] }}"></div>
{% endif; %}
                <div class="special-alt-column">
{% if ($special['sourcename']): %}
                    <a href="{{ $special['source'] }}" target="_blank" rel="nofollow"><h2>{{ $special['sourcename'] }}</h2></a>
{% endif; %}
                    <hr/><span>{{ str_replace('\n', '<br/>', $special['response']) }}</span>
                </div>
{% if ($special['source']): %}
                <br/><span style="float: right;">Source: <a href="{{ $special['source'] }}" target="_blank" rel="nofollow"><strong>{{ $special['source'] }}</strong></a></span>
{% endif; %}
            </div>
        </div>
        <hr/>
{% endif; %}
{% foreach($results as $item): %}
        <div class="text-result-wrapper">
            <a href="{{ $item['url'] }}" target="_blank" rel="nofollow">
                <div class="results-row">
                    <div class="results-column"><img src="{{ $item['image'] }}" /></div>
                    <div class="results-column"><strong>{{ $item['sitename'] }}</strong><br/>{{ $item['base_url'] }}</div>
                </div>
                <h2>{{ $item['title'] }}</h2>
            </a>
            <span>{{ $item['description'] }}</span>
        </div>
{% endforeach; %}
{% if ($result_count == 0): %}
        <h1>No more results.</h1>
{% endif; %}
        <div class="text-result-wrapper"><hr/><span id="time">Fetched the results in {{ $end_time }} seconds using {{ $engine }}.</span></div>
    </div>
    <div class="next-page-button-wrapper">
{% if ($pagenum == 0): %}
        <a class="next-page-only-button" href="./search?q={{ $query }}&t={{ $type }}&p={{ $pagenum + 1 }}" aria-label="Next page" rel="nofollow">Next &gt;</a>
{% endif; %}
{% if ($pagenum > 0 && $result_count > 0): %}
        <div class="next-page-button-container">
            <a class="prev-page-button" href="./search?q={{ $query }}&t={{ $type }}&p={{ $pagenum - 1 }}" style="text-align:right" aria-label="Previous page" rel="nofollow">
                <span class="no-highlight">&lt;</span>
            </a>
            <span class="page-number">Page {{ $pagenum + 1 }}</span>
            <a class="next-page-button" href="./search?q={{ $query }}&t={{ $type }}&p={{ $pagenum + 1 }}" style="text-align:left" aria-label="Next page" rel="nofollow">
                <span class="no-highlight">&gt;</span>
            </a>
        </div>
{% endif; %}
{% if ($pagenum > 0 && $result_count == 0): %}
        <a class="next-page-only-button" href="./search?q={{ $query }}&t={{ $type }}&p={{ $pagenum - 1 }}" aria-label="Previous page" rel="nofollow">&lt; Previous</a>
{% endif; %}
    </div>
{% include footer.tpl %}
{% endblock %}