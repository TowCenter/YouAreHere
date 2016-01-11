$( document ).ready( function() {

	console.log("document is ready!");

	Twilio.Device.ready(function (device) {
              $("#log").text("Ready");
            });
       
            Twilio.Device.error(function (error) {
              $("#log").text("Error: " + error.message);
            });
       
            Twilio.Device.connect(function (conn) {
              $("#log").text("Successfully established call");
            });
       
            Twilio.Device.disconnect(function (conn) {
              $("#log").text("Call ended");
            });
       
            Twilio.Device.incoming(function (conn) {
              $("#log").text("Incoming connection from " + conn.parameters.From);
              // accept the incoming connection and start two-way audio
              conn.accept();
            });
       
            function call() {
              Twilio.Device.connect();
            }
       
            function hangup() {
              Twilio.Device.disconnectAll();
            }

  $(".call").on("click", function() {
    call();
  });

  $(".hangup").on("click", function() {
    hangup();
  });

} );