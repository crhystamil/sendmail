var serviceApp = angular.module('seesApp', ['angularFileUpload', 'ngAnimate']);

serviceApp.controller('SeesCtrl', ['$scope','$upload','$http','$interval', function($scope, $upload, $http, $interval) {

    $scope.init = function(){
        $scope.sendbuttonvalue = "Send";
        
        $scope.gui = {};
        $scope.showgui('about');
                
        $scope.fetchattachments();
        $scope.fetchconfiguration();

        $scope.mail = {};
        $scope.fetchmail();
        
        $scope.loglines = [];
        $scope.lastlogtime = "";                
        $interval($scope.readlog, 5000);  
    };
    
    $scope.showgui = function(whichgui){
        $scope.gui.mail = true; 
        $scope.gui.configuration = true;
        $scope.gui.log = true;
        $scope.gui.about = true;
        $scope.gui.mailactive = ""; 
        $scope.gui.configurationactive = "";
        $scope.gui.logactive = "";
        $scope.gui.aboutactive = "";
        
        if(whichgui === "mail"){
            $scope.gui.mail = false;
            $scope.gui.mailactive = "active"; 
        }
        if(whichgui === "configuration"){
            $scope.gui.configuration = false;
            $scope.gui.configurationactive = "active"; 
        }
        if(whichgui === "log"){
            $scope.gui.log = false;
            $scope.gui.logactive = "active"; 
        }
        if(whichgui === "about"){
            $scope.gui.about = false;            
            $scope.gui.aboutactive = "active"; 
        }
    };
    
    // For todays date;
    Date.prototype.today = function () { 
        return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
    };

    // For the time now
    Date.prototype.timeNow = function () {
         return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
    };   
     
    $scope.readlog = function(){
        $http({method: 'GET', url: 'log/read'}).
            success(function(response, status, headers, config) { 
                if(response.status === "success"){
                    for (var i = 0; i < response.data.length; i++) {
                        if(response.data[i].trim()){
                            $scope.loglines.push(response.data[i].trim());
                            $scope.lastlogtime = "Last log line fetched " + new Date().today() + " @ " + new Date().timeNow();                            
                        }
                    }                
                }
                else{
                    $scope.alert(response.msg, "danger");                    
                    console.log(response);                    
                }
            }).
            error(function(response, status, headers, config) {            
                console.log(response);
            });
    };
    
    $scope.selectandupload = function(attachmentstoupload) {
        for (var i = 0; i < attachmentstoupload.length; i++) {
            var file = attachmentstoupload[i];   
            $scope.upload = $upload.upload({
                url: 'attachment/upload', 
                file: file,
                fileFormDataName: 'userfile'
            }).success(function(response, status, headers, config) {
                if(response.status === "success"){
                    $scope.alert(response.msg, "success");
                    $scope.fetchattachments();                    
                }
                else{
                    $scope.alert(response.msg, "danger");                    
                }
            });
        }        
    };
     
    $scope.fetchattachments = function(){
        $http({method: 'GET', url: 'attachment/fetch'}).
            success(function(response, status, headers, config) {   
                $scope.uploadedattachments = response;                
            }).
            error(function(response, status, headers, config) {            
                console.log(response);
            });
    };
    
    $scope.fetchmail = function(){
        $http({method: 'GET', url: 'mail/fetch'}).
            success(function(response, status, headers, config) {
                if(response.status === "success"){
                    var to = "";
                    for (var i = 0; i < response.data.length; i++) {                    
                        to += response.data[i].to + "\n";
                    }     
                    $scope.mail.to = to;
                    $scope.mail.from = response.data[0].from;
                    $scope.mail.name = response.data[0].name;
                    $scope.mail.subject = response.data[0].subject;
                }
                else
                    console.log(response);
            }).
            error(function(response, status, headers, config) {            
                console.log(response);
            });           
    };
    
    $scope.sendmail = function(){
        
        validationmsg = "";
        if(!$scope.mail.attachment && !$scope.mail.body){
            validationmsg = "You have to select either an HTML or an Attachment. Or both...<br/>";
        }
        
        if(!$scope.mail.from){
            validationmsg += "From field is required<br/>";
        }

        if(!$scope.mail.name){
            validationmsg += "Name field is required<br/>";
        }
        
        if(!$scope.mail.subject){
            validationmsg += "Subject field is required<br/>";
        }        
        
        if(!$("#to").val()){
            validationmsg += "To field is required<br/>";
        }

        if(validationmsg){
            $scope.alert(validationmsg, "danger");
            return;
        }
        // marshal $scope.mail into array of emails
        var emails = [];
        var lines = $("#to").val().split("\n");
        for (var i = 0; i < lines.length; i++) 
        {
            if(lines[i].trim()){
                email = {};
                email.to = lines[i].trim();
                email.from = $scope.mail.from;
                email.name = $scope.mail.name;
                email.subject = $scope.mail.subject;
                
                if($scope.checked && $scope.mail.attachment)
                    email.attachment = $scope.mail.attachment.name;
                else
                    email.attachment = "";
                
                email.html = $scope.mail.body.name;
                emails[emails.length] = email;
            }
        }
        
        $scope.sendbuttonvalue = "Sending...";
        $scope.issendbuttondisabled = true;
        
        $http({method: 'POST', url: 'mail/send', data : emails}).
            success(function(response, status, headers, config) {
                if(response.status === "success"){
                    $scope.alert(response.msg, "success");
                }
                else{
                    $scope.alert(response.msg, "danger");
                    console.log(response);
                }
                $scope.issendbuttondisabled = false;                
                $scope.sendbuttonvalue = "Send";
            }).
            error(function(response, status, headers, config) {            
                console.log(response);
                $scope.issendbuttondisabled = false;                
                $scope.sendbuttonvalue = "Send";
            });     
    };
    
    $scope.fetchconfiguration = function(){
        $http({method: 'GET', url: 'configuration/fetch'}).
            success(function(response, status, headers, config) {
                if(response.status === "success")
                    $scope.configuration = response.data;                
                else
                    console.log(response);
            }).
            error(function(response, status, headers, config) {            
                console.log(response);
            });
    };  
    
    $scope.updateconfiguration = function(){
        $http({method: 'POST', url: 'configuration/update', data : $scope.configuration}).
            success(function(response, status, headers, config) {
                if(response.status === "success"){
                    $scope.alert(response.msg, "success");
                }
                else{
                    $scope.alert(response.msg, "danger");
                    console.log(response);
                }
            }).
            error(function(response, status, headers, config) {            
                console.log(response);
            });        
    };
    
    $scope.alert = function(msg, type){
        $('#alert_placeholder').html('<div class="alert alert-' + type + 
                                     ' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span>' + msg + '</span></div>');
        setTimeout(function() { $("div.alert").remove(); }, 3000);        
    };
    
    $scope.init();

}]);
