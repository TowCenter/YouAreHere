<?php
header("Access-Control-Allow-Origin: *");

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
      <!-- <div class="jumbotron">
        <h1>You Are Here</h1>
          <form class="navbar-form">
            <p>Enter your phone number to have the recording system call you back. Follow the instructions to record your story and have it appear on this page.</p>
            <p><input type="text" placeholder="212-555-1212"></p>
            <p><button type="submit" class="btn btn-primary btn-lg">Request a Callback</button></p>
          </form>
        <div class="record">

        </div>
      </div> -->
      <div class="heading">
        <img src="img/logo.png" width="297" height="202" />
      </div>
      <nav class="flex-nav">
        <ul>
          <li>RECORD</li>
          <li>LISTEN</li>
          <li>ABOUT</li>
        </ul>
      </nav>
      <div class="container recordings">
        <div class="row">
          <div class="col-sm-12">loading recordings...</div>
        </div>
        <div class="row">
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <div class="btn-play" role="button"></div>
                <div class="title">
                  <h4>Title of story</h4>
                  <p class="add-date">January 11, 2016</p>
                </div>
                <p>Lorem ipsum dolor sit amet, lorem ipsum dolor sit amet, lorem ipsum dolor sit amet, lorem ipsum dolor sit amet.</p>
                <p class="comments">56 Comments</p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <div class="btn-play" role="button"></div>
                <div class="title">
                  <h4>Title of story</h4>
                  <p class="add-date">January 11, 2016</p>
                </div>
                <p>Lorem ipsum dolor sit amet, lorem ipsum dolor sit amet, lorem ipsum dolor sit amet, lorem ipsum dolor sit amet.</p>
                <p class="comments">56 Comments</p>
              </div>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="thumbnail">
              <img src="img/park.jpg" alt="park" />
              <div class="caption">
                <div class="btn-play" role="button"></div>
                <div class="content">
                  <div class="title">
                    <h4>Title of story</h4>
                    <p class="add-date">January 11, 2016</p>
                  </div>
                  <p>Lorem ipsum dolor sit amet, lorem ipsum dolor sit amet, lorem ipsum dolor sit amet, lorem ipsum dolor sit amet.</p>
                </div>
                <div class="comments">
                  <div class="count">
                    56 Comments
                  </div>
                  <div class="expanded">
                    <h3>We'd love to get your input</h3>
                    <div class="comment">
                      <img src="img/park.jpg" alt="park" />
                      <div class="caption">
                        <div class="btn-play" role="button"></div>
                        <div class="content">
                          <h4>comment title</h4>
                          <p>Lorem ipsum dolor sit amet, lorem ipsum dolor sit amet.</p>
                        </div>
                      </div>
                    </div>
                    <div class="comment">
                      <img src="img/park.jpg" alt="park" />
                      <div class="caption">
                        <div class="btn-play" role="button"></div>
                        <div class="content">
                          <h4>comment title</h4>
                          <p>Lorem ipsum dolor sit amet, lorem ipsum dolor sit amet.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- /container -->   
      <div id="record"> 
        <div class="btn-record" role="button"></div>
        <form>
          <input type="text" placeholder="Title" />
          <input type="textarea" placeholder="Description" />
          <input type="text" placeholder="Your Name" />
          <ul>
            <li>
              <div class="btn-default" role="button">Cancel</div>
            </li>
            <li>
              <div class="btn-default" role="button">Re-do</div>
            </li>
            <li>
              <div class="btn-default" role="button">Submit</div>
            </li>
          </ul>
        </form>
        <div class="add-photo">
          <h3>add a photo</h3>
          <div class="btn-default" role="button">no thanks</div>
        </div>
      </div>
      </div> <!-- /container -->  
      <div id="about">
        <p>“You Are Here” is an experimental journalism-distribution network that leverages small, inexpensive, open-source wireless routers to deliver compelling, location-specific content to communities around New York. Starting with a series of high-quality audio pieces that reflect the unique culture and history of the people, politics and communities of the geographic area, the “You Are Here” servers can also act as a kind of digital town square where those nearby can exchange ideas, stories and information. The fact that these servers are not connected to the Internet allows them to accumulate a genuinely local character, in addition to serving as a safe, resilient means of exchanging digital information.</p>
        <p>This is a research project of the Tow Center for Digital Journalism, with fellows Sarah Grant, Susan McGregor, Benjamen Walker, Dan Phiffer, and Amelia Marzec.</p> 
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
