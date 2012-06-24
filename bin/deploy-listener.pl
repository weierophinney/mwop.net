#!/usr/bin/perl
# Deployment listener
#
# Checks /var/local/mwop.net.update every 30s to see if it contains the value
# "1". If so, it executes the deployment script.
#
# Logs are written for:
# - Failure to open the update file
# - Successful deployment
# - Deployment failure
#
# If the script is unable to open the update file to read or write 10 or more
# times, the daemon will halt.

use strict;
use warnings;
use Proc::Daemon;

Proc::Daemon::Init;

my $continue = 1;
$SIG{TERM} = sub { $continue = 0 };

my $updateFile   = "/var/local/mwop.net.update";
my $updateScript = "/home/matthew/bin/deploy-mwop";
my $logFile      = "/var/local/mwop.net-deploy.log";
my $failureCount = 0;
my $maxFailures  = 10;
while ($continue) {
    my $flag;
    my $read;
    my $write;

    # 30s intervals between iterations
    sleep 30;

    if (!open($read, "<", $updateFile)) {
        system('echo "' . time() . ': Failed to read ' . $updateFile . '" >> ' .  $logFile);
        $failureCount += 1;
        if ($failureCount >= $maxFailures) {
            $continue = 0;
        }
        next;
    }
    read $read, $flag, 1;
    close($read);

    if ($flag != "1") {
        next;
    }

    # Disable updating (so later processes don't try and update)
    if (!open($write, ">", $updateFile)) {
        system('echo "' . time() . ': Failed to UPDATE ' . $updateFile . '" >> ' . $logFile);
        $failureCount += 1;
        if ($failureCount >= $maxFailures) {
            $continue = 0;
        }
        next;
    }
    print $write, "0";
    close($write);

    # Deploy
    system($updateScript);
    if ( $? == -1 ) {
    print "command failed: $!\n";
        system('echo "' . time() . ': FAILED to deploy: ' . $! . '" >> ' .  $logFile);
    } else {
        system('echo "' . time() . ': Successfully DEPLOYED" >> ' . $logFile);
    }
}
