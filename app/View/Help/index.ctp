<?php
$this->extend('/Common/help_tabs');
$this->assign('help_nasconfig_active', 'active');
$ipAddress=Configure::read('Parameters.ipAddress');
?>

<h4><?php echo __('General information:');?></h4>
<dl class="well dl-horizontal">
    <dt><?php echo __('IOS < 15.x'); ?></dt><br/>
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
snmp-server community public RO
snmp-server host <?php echo $ipAddress;?> version 2c public
radius-server host <?php echo $ipAddress;?> auth-port 1812 acct-port 1813 key 0 sharedkey
    </pre>
    
    <dt><?php echo __('IOS > 15.x'); ?></dt><br/>
    <pre>
aaa group server radius RadiusServers
    server snack
    ip radius source-interface Vlan1
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
authentication mac-move permit
!
logging <?php echo $ipAddress;?> 
logging source-interface Vlan 1
snmp-server community public RO
snmp-server host <?php echo $ipAddress;?> version 2c public
radius-server vsa send cisco-nas-port
radius server snack
    address ipv4 <?php echo $ipAddress;?> auth-port 1812 acct-port 1813 
    key 0 sharedkey
    </pre>
    
    <dt><?php echo __('Interface Conf'); ?></dt></br>
    <pre>
        mab
        switchport mode access
        authentication host-mode multi-auth
        authentication order mab
        dot1x pae authenticator
        authentication port-control auto
        authentication violation replace
        mab
        authentication periodic
        authentication timer inactivity server
    </pre>

    <dt><?php echo __('Authentification mode'); ?></dt></br>
    <pre>
authentication host-mode [multi-auth | multi-domain | multi-host | single-host]
Allow multiple hosts (clients) on an 802.1x-authorized port : 
    • single-host :
    • multi-host : Allow multiple hosts on an 802.1x-authorized port after a single host has been authenticated. 
    • multi-domain : Allow both a host and a voice device, such as an IP phone (Cisco or non-Cisco), to be authenticated on an IEEE 802.1x-authorized port.
    • multi-auth : Allow one client on the voice VLAN and multiple authenticated clients on the data VLAN. 
    </pre>

    <dt><?php echo __('Violation mode'); ?></dt></br>
    <pre>
authentication violation shutdown | restrict | protect | replace}
dot1x violation-mode {shutdown | restrict | protect}
	
Configure the violation mode. The keywords have these meanings:
    •shutdown-Error disable the port.
    •restrict-Generate a syslog error.
    •protect-Drop packets from any new device that sends traffic to the port.
    •replace-Removes the current session and authenticates with the new host. 
    </pre>
</dl>
<h4><?php echo __('Force authentication to re-up the server after failure');?></h4>
<pre>
test aaa group RadiusServers username password legacy
</pre>