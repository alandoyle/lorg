{% extends base.tpl %}
{% block content %}
    <div class="api-container">
        <a class="no-decoration" href="./" rel="nofollow"><span class="site-logo" title="{{ $description }}"></span></a>
        <p>Example API request: <a href="{{ $baseurl }}/api?q=debian&p=2&t=0" target="_blank">{{ $baseurl }}/api?q=debian&p=2&t=0</a></p>
        <br/>
        <p>"q" is the keyword</p>
        <p>"p" is the result page (the first page is 0)</p>
        <p>"t" is the search type (0=text, 1=image, 2=video)</p>
        <br/>
        <p>The results are going to be in JSON format.</p>
        <br/>
{% if (empty($contact_email)): %}
        <h3>This is a Private instance.</h3>
{% endif; %}
{% if (!empty($contact_email)): %}
        <h3>Please request the API key from the link below.</h3>
{% endif; %}
    </div>
{% include footer.tpl %}
{% endblock %}