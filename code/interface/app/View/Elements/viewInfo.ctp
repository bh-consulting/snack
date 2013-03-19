<h1><?php echo $title; ?></h1>
<?php

// Pour le cake extract :
__('Yes');
__('No');

// Don't show the edit/delete button for tech users
// => show only for admin and root users
if(AuthComponent::user('role') !== 'tech'){
    $items = array(
        $this->Html->link(
			'<i class="icon-edit"></i> ' . __('Edit'),
			array('action' => $editAction, $id),
			array('escape' => false)
        ),
    );

    // show delete button only for root users
    if(AuthComponent::user('role') === 'root'){
		$items[]= $this->Html->link(
			'<i class="icon-remove"></i> ' . __('Delete'),
			"#confirm$id",
			array(
			    'escape' => false,
			    'data-toggle' => 'modal'
			)
		);

        echo $this->element('modalDelete', array(
            'id'   => $id,
            'link' => $this->Form->postLink(
                    '<i class="icon-remove icon-white"></i> ' . __('Delete'),
                    array('action' => 'delete', $id),
                    array(
                        'escape' => false,
                        'class' => 'btn btn-primary btn-danger'
                    )
                )
        ));
    }

    echo $this->element('dropdownButton', array(
        'buttonCount' => 2,
        'title' => h($name),
        'icon' => $icon,
        'linkOptions' => array('action' => $editAction, $id),
        'items' => $items,
    ));
}

?>

<dl class="well dl-horizontal">
	<?php
	foreach($attributes as $attr => $value) {
		if( in_array( $attr, $showedAttr ) ) {
			if( is_array( $value ) ) {
				echo '<dt>' . __($attr) . '</dt><dd>';
				if( empty( $value ) ) {
					echo __('Not defined');
				} else {
					foreach($value as $item) {
						echo '<span class="label label-info">' . $item . '</span> ';
					}
				}
				echo '</dd>';
			} else {
				if($attr == 'EAP-Type') {
				    $attr = 'Check server certificate';

				    if($value == 'EAP-TTLS')
					$value = 'Yes (TTLS)';
				    else
					$value = 'No (challenge MD5)';
				}

				if($attr == 'NAS-Port-Type') {
				    $attr = 'NAS Port Type';

				    $values = explode('|', $value);
				    $i18n_values = array();

				    foreach ($values AS $val) {
					switch($val) {
						case 'Async':
						    $i18n_values[] = __('Console');
						    break;
						case 'Virtual':
						    $i18n_values[] = __('Telnet/SSH');
						    break;
						case 'Ethernet':
						    $i18n_values[] = __('802.1x');
						    break;
					}
				    }

				    $value = implode(', ', $i18n_values);
				}

				echo '<dt>' . __($attr) . '</dt>'
					. '<dd>' . ( ( !empty($value) ) ? __($value) : __('Not defined') ) . '</dd>';
			}
		}
	}
	?>
</dl>
