    <div class="footer-container">
        <a href="https://github.com/alandoyle/lorg/" target="_blank">Github</a>
        <a href="./settings">Settings</a>
<!-- @@@ NOT IMPLEMENTED... YET.
        <a href="./api">API</a>
-->
{% if (!empty($contact_email)): %}
        <span class="box">
	        <a href="#contactPopup">Request API Access</a>
        </span>
        <div id="contactPopup" class="overlay">
                <div class="popup">
                        <h2>API Request</h2>
                        <a class="close" href="#">&times;</a>
                        <div class="content">
                                Please email the Site Admin at<br/><br/><strong>{{ $contact_email }}</strong><br/><br/>to request API access.
                        </div>
                </div>
        </div>
{% endif; %}
    <a href="https://lorg.dev" target="_blank">About...</a>
</div>