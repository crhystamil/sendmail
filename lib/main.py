
try:
	import os
	import re
        import sys
	import time
	import random
	import socket
	import logging
        import argparse
	import subprocess
	from smtp import Smtp
	from core.common import *
	from core.version import *
	from core.config import Config_Parser
	from core.exceptions import SeesExceptions
except ImportError,e:
        import sys
        sys.stdout.write("%s\n" %e)
        sys.exit(1)


def is_file_exists(file_list):

	for file in file_list:
        	if not os.path.exists(file):
			raise SeesExceptions("The file \"%s\" doesn't Exists On The System !!!"% (file))
		

class AddressAction(argparse.Action):

        def __call__(self, parser, args, values, option = None):
                args.options = values

                if args.attach:
			if not args.options:
                        	parser.error("Usage --attach <file1 file2 file3> ")
			else:
				is_file_exists(args.options)


class Main:

	def __init__(self):

		parser = argparse.ArgumentParser()
                group_parser = parser.add_mutually_exclusive_group(required = True)

                group_parser.add_argument('--attach', dest='attach', action='store_const', const='attach', help="Attach Email")
                group_parser.add_argument('--text', dest='text', action='store_const', const='text', help="Text Email")

                parser.add_argument('options', nargs='*', action = AddressAction)
                parser.add_argument('--config_file', '-c', action = 'store', dest = 'config_file', help = "Configuration Files", metavar="FILE", required = True)
                parser.add_argument('--mail_user', '-m', action = 'store', dest = 'mail_user_file', help = "Mail User File", metavar="FILE", required = True)
                parser.add_argument('--html_file', '-f', action = 'store', dest = 'html_file', help = "Content of Html File" ,metavar="FILE", required = True)
                parser.add_argument('--verbose', '-v', action = 'store_true', help = "Verbose For Ending Email", default = False)
                parser.add_argument('--warning', '-w', action = 'store_true', help = "Warning Message", default = False)
		
                self.args = parser.parse_args()


		file_list = (self.args.config_file, self.args.mail_user_file, self.args.html_file)
		is_file_exists(file_list)

		self.config_values = Config_Parser.parse(self.args.config_file)	

		self.comment_reg = re.compile("^[#|;].*")
                self.exit_reg = re.compile("^exit$")

		logfile_path = "sees.log"
		self.logger = logging.getLogger('Sees')
		log_handler = logging.FileHandler('sees.log')
		formatter = logging.Formatter("SEES :: %(message)s ")
		log_handler.setFormatter(formatter)
		self.logger.addHandler(log_handler) 
		self.logger.setLevel(logging.INFO)

		self.smtp = Smtp()


	def run(self):

		server = self.config_values["server"]
		wait_time = self.config_values["wait_time"]
		domain = self.config_values["domain"]
		mail_type = self.config_values["mail_type"]
		mail_log = self.config_values["mail_log"]

		try:
                        sock = socket.socket()
                        sock.connect((server,25))
                except:
                        raise SeesExceptions("Please check your smtp server, netstat -nlput | grep 25")


		try:
                        read_file = open(self.args.mail_user_file, "r").read().splitlines()
                except Exception, mess:
                        raise SeesExceptions("Error: %s"%  mess)


		for  line in read_file:
			if re.search (self.exit_reg, line):
				time.sleep(5)
				log_file = open(mail_log, "r")
				for line in log_file:
					for _ in self.smtp.email_id_list:
						id_line = str(_.split(" ")[0]) + " " + str(_.split(" ")[1])
						timestamp = str(_.split(" ")[2]) + " " + str(_.split(" ")[3]) + " " + str(_.split(" ")[4])
						from_mail = str(_.split(" ")[5])
						mail_to = str(_.split(" ")[6])
						
						id_reg = re.compile(id_line)
						if re.search(id_reg, line):
							mail_stat = re.search(id_reg, line).group(1)
							result = timestamp + " : " + from_mail + " <=> " + mail_to + " :: " + "Result: \"%s\"" %mail_stat
							self.logger.info(result)
				log_file.close()

                                print >> sys.stdout, bcolors.FAIL + "%s"% (message) + bcolors.ENDC
				sys.exit(0)	
                        elif re.search(self.comment_reg, line):
                                continue
			else:
				if not len(line.split(":")) == 4:
                                        print >> sys.stderr, bcolors.OKBLUE + "Warning : " + bcolors.ENDC + bcolors.FAIL + "Line must be \"X:X:X:X\" format," + bcolors.OKBLUE +  "But line is " + bcolors.ENDC + bcolors.FAIL+ "%s"% (line) + bcolors.ENDC
                                else:
					from_mail_header = line.split(":")[0]
                                        from_mail_gecos = line.split(":")[1]
                                        subject = line.split(":")[2]
                                        mail_to = line.split(":")[3]

					time_interval = wait_time.split(",")
                                        if len(time_interval) == 2:
                                                wait = random.randrange(int(time_interval[0]),int(time_interval[1]))
                                        else:
                                                wait = int(wait_time.split(",")[0])

					try:
                                        	self.smtp.main(self.args.attach, from_mail_header, from_mail_gecos, mail_to, subject, server, domain, self.args.html_file, self.args.verbose, mail_type, mail_log, self.args.options)
					except Exception, mess:
                                		raise SeesExceptions("Error: %s"%  mess)

                                        time.sleep(float(wait))

