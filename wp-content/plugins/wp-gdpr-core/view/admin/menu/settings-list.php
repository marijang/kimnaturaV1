<div>
    <table>
        <form method="post">
			<?php
			foreach ( $options as $options_name => $option ) {
				switch ( $option['type'] ) {
					case 'checkbox':
						echo '<tr>';
						echo sprintf( '<td><label for="%s">%s</label></td>', $options_name, $option['label'] );
						echo sprintf( '<td><input type="checkbox" %s name="%s" id="gdpr_%s"></td>', $option['value'], $options_name, $options_name );
						echo '</tr>';
						break;
					case 'email':
						echo '<tr>';
						echo sprintf( '<td><label for="%s">%s</label></td>', $options_name, $option['label'] );
						echo sprintf( '<td><input id="gdpr_%s" type="email" name="%s" value="%s"></td>', $options_name, $options_name, $option['value'] );
						echo '</tr>';
						break;
					case 'text':
						echo '<tr class="gdpr_text_row">';
						echo sprintf( '<td><label for="%s">%s</label></td>', $options_name, $option['label'] );
						echo sprintf( '<td><input id="gdpr_%s" type="text" name="%s" value="%s"></td>', $options_name, $options_name, $option['value'] );
						echo '</tr>';
						break;
				}
			}
			?>
            <tfoot>
            <tr>
                <td rowspan="2">
                    <p"><small><a class="dpo_info href="https://edps.europa.eu/data-protection/data-protection/reference-library/data-protection-officer-dpo_en" target="_blank"><b>More info</b></a> about DPO ( Data Protection Officer )</small></p>
                    <input type="submit" class="button button-primary" name="gdpr_save_global_settings"
                           value="<?php _e( 'Update settings', 'wp_gdpr' ); ?>">
                </td>
            </tr>
            </tfoot>
        </form>
    </table>
</div>