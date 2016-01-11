<?php
include 'lib/Services/Twilio/Capability.php';
 
// put your Twilio API credentials here
$accountSid = 'AC3248e744302535fb1bf3d2347b02fc1d';
$authToken  = '7b8af20a4682a407b93269983cc5954b';
 
// put your Twilio Application Sid here
$appSid     = 'AP7fb08f9e9ede99ec471bb4a4fc03de45';
 
$capability = new Services_Twilio_Capability($accountSid, $authToken);
$capability->allowClientOutgoing($appSid);
$capability->allowClientIncoming('yah_client');
$token = $capability->generateToken();
?>

<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>you are here</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href='https://fonts.googleapis.com/css?family=Raleway:400,700,300,500' rel='stylesheet' type='text/css'>
        <!-- <link href="http://static0.twilio.com/bundles/quickstart/client.css" type="text/css" rel="stylesheet" /> -->

        <!--build:css css/styles.min.css-->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/styles.css">
        <!--endbuild-->

    </head>
    <body>
      <div class="jumbotron">
        <h1>You Are Here</h1>
          <form class="navbar-form">
            <p>Enter your phone number to have the recording system call you back. Follow the instructions to record your story and have it appear on this page.</p>
            <p><input type="text" placeholder="212-555-1212"></p>
            <p><button type="submit" class="btn btn-primary btn-lg">Record Your Story</button></p>
          </form>
        <div class="record">

        </div>
      </div>
      <div class="container recordings">
        <div class="row">
          <div class="col-sm-12">loading recordings...</div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <h4>Thumbnail label</h4>
                <p class="add-date">Added January 11, 2016</p>
                <p><a href="#" class="btn btn-default" role="button">Listen</a></p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <h4>Thumbnail label</h4>
                <p class="add-date">Added January 9, 2016</p>
                <p><a href="#" class="btn btn-default" role="button">Listen</a></p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <h4>Thumbnail label</h4>
                <p class="add-date">Added January 6, 2016</p>
                <p><a href="#" class="btn btn-default" role="button">Listen</a></p>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <h4>Thumbnail label</h4>
                <p class="add-date">Added January 4, 2016</p>
                <p><a href="#" class="btn btn-default" role="button">Listen</a></p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <h4>Thumbnail label</h4>
                <p class="add-date">Added January 3, 2016</p>
                <p><a href="#" class="btn btn-default" role="button">Listen</a></p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <h4>Thumbnail label</h4>
                <p class="add-date">Added December 26, 2015</p>
                <p><a href="#" class="btn btn-default" role="button">Listen</a></p>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- /container -->  


          <!-- main content -->
          <!-- <div class="col-md-12">
            <button class="call">
              Call
            </button>
         
            <button class="hangup">
              Hangup
            </button>
         
            <div id="log">Loading pigeons...</div>
          </div> -->

      <script src="//static.twilio.com/libs/twiliojs/1.2/twilio.min.js"></script>
      <script>Twilio.Device.setup("<?php echo $token; ?>");</script>
      <!--build:js js/main.min.js-->    
      <script src="js/vendor/jquery-1.11.2.min.js"></script>
      <script src="js/vendor/bootstrap.min.js"></script>
      <script src="js/main.js"></script>
      <!-- endbuild -->
    </body>
</html>
