<? 
$this->extend('/Common/radius_sidebar');
$this->assign('groups_active', 'active');
?>
<h1>Group <? echo h($radgroup['Radgroup']['groupname']); ?></h1>

<p><strong>Comment: </strong>
<? echo $radgroup['Radgroup']['comment']; ?></p>

<? foreach($radgroupchecks as $r){
    if($r['Radgroupcheck']['attribute'] == 'EAP-Type')
        echo '<p><strong>EAP Type: </strong> ' . $r['Radgroupcheck']['value'];
    if($r['Radgroupcheck']['attribute'] == 'Simultaneous-Use')
        echo '<p><strong>Simultaneous use: </strong> ' . $r['Radgroupcheck']['value'];
    if($r['Radgroupcheck']['attribute'] == 'Expiration')
        echo '<p><strong>Expiration date: </strong> ' . $r['Radgroupcheck']['value'];
}
?>
