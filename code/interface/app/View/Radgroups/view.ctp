<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1>Group <? echo h($radgroup['Radgroup']['groupname']); ?></h1>

<p><strong><? echo __('Comment:'); ?> </strong>
<? echo $radgroup['Radgroup']['comment']; ?></p>

<? foreach($radgroupchecks as $r){
    if($r['Radgroupcheck']['attribute'] == 'EAP-Type')
        echo '<p><strong>' . __('EAP Type:') . ' </strong> ' . $r['Radgroupcheck']['value'];
    if($r['Radgroupcheck']['attribute'] == 'Simultaneous-Use')
        echo '<p><strong>' . __('Simultaneous use:') . ' </strong> ' . $r['Radgroupcheck']['value'];
    if($r['Radgroupcheck']['attribute'] == 'Expiration')
        echo '<p><strong>' . __('Expiration date:') . ' </strong> ' . $r['Radgroupcheck']['value'];
}
?>
