
try:
        import os
	import re
	import sys
	import random
	import string
        import base64
        import smtplib
        import StringIO
        import mimetools
	import mimetypes
        import cStringIO
	import MimeWriter
	from core.common import *
	from core.exceptions import SeesExceptions
except ImportError,e:
        import sys
        sys.stdout.write("%s\n" %e)
        sys.exit(1)


class Smtp:
	"""
		Functions and variables related to sending email ...
	"""

	email_id_list = []

	def __init__(self):

		self.num1 = random.randrange(4,8)
                self.num2= random.randrange(5,9)


	def parse_mail_log(self, mail_type, mail_log, real_mail_name, mail_to):

		timestamp = None
		mail_id = None
		mail_reg = re.compile("\s([^:]+): from=<%s>"% real_mail_name)

		log_file = open(mail_log, "r")	
		for line in log_file:
			if re.search(mail_reg, line):
				timestamp = line.split(" ")[0] + " " + line.split(" ")[1] + " " + line.split(" ")[2]
				mail_id = re.search(mail_reg, line).group(1)
				break
	
		return_postfix  = "%s: to=<%s>.*status=(.*) %s"% (mail_id, mail_to, timestamp)
		return_sendmail = "%s: to=<%s>.*stat=(.*) %s"% (mail_id, mail_to, timestamp)
		result_list = {"postfix":return_postfix, "sendmail":return_sendmail}

		log_file.close()

		return result_list[mail_type]
			
	

	def random_email(self):
		"""
			Create random string for sending email so that target email server doesn't recognize that this is a spam email ...
		"""

                chars_1 = "".join( [random.choice(string.letters) for i in xrange(self.num1)] )
                chars_2 = "".join( [random.choice(string.letters) for i in xrange(self.num2)] )

                return  chars_1 + "." + chars_2



        def send_html_email(self, fake_mail, mail_to, subject, text, html_content):
                """
                        Send html email 
                """

                out = cStringIO.StringIO()
                htmlin = cStringIO.StringIO(html_content)
                txtin = cStringIO.StringIO(text)

                writer = MimeWriter.MimeWriter(out)

                writer.addheader("From", fake_mail)
                writer.addheader("Subject", subject)
                writer.addheader("MIME-Version", "1.0")
                writer.addheader("To", mail_to)

                writer.startmultipartbody("alternative")
                writer.flushheaders()

                subpart = writer.nextpart()
                subpart.addheader("Content-Transfer-Encoding", "quoted-printable")
                pout = subpart.startbody("text/plain", [("charset", 'utf-8')])
                mimetools.encode(txtin, pout, 'quoted-printable')
		txtin.close()

                subpart = writer.nextpart()
                subpart.addheader("Content-Transfer-Encoding", "quoted-printable")

                pout = subpart.startbody("text/html", [("charset", 'utf-8')])
                mimetools.encode(htmlin, pout, 'quoted-printable')
                htmlin.close()

                writer.lastpart()
                msg = out.getvalue()
                out.close()

                return msg

		
	def send_attachment(self, mail_from = '', to = '', subject = '', attach_list = None, html_file = ''):
                """
                        Send email  with attachment
                """
	
		message = StringIO.StringIO()
                writer = MimeWriter.MimeWriter(message)

                writer = MimeWriter.MimeWriter(message)

                writer.addheader('To', to)
                writer.addheader('From', mail_from)
                writer.addheader('Subject', subject)
                writer.addheader('MIME-Version', '1.0')

                writer.startmultipartbody('mixed')
                writer.flushheaders()

                # start with a text/plain part
                part = writer.nextpart()
                body = part.startbody('text/plain', [("charset", 'utf-8')])
                part.flushheaders()

                for text in open(html_file):
                        body.write(text.encode('utf-8'))

                	# now add the attachments
		if attach_list is not None:
               	 	for attachment in attach_list:
                		for attach in attachment.split(","):
                        		filename = os.path.basename(attach)

                                	ctype, encoding = mimetypes.guess_type(attach)
                                	if ctype is None:
                                		ctype = 'application/octet-stream'
                                        	encoding = 'base64'
                                	elif ctype == 'text/plain':
                                		encoding = 'quoted-printable'
                                	else:
                                        	encoding = 'base64'

                                	part = writer.nextpart()
                                	part.addheader('Content-Transfer-Encoding', encoding)

                               	 	body = part.startbody("%s; name=%s" % (ctype, filename))
                                	mimetools.encode(open(attach, 'rb'), body, encoding)

                writer.lastpart()
                msg = message.getvalue()

                return msg



	def main(self, options, from_mail, from_mail_gecos, mail_to, subject, smtp_server, domain, html_file, verbose, mail_type, mail_log, attach = None):
                """
                        Create email and send whatever options used attach or text ...
                """

                real_mail_name = self.random_email() + "@" + domain
                fake_mail = "%s <%s>"% (from_mail_gecos, from_mail)

                mail_to = mail_to
                subject = subject
                text = ''

                # Ready to send email ...
                if options == "attach":
                        text = ''
                        attach_list = []

                        for file in attach:
                                attach_list.append(file)

                        try:
                                message = self.send_attachment(fake_mail, mail_to, subject, attach_list, html_file)
                        except Exception, mess:
                                raise SeesExceptions("%s"% mess)

                        try:
                                server = smtplib.SMTP("%s"% (smtp_server))
                                server.sendmail('%s'% ( real_mail_name ), mail_to, message)

				_email_id = self.parse_mail_log(mail_type, mail_log, real_mail_name, mail_to)
				email_id = _email_id + " " + from_mail + " " + mail_to
				Smtp.email_id_list.append(email_id)

                                if verbose:
                                        print >> sys.stderr, bcolors.OKGREEN + "[+] " + bcolors.ENDC +  bcolors.FAIL + "%s"% (from_mail) + bcolors.ENDC + bcolors.FAIL + " -> " + bcolors.ENDC +  bcolors.OKBLUE + "%s"% (mail_to) + bcolors.ENDC

                                server.quit()
                        except Exception, mess:
                                raise SeesExceptions("Error: %s"%  mess)
                else:
                        text_file = open(html_file,'r')
                        html_content = text_file.read()
                        text_file.close()

                        try:
                                message = self.send_html_email(fake_mail, mail_to, subject, text, html_content)
                        except Exception, mess:
                                raise SeesExceptions("%s"% mess)

                        try:
                                server = smtplib.SMTP("%s"% (smtp_server))
                                server.sendmail('%s'% ( real_mail_name ), mail_to, message)

				_email_id = self.parse_mail_log(mail_type, mail_log, real_mail_name, mail_to)
				email_id = _email_id + " " + from_mail + " " + mail_to
				Smtp.email_id_list.append(email_id)

                                if verbose:
                                        print >> sys.stderr, bcolors.OKGREEN + "[+] " + bcolors.ENDC +  bcolors.FAIL + "%s"% (from_mail) + bcolors.ENDC + bcolors.FAIL + " -> " + bcolors.ENDC +  bcolors.OKBLUE + "%s"% (mail_to) + bcolors.ENDC
                                server.quit()
                        except Exception, mess:
                                raise SeesExceptions("Error: %s"%  mess) 
