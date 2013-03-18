<?php
$this->extend('/Common/radius_sidebar');
$this->assign('radius_active', 'active');
$this->assign('session_active', 'active');
?>
<h2>
<?php
echo __('Session of %s', h($radacct['Radacct']['username']));
?>
</h2>

<?php
echo $this->element('block-dl', array(
    'title' => __('User:'),
    'fields' => array(
        __('Username') => $radacct['Radacct']['username'],
        __('Groupname') => $radacct['Radacct']['groupname'],
        __('Station') => $radacct['Radacct']['callingstationid'],
        __('Session id') => $radacct['Radacct']['acctuniqueid'],
    ),
));

$fields = array();

if ($radacct['Radacct']['durationsec'] != -1) {
    $duration = '<span title="' . __('User still connected') . '">'
	. '<i class="icon-time"></i> '
	. '<span'
	. " -data-duration='{$radacct['Radacct']['durationsec']}'"
	. " -data-duration-format='" . __('y,m,d,h,min,s') . "'>"
	. $radacct['Radacct']['duration']
	. '</span></span>';
} else {
    $duration = $radacct['Radacct']['duration'];

    $fields = array(
        __('Session stop') => $radacct['Radacct']['acctstoptime'],
    );
}

$fields = array_merge(
    array(
        __('Session start') => $radacct['Radacct']['acctstarttime'],
    ),
    $fields,
    array(
        __('Duration') => $duration,
        __('Terminate cause') => $radacct['Radacct']['acctterminatecause'],
        __('Input data') => Utils::octets($radacct['Radacct']['acctinputoctets']),
        __('Output data') => Utils::octets($radacct['Radacct']['acctoutputoctets']),
    )
);

echo $this->element('block-dl', array(
    'title' => __('Statistics:'),
    'fields' => $fields,
));

if (!empty($radacct['Radacct']['nasportid'])) {
    $portid = " (" .  $radacct['Radacct']['nasportid'] . ")";
} else {
    $portid = '';
}

echo $this->element('block-dl', array(
    'title' => __('Network Access Server:'),
    'fields' => array(
        __('IP address') => $radacct['Radacct']['nasipaddress'],
        __('Port') => (isset($types[$radacct['Radacct']['nasporttype']]) ?
		    $types[$radacct['Radacct']['nasporttype']] :
		    $radacct['Radacct']['nasporttype']) . $portid
    ),
));

$this->start('script');
echo $this->Html->script('radaccts');
$this->end();
?>
