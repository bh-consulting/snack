#!/usr/bin/perl
use Expect;

if((($#ARGV != 0) && ($#ARGV != 4)) || ($ARGV[0] ne 'config' && $ARGV[0] ne 'listusers' && $ARGV[0] ne 'listgroups' && $ARGV[0] ne 'status')) {
    print "Usage:\t$1 <config|status|listusers|listgroups> <domain> <hostname> <admin> <password> \n\nContact: <groche@guigeek.org>\n";
	exit;
}

$domain = $ARGV[1];
$adhostname = $ARGV[2];
$admin_account = $ARGV[3];
$admin_password = $ARGV[4];

($domainpredot, $ext) = split /\./, $domain;

if ($ARGV[0] eq 'config') {
    $cmd = "net rpc getsid -S ".$domain;
    system($cmd);
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
    $res=`ping -c5 $hostname | grep packets`;
    #print $res;
    if ($res =~ /(\d+)\s+received*/)  {
        if ($1 >= 3) {    
            $cmd = "sed -e 's/\\(127\\.0\\.1\\.1\\).*/\\1\\t$hostname\\t$hostname\\.$domain/' -i /etc/hosts";
            #$cmd = "sed -e 's/\\(127\\.0\\.1\\.1\\).*/\\1\\t$hostname\\.$domain\\t$hostname/' -i /etc/hosts";
            #print $cmd;
            system($cmd);
            $cmd = "net conf drop";
            system($cmd);
            $cmd = "net conf import /etc/samba/smb.conf";
            system($cmd);
            #$cmd = '/usr/bin/net join ads -S'.$hostname.'-I '.$adhostname.' -U '.$admin_account.'%'.$admin_password;
            my @cmd = ('/usr/bin/net', '-I', $adhostname, '-W', $domain, '-U', $admin_account.'%'.$admin_password);
            #print @cmd;
            my $success = open(NET, '-|', @cmd, 'ads', 'join');
            
            open(RES,"> /etc/samba/joinresult");
            while (<NET>) {
               #print RES $_;
                if ($_ =~ /^Joined '(.*)' to realm '(.*)'/) {
                    print "\n$1\ $2\n";
                }
            }
            close(RES);
            close(NET);

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
    my @cmd = ('wbinfo', '-t');
    my $success = open(NET, '-|', @cmd);
        
    while (<NET>) {
        if ($_ =~ /^checking the trust secret for domain (.*) via RPC calls succeeded/) {
            $domain = $1;
        }
    }

    my @cmd = ('wbinfo', '-D', $domain);
    #print $cmd;
    my $success = open(NET, '-|', @cmd);
    while (<NET>) {
        if ($_ =~ /^Alt_Name\s+:\s+(.*)/) {
            $altdomain = $1;
        }
    }
    
    my $filename = '/etc/freeradius/proxy.conf';
    open(my $fh, '<:encoding(UTF-8)', $filename)
      or die "Could not open file '$filename' $!";
    
    $regex0 = "\\s*realm\\s+host\\s*{\s*";
    $regex1 = "\\s*realm\\s+$domain\\s*{\s*";
    $regex2 = "\\s*realm\\s+$altdomain\\s*{\s*"; 
    $domainfound = 0;
    $altdomainfound = 0;
    $hostfound = 0;
    
    while (my $row = <$fh>) {
      chomp $row;
      #print $row;
      if ($row =~ $regex0) {
        $hostfound = 1;
      }
      if ($row =~ $regex1) {
        $domainfound = 1;
      }
      if ($row =~ $regex2) {
        $altdomainfound = 1;
      }
    }
    close $fh;
    open(my $fh, '>>:encoding(UTF-8)', $filename)
      or die "Could not open file '$filename' $!";
    if ($hostfound == 0) {
        say $fh "\nrealm host {
    type = radius
    authhost = LOCAL
    accthost = LOCAL
}\n";
    }
    if ($domainfound == 0) {
        say $fh "realm $domain {
    type = radius
    authhost = LOCAL
    accthost = LOCAL
}\n";
    }
    if ($altdomainfound == 0) {
        say $fh "realm $altdomain {
    type = radius
    authhost = LOCAL
    accthost = LOCAL
}\n";
    }
    close $fh;
}

if ($ARGV[0] eq 'status') {
    my @cmd = ('wbinfo', '-t');
    #print @cmd;
    my $success = open(NET, '-|', @cmd);
        
    while (<NET>) {
        print $_;
    }
}

if ($ARGV[0] eq 'listusers') {
    my @cmd = ('wbinfo', '-u');
    #print @cmd;
    my $success = open(NET, '-|', @cmd);
        
    while (<NET>) {
        print $_;
    }
}

if ($ARGV[0] eq 'listgroups') {
    my @cmd = ('wbinfo', '-g');
    #print @cmd;
    my $success = open(NET, '-|', @cmd);
        
    while (<NET>) {
        print $_;
    }
}