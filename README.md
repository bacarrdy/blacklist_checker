blacklist_checker
=================

Simple bash script to check whether if single IP addresses or subnets are blacklisted or not. 
Made by www.vpsnet.lt

This simple script can check subnets (from file sub) to see if they are blacklisted in a blacklist (from file list)

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

I have checked it on our www.VPSnet.lt subnets and it works fine, you can edit it if you want
