#!/usr/bin/perl
# Deployment listener
#
# Checks /var/local/mwop.net.update every 30s to see if it exists;
# if so, it executes the deployment script.
#
# Logs are written for:
# - Failure to remove the update file
# - Successful deployment
# - Deployment failure
#
# If the script is unable to remove the update file, the daemon 
# will halt

use strict;
use warnings;
use Proc::Daemon;

Proc::Daemon::Init;

my $continue = 1;
$SIG{TERM} = sub { $continue = 0 };

my $updateFile   = "/var/local/mwop.net.update";
my $updateScript = "/home/matthew/bin/deploy-mwop";
my $logFile      = "/var/local/mwop.net-deploy.log";
while ($continue) {
    # 30s intervals between iterations
    sleep 30;

    # Check for update file, and restart loop if not found
    unless (-e $updateFile) {
        next;
    }

    # Remove update file
    if (!unlink($updateFile)) {
        # If unable to unlink, we need to quit
        system('echo "' . time() . ': Failed to REMOVE ' . $updateFile . '" >> ' . $logFile);
        $continue = 0;
        next;
    }

    # Deploy
    system($updateScript);
    if ( $? == -1 ) {
    print "command failed: $!\n";
        system('echo "' . time() . ': FAILED to deploy: ' . $! . '" >> ' .  $logFile);
    } else {
        system('echo "' . time() . ': Successfully DEPLOYED" >> ' . $logFile);
    }
}
