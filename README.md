blacklist_checker made by www.vpsnet.lt
=================

simple bash script to check or subnets are blacklisted

this simple script can check subnets (from file sub) or they are blacklisted in an blacklist (from file list)

Just add subnets in file sub like this:

	1.2.3 1 255
	2.3.4 15 255
	8.6.4 200 255

	for example ill explain 1.2.3 1 255
	1.2.3 is the begining of c class subnet
	1 is the ip of subnet from witch one script will start to check
	255 is the last ip from subnet on with one script stop checking that subnet and will take other subnet
  
and then add blacklists in file list

	b.barracudacentral.org
	bl.spamcannibal.org
	bl.spamcop.net
	blackholes.wirehub.net
	
you can add blacklists that much as you need

I have checked it on our www.VPSnet.lt subnet`s and works fine, you can edit it if you want
