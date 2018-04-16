<div class="wrap">
    <h2><b><?php _e( 'Dataregister', 'wp_gdpr' ); ?></b> <?php _e( 'records of processing activities', 'wp_gdpr' ); ?></h2>
    <div id="nav_menu">
        <a id="a_addons" href="" class="active_tab"><span class="dashicons dashicons-trash"></span>&nbsp;Complete log file</a>
        <a id="a_settings" href="<?php echo admin_url( 'admin.php?page=settings_wp-gdpr' ) ?>"><span class="dashicons dashicons-admin-generic"></span>&nbsp;Settings</a>
        <a id="a_help" href="<?php echo admin_url( 'admin.php?page=help' ) ?>"><span class="dashicons dashicons-editor-help"></span> Help</a>
        <a id="a_addons" href="<?php echo admin_url( 'admin.php?page=addon' ) ?>"><span class="dashicons dashicons-screenoptions"></span>Available addons</a>
    </div>
    <div id="nav_menu_extra">
        <a id="a_review" target="_blank" href="https://wordpress.org/support/plugin/wp-gdpr-core/reviews/#new-post"><span class="dashicons dashicons-admin-comments"></span>&nbsp;Review
            our plugin</a>
        <a id="a_homepage" target="_blank" href="https://wp-gdpr.eu/"><span class="dashicons dashicons-admin-home"></span>&nbsp;Visit our homepage</a>
    </div>
    <div id="user_info" class="user_info">
        <div class="user_info_header">
            <h3>What is dataregister?</h3>
            <button id="usr_info_header_btn">dismiss</button>
        </div>
        <br>
        <div class="user_info_content">
            <img class="a_info" src="<?php echo GDPR_URL . 'assets/images/icon-info-bg.png'; ?>">
            <p style="width: 80%;">Article 30 of the GDPR law staates it is manadatory to keep records of processing activity.
                The plugin logs all actions taken regarding personal data on the website and administrators can download a complete logfile here.
                If you still are not sure what this is, check out our <a href="<?php echo admin_url( 'admin.php?page=help' ) ?>"><b>Help page</b></a> or our <a href="https://wp-gdpr.eu/tutorials/" target="_blank"><b>online tutorials</b></a>.</p>
        </div>
    </div>
    <div id="tab_box">
        <table id="dataregister_table">
            <thead>
            <tr>
                <th>test</th>
                <th>test</th>
                <th>test</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>seba</td>
                <td>teba</td>
                <td>test</td>
            </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td id="dataregi_dwnl_btn" colspan="3"><button class="button button-primary">Download logfile</button></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php $plugin_data = get_plugin_data(GDPR_DIR .'wp-gdpr-core.php' );$plugin_version = $plugin_data['Version']; ?>
<p class="appsaloon_footer">WP-GDPR <?php echo $plugin_version; ?> developed by <a href="https://appsaloon.be/" target="_blank"><b>Appsaloon</b></a></p>