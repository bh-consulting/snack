<?php

echo '<fieldset>';
echo '<legend>' . __('Snack rights') . '</legend>';
echo $this->Form->input('role', array(
    // 'type' => 'radio',
    'options' => array(
        'user' => __('User: access to the network, not to the interface'),
        'tech' => __('Tech: view users, download certificates'),
        'admin' => __('Admin: view, create, update users'),
        'superadmin' => __('Super admin: view, create, update, delete all objects'),
    ),
    'legend' => false,
    'label' => __('Role')
));
echo '</fieldset>';

?>