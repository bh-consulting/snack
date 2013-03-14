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

echo $this->element('block-dl', array(
    'title' => __('Statistics:'),
    'fields' => array(
        __('Session start') => $radacct['Radacct']['acctstarttime'],
        __('Session stop') => $radacct['Radacct']['acctstoptime'],
        __('Session time') => Utils::secondToTime($radacct['Radacct']['acctsessiontime']),
        __('Terminate cause') => $radacct['Radacct']['acctterminatecause'],
        __('Input data') => Utils::octets($radacct['Radacct']['acctinputoctets']),
        __('Output data') => Utils::octets($radacct['Radacct']['acctoutputoctets']),
    ),
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
        __('Port') => $radacct['Radacct']['nasporttype'] . $portid
    ),
));
?>
