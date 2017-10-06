blacklist_checker
=================

Bash script to check whether your ips are blacklisted.
Made by www.vpsnet.com

This simple script can check subnets (from file subnets) to see if they are blacklisted in a blacklist (from file list)

You need to check variables in blcheck.sh file and configure them by your requirements.

Just add subnets in file sub like this:

	1.2.3 1 255
	2.3.4 15 255
	8.6.4 200 255

	for example I'll explain 1.2.3 1 255
	1.2.3 is the begining of the c class subnet or rather /24
	1 is the first ip from the subnet from which script will start to check
	255 is the last ip from subnet to be checked. From here on script stop checking that subnet and will continue to the next subnet
	
	If you want, you can add single IP addresses using the same format
	Such as:
	3.4.5 6 6
  
and then add blacklists in file list

	# Make sure you have created an account and whitelisted your IP to query from Barracuda
	b.barracudacentral.org
	bl.spamcannibal.org
	bl.spamcop.net
	blackholes.wirehub.net
	
You can add as many blacklists as you need.
Entries that start with # will be disregarded when script runs.

Script requires:
	sendmail
	dig
	mailx (heirloom-mailx)

I have checked it on our www.VPSnet.com subnets and it works fine, you can edit it if you want

Script futhers:
#See content in blcheck.sh to configure these futhers

Script can run each subnet in to background so in that case you can check as many as you need subnets instantly (in same time)

If you run script in background you can controll how many background you want to run

You can tell to script or he should check all subnets files or only one (if you will set to check all files script will read subnets*)

There is possibility to specify DNS server for diging subnets or if you will leave empty this space it will use main dns server

Prompter can tell you delisting procedure in email

You can use smtp or simpe mail from server

You can use database to store logs and curently blacklisted ips

There is some basic web interface

Also there is your curent users notification system about blacklisted ip`s (you need to customize mysql queryes to get details about users so you need to activate it only when you know what you are doing)

And many others

