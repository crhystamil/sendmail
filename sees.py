#!/usr/bin/python -W ignore::DeprecationWarning

try:
	import sys
	import signal
	from lib.main import Main
	from lib.core.common import *
	from lib.core.version import *
except ImportError,e:
        import sys
        sys.stdout.write("%s\n" %e)
        sys.exit(1)


def signal_handler(signal, frame):
        print >> sys.stderr, "You have pressed Ctrl+C ..."
        sys.exit(1)


def sees():

	signal.signal(signal.SIGINT, signal_handler)	

	main = Main()
	while True:
		mess = bcolors.FAIL + disclamer + bcolors.ENDC + " : "
		sys.stdout.write(mess)

		if not main.args.warning:
			agree = raw_input()
			if (agree == "Y" or agree == "y"):
				try:	
					main.run()
				except Exception, err:
					print >> sys.stderr, err
					sys.exit(1)
			elif (agree == "N" or agree == "n"):
				print >> sys.stderr, message
				sys.exit(1)
			else:
				print >> sys.stderr, wrong_option
		else:
			try:
                        	main.run()
                        except Exception, err:
                               	print >> sys.stderr, err
                                sys.exit(1)


if __name__ == "__main__":
	"""
		Main Block ... Sees ...
	"""

	sees()

