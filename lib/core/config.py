
try:
	import re
	import sys
        from ConfigParser import SafeConfigParser
except ImportError,e:
        import sys
        sys.stdout.write("%s\n" %e)
        sys.exit(1)


class Config_Parser:

	result = {}

	@staticmethod
        def parse(config_file):

		if not Config_Parser.result:
			cmd_parser = SafeConfigParser()
			cmd_parser.read(config_file)


			for section_name in cmd_parser.sections():
                        	if section_name == "mail":
                                	for name, value in cmd_parser.items(section_name):
                                        	if name == "domain":
                                                	domain = value
							Config_Parser.result["domain"] = domain
                        	elif section_name == "smtp":
                                	for name,value in cmd_parser.items(section_name):
                                        	if name == "server":
                                                	server = value
							Config_Parser.result["server"] = value
                                        	elif name == "time":
                                                	wait_time = value
							Config_Parser.result["wait_time"] = value
                        	elif section_name == "log":
                                	for name, value in cmd_parser.items(section_name):
                                        	if name == "type":
                                                	mail_type = value
							Config_Parser.result["mail_type"] = value
                                        	elif name == "log_path":
                                                	mail_log = value
							Config_Parser.result["mail_log"] = value
                        	else:
                                	print >> sys.stderr, bcolors.OKBLUE + "Error : " + bcolors.ENDC + bcolors.FAIL + "Wrong Parametre Usage In The Config File: %s"% (self.config_file) + bcolors.ENDC
                                	sys.exit(1)
                                	
                        return Config_Parser.result        	
		else:
			return Config_Parser.result	


