{% if (!empty($footer_message)): %}
	<div style="text-align: center;"><br/><p>{{ $footer_message }}</p></div>
{% endif; %}
    <div class="footer-container">
        <a href="https://github.com/alandoyle/lorg/" target="_blank">Github</a>
{% if ($api_enabled == true): %}
        <a href="./api">API</a>
{% endif; %}
        <span class="box"><a href="#settingsPopup">Settings</a></span>
        <div id="settingsPopup" class="overlay">
            <div class="popup">
                <h2>Settings</h2>
                <a class="close" href="#">&times;</a>
                <div class="content">
                    <div class="settings-container">
                        <form method="post" enctype="multipart/form-data" autocomplete="off" action="./save-settings">
{% if ($hide_templates != true): %}
                            <div>
                                <label for="template">Template:</label>
                                <select name="template">
{% foreach($templates as $template): %}
                                    <option value="{{ $template['name'] }}" {{ $template['selected'] }}>{{ $template['name'] }}</option>
{% endforeach; %}
                                </select>
                            </div>
{% endif; %}
                            <h2>Google settings</h2>
                            <div class="settings-textbox-container">
                                <div>
                                    <span>Site language</span>
                                    <input type="text" name="google_language_site" placeholder="E.g.: en" value="{{ $google_language_site }}"/>
                                </div>
                                <div>
                                    <span>Results language</span>
                                    <input type="text" name="google_language_results" placeholder="E.g.: de" value="{{ $google_language_results }}"/>
                                </div>
                                <div>
                                    <label>Number of results per page</label>
                                    <input type="number" name="google_number_of_results" placeholder="E.g.: 20" value="{{ $google_number_of_results }}"/>
                                </div>
                                <div>
                                    <label>Safe search</label>
                                    <input type="checkbox" name="safe_search"<?php echo isset($_COOKIE["safe_search"]) ? "checked"  : ""; ?> >
                                </div>
                            </div>

                            <h2>Invidious settings</h2>
                            <div class="settings-textbox-container">
                                <div>
                                    <span>Instance URL</span>
                                    <input type="text" name="invidious_url" placeholder="E.g.: https://i.lorg.dev" value="{{ $invidious_url }}"/>
                                </div>
                            </div>
                            <div>
                                <button type="submit" name="save" value="1">Save</button>
                                <button type="submit" name="reset" value="1">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <a href="https://lorg.dev" target="_blank">About...</a>
    </div>