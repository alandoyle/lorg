{% extends base.tpl %}

{% block content %}
    <div class="search-container">
        <span class="{{ $sitelogo }}" title="{{ $description }}"></span>
        <p>Example API request: <a href="{{ $baseurl }}/api?q=debian&p=2&t=0" target="_blank">{{ $baseurl }}/api?q=debian&p=2&t=0</a></p>
        <br/>
        <p>"q" is the keyword</p>
        <p>"p" is the result page (the first page is 0)</p>
        <p>"t" is the search type (0=text, 1=image, 2=video)</p>
        <br/>
        <p>The results are going to be in JSON format.</p>
        <p>The API supports both POST and GET requests.</p>
    </div>
{% endblock %}