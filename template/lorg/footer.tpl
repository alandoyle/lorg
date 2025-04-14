{% if (!empty($footer_message)): %}
	<div style="text-align: center;"><br/><p>{{ $footer_message }}</p></div>
{% endif; %}
    <div class="footer-container">
        <a href="https://github.com/alandoyle/lorg/" target="_blank">Github</a>
{% if ($api_enabled == true): %}
        <a href="./api">API</a>
{% endif; %}
        <span class="box"><a href="#settingsPopup">Settings</a></span>
{% include settings.tpl %}
        <a href="https://lorg.dev" target="_blank">About...</a>
    </div>