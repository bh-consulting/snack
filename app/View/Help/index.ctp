<?php
$this->extend('/Common/radius_sidebar'); 
$this->assign('radius_active', 'active');
$this->assign('help_active', 'active');
Configure::load('parameters');
$ipAddress=Configure::read('Parameters.ipAddress');
?>

<h1><?php echo __('Help'); ?></h1>

<h4><?php echo __('General information:');?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('NAS Configuration'); ?></dt>
    <br>
    <pre>
aaa group server radius RadiusServers
server <?php echo $ipAddress;?> auth-port 1812 acct-port 1813
!
aaa authentication login default group RadiusServers local
aaa authentication dot1x default group RadiusServers local
aaa authorization exec default group RadiusServers local
aaa authorization network default group RadiusServers local
aaa accounting dot1x default start-stop group RadiusServers
aaa accounting exec default start-stop group RadiusServers
aaa accounting system default start-stop group RadiusServers
!
dot1x system-auth-control
!
logging <?php echo $ipAddress;?> 
logging source-interface Vlan 1
snmp-server community private RW
snmp-server enable traps snmp coldstart
snmp-server enable traps config
snmp-server host <?php echo $ipAddress;?> version 2c private
snmp-server trap-source Vlan 1
radius-server host <?php echo $ipAddress;?> auth-port 1812 acct-port 1813 key 0 sharedkey
    </pre>
    
    <dt><?php echo __('Interface Conf'); ?></dt>
    <br>
    <pre>
authentication port-control auto
    </pre>
</dl>

