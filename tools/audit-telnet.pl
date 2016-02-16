#!/usr/bin/perl
use Net::Telnet::Cisco;

$num_args = $#ARGV + 1;
if ($num_args < 3) {
    print "\nUsage: telnet.pl nas login password enablepassword\n";
    exit;
}
$nas=$ARGV[0];
$login=$ARGV[1];
$password=$ARGV[2];
$enablepassword=$ARGV[3];
@commands = ("show ver","show clock","show cdp neigh det", "sh int counters errors", "show spanning-tree summary", "show vtp status", "show vtp password", "show ntp status", "show env all", "show int counters", "show int | include line protocol|Last clearing", "show standby brief", "show vlan brief", "show run");
my $session = Net::Telnet::Cisco->new(Host => $nas);
$session->login($login, $password);
$session->cmd('terminal length 0');
$session->errmode("return");
# Enable mode
if (!$session->is_enabled) {
    if ($session->enable($enablepassword) ) {
    	foreach $command (@commands) {
    		@output = $session->cmd($command);
        	print "@output";
        }
    } else {
        warn "Can't enable: " . $session->errmsg;
    }
} else {
	foreach $command (@commands) {
		print "$command\n";
    	@output = $session->cmd($command);
    	print "@output";

    	#$session->always_waitfor_prompt;

    }
}
$session->close;
print "EOF\n";