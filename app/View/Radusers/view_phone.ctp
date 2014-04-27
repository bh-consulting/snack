<?php

$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('users_active', 'active');

if (isset($attributes['EAP-Type']) && $attributes['EAP-Type'] == 'EAP-TTLS'
) {
    $attributes['Server certificate path'] = $this->Html->link(
            __($attributes['Server certificate path']), array(
        'action' => 'get_cert/server',
        'controller' => 'certs',
            )
    );
    $attributes['Server certificate cer path'] = $this->Html->link(
            $attributes['Server certificate cer path'], array(
        'action' => 'get_cert/servercer',
        'controller' => 'certs',
            )
    );
} else if (array_search('Server certificate path', $showedAttr)) {
    unset($showedAttr[array_search('Server certificate path', $showedAttr)]);
}

echo $this->element(
        'viewInfo', array(
    'title' => __('Cisco Phone'),
    'glyphicon glyphicon' => 'glyphicon glyphicon-user',
    'name' => $raduser['Raduser']['username'],
    'id' => $raduser['Raduser']['id'],
    'editAction' => 'edit_' . $raduser['Raduser']['type'],
    'attributes' => $attributes,
    'showedAttr' => $showedAttr,
        )
);
?>
