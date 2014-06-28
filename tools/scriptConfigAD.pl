#!/usr/bin/perl
use Expect;

if((($#ARGV != 1) && ($#ARGV != 4)) || ($ARGV[0] ne 'config' && $ARGV[0] ne 'status')) {
    print "Usage:\t$1 <config|status> <domain> <hostname> <admin> <password> \n\nContact: <groche@guigeek.org>\n";
	exit;
}

$domain = $ARGV[1];
$adhostname = $ARGV[2];
$admin_account = $ARGV[3];
$admin_password = $ARGV[4];

($domainpredot, $ext) = split /\./, $domain;

if ($ARGV[0] eq 'config') {
    open(SMBCONF, "> /etc/samba/smb.conf");
    print SMBCONF "[global]\n";
    print SMBCONF " workgroup = $domainpredot\n";
    print SMBCONF " security = ads\n";
    print SMBCONF " realm = $domain\n";
    print SMBCONF " password server = $adhostname\n";
    print SMBCONF " winbind use default domain = yes\n";
    print SMBCONF " idmap uid = 10000-1000000\n";
    print SMBCONF " idmap gid = 10000-1000000\n";
    close(SMBCONF);

    open(KRB5CONF, "> /etc/krb5.conf");
    print(KRB5CONF "[libdefaults]\n\tdefault_realm = $domain\n\n");
    print(KRB5CONF "[realms]\n\t".$domain." = {\n\t\tkdc = ".$adhostname."\n\t}\n") if($adhostname);
    close(KRB5CONF);

    $info = `hostname`;
    if ($info =~ /^(.*)\n$/) {  
        $hostname = $1;
    }
    open(RESOLVCONF, "> /etc/resolv.conf");
    print RESOLVCONF "nameserver $adhostname\n";
    close(RESOLVCONF);

    system("service winbind restart");
    $res=`wbinfo -p`;
    print $res;
    if ($res =~ /^Ping to winbindd succeeded.*/) {    
        $cmd = "sed -e 's/\\(127\\.0\\.1\\.1\\).*/\\1\\t$hostname\\t$hostname\\.$domain/' -i /etc/hosts";
        #print $cmd;
        system($cmd);

        my $session      = Expect->new;
        my $command     = '/usr/bin/net join ads -S'.$hostname.'-I '.$adhostname.' -U '.$admin_account;
        my $timeout     = 20;
        $session->spawn($command)
            or die "Cannot spawn $command: $!\n";
        $session->expect($timeout,
                    [  "Enter ".$admin_account."'s password:" , #/
                        sub {
                            my $self = shift;
                            $self->send("$admin_password\n");                       
                            exp_continue;
                        }
                    ],
                    [ "Joined ", #/
                        sub {
                             my $self = shift;
                            $output = $self->exp_after;
                            exp_continue;
                        }
                    ],
                    [ "Connection failed: ", #/
                        sub {
                             my $self = shift;
                            $output = $self->exp_after;
                            exp_continue;
                        }
                    ]
                );

        if ($output =~ /^'(.*)' to realm '(.*)'/) {
            print "\n$1\ $2\n";
        }
        #print $output;
        open(RES,"> /etc/samba/joinresult");
        print RES $output;
        close(RES);
        system("service winbind restart");
    }
}
if ($ARGV[0] eq 'status') {
    
}