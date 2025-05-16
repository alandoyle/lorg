        <div id="settingsPopup" class="overlay">
            <div class="popup">
                <h2>Settings</h2>
                <a class="close" href="#">&times;</a>
                <div class="content">
                    <div class="settings-container">
                        <form method="post" enctype="multipart/form-data" autocomplete="off" action="./save-settings">
                            <div class="settings-textbox-container">
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
                                <div>
                                    <label>Safe search</label>
                                    <input type="checkbox" name="safe_search" <?php echo ($_COOKIE["safe_search"] === 'on') ? "checked"  : ""; ?> >
                                </div>
                                <div>
                                    <span>Invidious URL</span>
                                    <input type="text" name="invidious_url" placeholder="E.g.: https://y.com.sb" value="{{ $invidious_url }}"/>
                                </div>
                            </div>

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
                                    <label>Use Google Image Search</label>
                                    <input type="checkbox" name="google_image_search" <?php echo ($_COOKIE["google_image_search"] === 'on') ? "checked"  : ""; ?> >
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