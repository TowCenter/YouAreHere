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
        
        <link href='https://fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oswald:400,700' rel='stylesheet' type='text/css'>
        
        <!-- build:css css/styles.min.css -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/styles.css">
        <!-- endbuild -->

        <script src="//static.twilio.com/libs/twiliojs/1.2/twilio.min.js"></script>
        <script>Twilio.Device.setup("<?php echo $token; ?>");</script>

    </head>
    <body>
      <header>
        <img class="logo" src="img/logo.png" width="297" height="202" />
        <nav>
          <ul>
            <li><a href="/">Listen</a></li>
            <li><a href="/#about">About</a></li>
          </ul>
        </nav>
        <div class="story">
          <script id="story-template" type="x-handlebars-template">​
            <p>To record your story, call {{story_phone_number}}<br/>or tap the button below.</p>
            <p>
              <a class="btn btn-primary btn-lg" href="tel:{{story_phone_number}}">Tap To Call!</a>
            </p>
            <div class="title">
              <h1>{{name}}</h1>
              <p class="add-date"></p>
            </div>
          </script> 
        </div>
      </header>
      <div class="loading visible">
        <div class="col-sm-12">loading recordings...</div>
      </div>
      <div class="container recordings">
        <div class="row recordings-list">
          <script id="recordings-template" type="x-handlebars-template">​
            {{#each this}}
              <div class="btn-play" role="button"></div>
              <audio controls>
                <source src="{{mp3_url}}" type="audio/mpeg">
                Your browser does not support the audio tag.
              </audio>
              <p class="add-date">{{created}}</p>
            {{/each}}
          </script> 
        </div>
      </div>
      <div class="about">
        <h2>about</h2>
        <p>“You Are Here” is an experimental journalism-distribution network that leverages small, inexpensive, open-source wireless routers to deliver compelling, location-specific content to communities around New York. Starting with a series of high-quality audio pieces that reflect the unique culture and history of the people, politics and communities of the geographic area, the “You Are Here” servers can also act as a kind of digital town square where those nearby can exchange ideas, stories and information. The fact that these servers are not connected to the Internet allows them to accumulate a genuinely local character, in addition to serving as a safe, resilient means of exchanging digital information.</p>
        <p>This is a research project of the Tow Center for Digital Journalism, with fellows Sarah Grant, Dan Phiffer, Amelia Marzec, Susan McGregor, and Benjamen Walker.</p> 
      </div>
      <div class="error">
        <h2>Sorry, something went wrong.</h2>
      </div>

      <!-- build:js js/main.min.js -->
      <script src="js/vendor/jquery-1.11.2.min.js"></script>
      <script src="js/vendor/bootstrap.min.js"></script>
      <script src="js/vendor/handlebars.js"></script>
      <script src="js/main.js"></script>
      <!-- endbuild -->
    </body>
</html>
