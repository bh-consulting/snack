#!/usr/bin/perl
use Net::Telnet::Cisco;

$num_args = $#ARGV + 1;
if ($num_args < 3) {
    print "\nUsage: telnet.pl login password enablepassword command\n";
    exit;
}
$nas=$ARGV[0];
$login=$ARGV[1];
$password=$ARGV[2];
$enablepassword=$ARGV[3];
$command=$ARGV[4];
my $session = Net::Telnet::Cisco->new(Host => $nas);
$session->login($login, $password);

# Enable mode
if (!$session->is_enabled) {
    if ($session->enable($enablepassword) ) {
        @output = $session->cmd($command);
        print "@output";
    } else {
        warn "Can't enable: " . $session->errmsg;
    }
} else {
    # Execute a command
    my @output = $session->cmd($command);
    print @output;
}
$session->close;


