<!DOCTYPE html>
<html ng-app="seesApp">
    <head>
            <meta charset="utf-8">
            <title>Welcome to SeeS</title>
            <?php echo put_headers(); ?>        
    </head>
    <body>
        
        <div class="container" ng-controller="SeesCtrl">  
            
            <nav role="navigation" class="navbar navbar-inverse">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="#" class="navbar-brand">SEES</a>
                </div>
                <!-- Collection of nav links, forms, and other content for toggling -->
                <div id="navbarCollapse" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li ng-class="gui.mailactive">
                            <a href="#" ng-click="showgui('mail')">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <span class="text">Mail</span>
                            </a>
                        </li>
                        <li ng-class="gui.configurationactive">
                            <a href="#" ng-click="showgui('configuration')">
                                <span class="glyphicon glyphicon-wrench"></span>
                                <span class="text">Configure</span>
                            </a>
                        </li>
                        <li ng-class="gui.logactive">
                            <a href="#" ng-click="showgui('log')">
                                <span class="glyphicon glyphicon-filter"></span>
                                <span class="text">Log</span>
                            </a>
                        </li>
                        <li ng-class="gui.aboutactive">
                            <a href="#" ng-click="showgui('about')">
                                <span class="glyphicon glyphicon-info-sign"></span>
                                <span class="text">About & Help</span>                                
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
               
            <div ng-hide="gui.mail">

                <form class="form-horizontal" role="form" name="mail">
                  <div class="form-group">
                    <label for="from" class="col-sm-2 control-label">From:</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="form" id="from" placeholder="From" ng-model="mail.from"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Name:</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="name" placeholder="Name Surname" ng-model="mail.name"/>
                    </div>
                  </div>                                        
                  <div class="form-group">
                    <label for="htmlattachment" class="col-sm-2 control-label">Html:</label>
                    <div class="col-sm-6">
                        <select id="htmlattachment" ng-model="mail.body" 
                                ng-options="attachment.name for attachment in uploadedattachments"
                                class="form-control">
                            <option value="">-- Choose html --</option>
                        </select>                        
                    </div>
                    <div class="col-sm-2">
                        <button ng-file-select="selectandupload($files)" 
                            onclick="this.value = null" 
                            type="button" class="btn btn-success">Select & Upload</button>
                    </div>                    
                  </div>                     
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">With Attachment?:</label>
                    <div class="col-sm-6">
                        <input type="checkbox" ng-model="checked">
                    </div>
                  </div>                     
                  <div class="form-group" ng-show="checked">
                    <label for="attachment" class="col-sm-2 control-label">Attachment:</label>
                    <div class="col-sm-6">
                        <select id="attachment" ng-model="mail.attachment" 
                                ng-options="attachment.name for attachment in uploadedattachments"
                                class="form-control">
                            <option value="">-- Choose attachment --</option>
                        </select>                        
                    </div>
                  </div>                    
                  <div class="form-group">
                    <label for="subject" class="col-sm-2 control-label">Subject:</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="subject" placeholder="Subject" ng-model="mail.subject"/>
                    </div>
                  </div>  
                    <!--
                  <div class="form-group">
                    <label for="message" class="col-sm-2 control-label">Message:</label>
                    <div class="col-sm-6">
                        <textarea rows="8" class="form-control message" id="message" placeholder="Text message" ng-model="mail.message"> </textarea>
                    </div>
                  </div>
                    -->
                  <div class="form-group">
                    <label for="to" class="col-sm-2 control-label">To:</label>
                    <div class="col-sm-6">
                        <textarea rows="8" class="form-control to" id="to" placeholder="Mail addresses for each line" ng-model="mail.to"> </textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <button ng-disabled="issendbuttondisabled" ng-click="sendmail()" class="btn btn-primary">{{ sendbuttonvalue }}</button>
                    </div>
                  </div>
                </form>

            </div>

            <div ng-hide="gui.configuration">            

                <form class="form-horizontal" role="form">
                  <div class="form-group">
                    <label for="domain" class="col-sm-2 control-label">Domain</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="domain" placeholder="Domain" ng-model="configuration.domain">
                    </div>
                    <div class="text-muted help">Domain name that the target SMTP server will get the phishing emails.</div>
                  </div>
                  <div class="form-group">
                    <label for="server" class="col-sm-2 control-label">Server</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="server" placeholder="Server" ng-model="configuration.smtpserver">
                    </div>
                    <div class="text-muted help">SMTP server that the phishing emails will be sent from</div>                    
                  </div>                
                  <div class="form-group">
                    <label for="time" class="col-sm-2 control-label">Interval</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="time" placeholder="Time Interval" ng-model="configuration.time">
                    </div>
                    <div class="text-muted help">Random time differences between sent emails in seconds. Comma seperated two integers.</div>                    
                  </div>                
                  <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">Log Type</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="type" placeholder="Log Type" ng-model="configuration.type">
                    </div>
                    <div class="text-muted help">Email server you use</div>                    
                  </div>
                  <div class="form-group">
                    <label for="logpath" class="col-sm-2 control-label">Log Path</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="logpath" placeholder="Log Path" ng-model="configuration.smtplogpath">
                    </div>
                    <div class="text-muted help">The log path your email server uses</div>                    
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <button ng-click="updateconfiguration()" class="btn btn-primary">Update</button>
                    </div>
                  </div>
                </form>

            </div>

            <div ng-hide="gui.log">
                <h5>{{ lastlogtime }}</h5>   
                <form ng-show="loglines.length" class="form-inline" role="form">
                  <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-search"></span>
                        </div>
                        <input type="text" ng-model="search" class="form-control" placeholder="Search">
                    </div>
                  </div>
                  <!--<button type="submit" class="btn btn-info">Search</button> -->
                </form>
                <div ng-show="!loglines.length" class="alert alert-danger" role="alert">
                    Started tailing the log but no new lines up to now!
                </div>
                <br/>
                <div ng-show="loglines.length" class="log">
                    <div ng-repeat="line in loglines | filter:search track by $index" ng-class-odd="'oddline'" ng-class-even="'evenline'">
                        {{ line }}
                    </div>
                </div>
            </div>

            <div ng-hide="gui.about">
                <div class="well">
                  <h2>Welcome to SEES</h2>
                  <p>
                      SEES is developed for sending targeted phishing emails in order to carry sophisticated social engineering attacks/audits.
                      It aims to increase the success rate of phishing attacks by sending emails to company users as if they are coming from 
                      the very same company’s domain. The attacks become much more sophisticated if an attacker is able to send an email, which 
                      is coming from ceo@example.org email address, to a company with domain example.org. 
                  </p>
                  <p>
                    <div class="alert alert-danger" role="alert">
                        Using SEES for malicious purposes is illegal. USE AT YOUR OWN RISK!
                    </div>                  
                  </p>
                  
                  <h3>Quick How To</h3>
                  <p>
                      Original SeeS is a standalone python program. The web interface is developed if you are
                      not a black screen lover unix geek (just kiddin). There are a few basic actions you need to take
                      in order to get SeeS Web with the standalone program.
                  </p>
                  <p>
                      <h3><span class="label label-info">First</span></h3>
                      Install SeeS standalone by following <a href="https://github.com/galkan/sees/blob/master/README.md">Sees ReadMe.</a>
                      Assuming that;
                      <ul>
                          <li>the Apache user is <span class="label label-success">www-data</span></li>
                          <li>SeeS standalone is copied under <span class="label label-success">/usr/local/sees/</span></li>
                          <li>Apache root directory is <span class="label label-success">/var/www/</span></li>
                          <li>SeeS web is copied under <span class="label label-success">/var/www/sees/</span></li>
                          <li>Local mail server's log file is <span class="label label-success">/var/log/mail.log</span></li>
                      </ul>
                      Run the following;
                      <br/>
                      <code>
                          # chown -R www-data /usr/local/sees /var/www/sees 
                      </code>
                      <br/>
                      <code>
                          # chmod 755 /usr/local/sees/sees.py
                      </code>
                      <br/>
                      <code>
                          # chgrp www-data /var/log/mail.log                          
                      </code>
                      <br/>
                      <code>
                          # chmod 640 /var/log/mail.log
                      </code>
                  </p>
                  <p>
                      <h3><span class="label label-info">Second</span></h3>
                      Enable mod_rewrite Apache module, make sure the enabled site's configuration file has the below bold lines and <b>restart</b>. 
                </p>
                <p>
                      <pre>
&lt;VirtualHost *:80>
        DocumentRoot /var/www
        &lt;Directory />
                Options FollowSymLinks
                <b>AllowOverride All</b>
        &lt;/Directory>
        &lt;Directory /var/www/>
                Options FollowSymLinks MultiViews
                <b>AllowOverride All</b>
                Order allow,deny
                allow from all
        &lt;/Directory>                          
                      </pre>
                  </p>
                  <p>
                      <h3><span class="label label-info">Third</span></h3>
                      After handling the web setup, update <span class="label label-success">/var/www/sees/application/config/sees.php</span> as;
                  </p>
                  <p>
                  <code>
                    $config['sees_root_dir'] =  '/usr/local/sees/'; // make sure it ends it with /
                  </code>
                  </p>
                  <p>
                    <div class="alert alert-danger" role="alert">
                        There's no intrinsic authentication/authorization control embedded in SeeS Web. So use IP restriction or .htaccess
                        for authorization.
                    </div>                  
                  </p>
                  <p>
                      <h3><span class="label label-info">Fourth</span></h3>
                      Click to Configure menu on the SeeS web gui and make sure parameter values are right for you. You are now ready to use SeeS thru web interface.
                  </p>
                  <p><a class="btn btn-danger btn-lg" role="button" href="https://github.com/galkan/sees">Need More of SEES?</a></p>
                </div>
            </div>

            <div id="alert_placeholder"></div>
            
        </div> <!-- container -->
    </body>
</html>