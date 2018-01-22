SEES
====

```
#### Disclaimer
Using SEES for malicious purposes is illegal. USE AT YOUR OWN RISK
```

## A Social Engineering Attack/Audit Tool for Spear Phishing

### SEES GUI Installation

SeeS now also has a web interface implemented under www directory. Just copy it to your Apache installation under a directory called sees, open it in your browser
and follow the installation steps...

Assuming that;
```
Apache user is www-data
Sees standalone is copied under /usr/local/sees/. If you want to chance this location, please edit the config['sees_root_dir'] as you like;

# vi application/config/sees.php
 "$config['sees_root_dir'] =  '/usr/local/sees/';"  

Apache root directory is /var/www/
Sees web is copied is under /var/www/sees/
Local mail server's log file is /var/log/mail.log 

After that you should run the commands below

# chown -R www-data /usr/local/sees/ /var/www/html/sees/
# chmod 640 /var/log/mail.log
# chgrp www-data /var/log/mail.log
# chmod 755 /usr/local/sees/sees.py

In addition above, mod_rewrite module should be enabled and "AllowOverride All" directive must be set root / and apache root directory in the Apache config file like this;

<Directory />
        Options FollowSymLinks
        AllowOverride All
        Order allow,deny
	allow from all
</Directory>

<Directory /var/www/html/>
        Options FollowSymLinks
        AllowOverride All
	Order allow,deny
	allow from all
</Directory>
```

Note: Allowed upload file types are "gif|jpg|png|doc|txt|pdf|rar|docx|pptx|ppt|xlsx|xls|zip|html|htm|text", make sure that file size is max 12MB in PHP configuration file.

![alt tag](https://raw.githubusercontent.com/galkan/sees/master/www/public/img/sees_help.PNG)

![alt tag](https://raw.githubusercontent.com/galkan/sees/master/www/public/img/sees_main.PNG)

### What is SEES?

 Most of the companies nowadays have their firewalls, threat monitoring and prevention security appliances setup. With these mechanisms in place, security precautions are taken and incidents are monitored. Inbound traffic being restricted, SEES on the other hand is developed for sending targeted phishing emails in order to carry sophisticated social engineering attacks/audits.

 SEES aims to increase the success rate of phishing attacks by sending emails to company users as if they are coming from the very same company’s domain. The attacks become much more sophisticated if an attacker is able to send an email, which is coming from ceo@example.org email address, to a company with domain example.org. 

#### Example SMTP Service Configuration

 It is possible to send emails with or without attachments with SEES. But first, a working SMTP service is needed to send an email. You can use postfix or sendmail for this purpose.. Here, postfix service will be used as an example. On Kali linux this can easily be achieved by using the package management system; 

```
 # apt-get install postfix
```


 After the installation and configuration postfix is re/started.

```
 # /etc/init.d/postfix start
```

 A simple netstat command can be executed to check if the service is up and running.

```
 # netstat -nlput | grep 25  
tcp        0      0 127.0.0.1:25            0.0.0.0:*               LISTEN      13707/master
```

### Downloading & Configuring SEES

Latest source code can be downloaded fro https://github.com/galkan/sees. 
For Backtrack Linux first we should install dependencies. For Kali Linux you can skit this step.

```
 # apt-get install python-argparse  
 # wget https://github.com/galkan/sees/archive/master.zip  
 # unzip master.zip  
```

### Configuration

 SEES configuration file includes the domain parameter under the **[mail]** section, server and time parameters under the **[smtp]** section. Example **config.cfg** file is under **config/ directory**.


 An example configuration file is shown below;

```
 # cat config/config.cfg  
[mail]  
domain = example.com  
[smtp]  
server = 127.0.0.1  
time = 1,3  
[log]
type = postfix
log_path = /var/log/mail.log
```

Paramters used inf config file are as follows:  
 - **domain**: This parameter notes the source domain names that the target SMTP server will get the phishing emails. 

 SEES produces emails using random domain names in order to prevent target system to classify original phishing emails as spam. Relevant function is shown below:  
```python

 def random_email(self):
                """
                        Create random string for sending email so that target email server doesn't recognize that this is a spam email ...
                """
                chars_1 = "".join( [random.choice(string.letters) for i in xrange(self.num1)] )
                chars_2 = "".join( [random.choice(string.letters) for i in xrange(self.num2)] )
                return  chars_1 + "." + chars_2
```

 - **server**: This parameter notes the source SMTP server that the phishing emails will be sent from. Server living on 127.0.0.1 will be used easily preventing any authorization & authentication mechanisms.

 - **time**: This parameter notes the time differences between sent emails. With this behaviour target SMTP servers are tried to be fooled in order not to classify the original phishing emails as spam. Two options are possible to determine the time differences. One of them is a fixed time period and this is the easy one. The other alternative is using a time range with a comma between, such as 1,3. This denotes waiting a random time difference between 1 and 3 seconds each time before sending a  phishing email.

 - **type**: This parameter specify the which email server you want to use. So SEES can parse and extract the result.
 - **log_path**: This parameter specify the log path that you want to use. So SEES knows which log file will be parsed to extract results. 
 
### Structure of Emails sent by SEES

 The structure of emails are represented in the following way;

 **From Email:From Name Surname:Email Subject:Target Email**

 Name, surname and the subject parts are obvious. The From Email parameter denotes the from email address, such as, mail_from@example.com. And Target Email parameter denotes the target email address, such as, mail_to@example.com.

 The below file includes two definitions of emails that will be sent. In order stop e-mail sending , an **exit**  command can be entered in config file. The definitions after the **exit** statement will be discarded.

```
 # cat config/mail.user  
MANAGER@example.com:Gökhan ALKAN:About Salary:mail_to@gmail.com  
MANAGER2@example.com:Gökhan ALKAN:About Salary:mail_to@gmail.com  
exit  
```

 **--text** parameter denotes that the email will not have an attachment but will only include a text message. If the email should include HTML text then the HTML file name should be indicated after the **--html_file** parameter.

```
 # cat data/html.text  
<html>  
        example content  
</html>  
```

 **--attach** parameter denotes the file names of attachments. More than one file names can be attached with a space between. Moreover, if the email should include text then the file name should be indicated after the **--html_file** parameter. But the file content shouldn’t be in HTML. If you want to send e-mail with blank body just creat blank file. An example content is shown below;  

```
 # cat data/attach.text  
example content
```

### SEES Use Cases

 **Scenario 1**: Emails with no attachment

 An example of sending emails with message content only is shown below.

```
 # ./sees.py --text --config_file config/config.cfg --mail_user config/mail.user --html_file data/html.text -v  
Using SEES for malicious purposes is illegal. USE AT YOUR OWN RISK, Agree (Y|n)  
Y  
[+] MANAGER@example.com -> mail_to@gmail.com  
[+] MANAGER2@example.com -> mail_to@gmail.com  
```


 **-v** parameter prints the sent email on the screen for debugging purposes. The targeted user’s  email client will appear as MANAGER@example.com as the sender for the first email but the target SMTP server will process the same email as it is coming from **tUniKSr.fPrAin@example.com**. As it was pointed out before, the random email addresses are formed in order to bypass spam controls on the target SMTP server.  

 ...
Return-Path: <tUniKSr.fPrAin@example.com>  
From: MANAGER@example.com  
..
**Scenario 2: Emails with attachments**

 An example of sending emails with attachments is shown below.  
```
 # ./sees.py --attach data/sample.pdf data/sample.docx --config_file config/sees.cfg --mail_user config/mail.user --html_file data/attach.text -v  

Using SEES for malicious purposes is illegal. USE AT YOUR OWN RISK, Agree (Y|n)  
Y  
[+] MANAGER@example.com -> mail_to@gmail.com  
[+] MANAGER2@example.com -> mail_to@gmail.com  
```


 The attached files are pointed with file names after the **--attach** parameter with a single space in between. **sample.pdf** and **sample.docx** are used as attachments with the example above.

 **-v** parameter prints the sent email on the screen for debugging purposes. The targeted user’s  email client will show MANAGER@example.com as the sender for the first email but the target SMTP server will process the same email as it is coming from PfFYyS.YtODLA@example.cpm. As it was pointed out before, the random email addresses are formed in order to bypass spam controls.  Example output:  

```
Return-Path: <PfFYyS.YtODLA@example.com>  
From: MANAGER2@example.com
```  

### SEES Logging

Once all the emails have been sent, you can understand what's happening. SEES logged whole operations which you did to sees.log file in your current working directory.

Log Format

Time - From Email - To Email - Result


Example Log Line;

...
...
SEES :: Sep 28 17:03:54 : MANAGER@example.com <=> mail_to@example.com :: Result: "sent (250 2.0.0 OK 1411913035 m13si9167381wiv.32 - gsmtp)"
...
...


### Extra Tool

Sees comes with extra script which was developed bash script named let_sees.sh under the script directory in order to use sees command line easily. 

It has a menu to navigate where you will go. It works in  location of sees.py 

```
# chmod 755 let_sees
```

First option is to make mail.user file or whatever you call. Mail.user is needed file for SEES to send mails. It includes sender's name, sender's email, subject and reciepent's mail. Especially when you have so many emails, first option is useful to prepare mail.user structure automatically. You should provide emails that seperated line by line. 

Second option is to send attached emails. Just follow the instructions. You may need to have config and mail.user files before this.

Third option is to send just text emails. Just follow the instructions. You may need to have config and mail.user files before this.

Fourth option is to configure config file. You can name config file whatever you want but SEES default uses sees.cfg file under config directory. You should configure your SEES before sending emails.

Fifth option explains itself.

Or control + C to exit anywhere.

Please provide us feedback for any advice or correction. 


```
#### Disclaimer

Using SEES for malicious purposes is illegal. USE AT YOUR OWN RISK
``` 
 
#### Thanks To:

 - Bedirhan Urgun
 - Ender Akbaş
