$( document ).ready( function() {

	console.log("document is ready!");

  /* API CALLS />
    https://phiffer.org/youarehere/yah.php?get=stories
    https://phiffer.org/youarehere/yah.php?get=responses&story=[story ID]
    https://phiffer.org/youarehere/yah.php?get=mp3s
    https://phiffer.org/youarehere/yah.php?twilio=1 (Twilio POST endpoint)
  */

  $.get( "https://phiffer.org/youarehere/yah.php?get=stories", function( data ) {
    console.log( "Load was performed." );
    // lay out thumbnails based on returned data
  });

  // $.ajax({
  //     type: "GET",
  //     dataType: "jsonp",
  //     url: "https://phiffer.org/youarehere/yah.php?get=stories",
  //     crossDomain: true
  // })
  // .done(function( data ) {
  //   console.log( "Load was performed: " + data );
  // })
  // .fail( function(xhr, textStatus, errorThrown) {
  //   console.log("xhr responseText: " + xhr.responseText);
  //   console.log("textStatus: " + textStatus);
  // });

  $(".callback").on("click", function() {

    // $.get( "https://phiffer.org/youarehere/yah.php?twilio=1", function( data ) {
    //   console.log( "Load was performed: " + data );
    // });

    $.ajax({
        type: "POST",
        dataType: "jsonp",
        url: "https://phiffer.org/youarehere/yah.php?twilio=1",
        crossDomain : true
    })
    .done(function( data ) {
      console.log( "Load was performed: " + data );
    })
    .fail( function(xhr, textStatus, errorThrown) {
      console.log("xhr responseText: " + xhr.responseText);
      console.log("textStatus: " + textStatus);
    });

  });

  $(".thumbnail .btn").on("click", function() {

    $.get( "https://phiffer.org/youarehere/yah.php?get=responses&story=1", function( data ) {
      console.log( "Load was performed: " + data );
    });
  });

	// Twilio.Device.ready(function (evice) {
 //    $("#log").text("Ready");
 //  });d
       
 //  Twilio.Device.error(function (error) {
 //    $("#log").text("Error: " + error.message);
 //  });
       
 //  Twilio.Device.connect(function (conn) {
 //    $("#log").text("Successfully established call");
 //  });
       
 //  Twilio.Device.disconnect(function (conn) {
 //    $("#log").text("Call ended");
 //  });
       
 //  Twilio.Device.incoming(function (conn) {
 //    $("#log").text("Incoming connection from " + conn.parameters.From);
 //    // accept the incoming connection and start two-way audio
 //    conn.accept();
 //  });
       
 //  function call() {
 //    Twilio.Device.connect();
 //  }
       
 //  function hangup() {
 //    Twilio.Device.disconnectAll();
 //  }

 //  $(".call").on("click", function() {
 //    call();
 //  });

 //  $(".hangup").on("click", function() {
 //    hangup();
 //  });

} );