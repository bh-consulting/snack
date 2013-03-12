<?php

echo '<fieldset>';
echo '<legend>' . __('Snack rights') . '</legend>';
echo $this->Form->input('role', array(
    'label' => __('Role'),
    'class' => 'slidermin',
));

echo '<dl class="well dl-horizontal">';
echo '<dt>User</dt><dd>' . __('access to the network, not to the interface') . '</dd>';
echo '<dt>Tech</dt><dd>' . __('view users, download certificates') . '</dd>';
echo '<dt>Admin</dt><dd>' . __('view, create, update users') . '</dd>';
echo '<dt>Super admin</dt><dd>' . __('view, create, update, delete all objects') . '</dd>';
echo '</dl>';
echo '</fieldset>';

?>