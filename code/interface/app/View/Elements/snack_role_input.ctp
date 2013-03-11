<?php

echo '<fieldset>';
echo '<legend>' . __('Snack rights') . '</legend>';
echo $this->Form->input('role', array(
    'label' => __('Role'),
    'class' => 'slidermin',
));

echo '<table>';
echo '<tr><td>User</td><td>' . __('access to the network, not to the interface') . '</td>';
echo '<tr><td>Tech</td><td>' . __('view users, download certificates') . '</td>';
echo '<tr><td>Admin</td><td>' . __('view, create, update users') . '</td>';
echo '<tr><td>Super admin</td><td>' . __('view, create, update, delete all objects') . '</td>';
echo '</table>';
echo '</fieldset>';

?>