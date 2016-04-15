(function ($) {

  $( document ).ready( function() {

    console.log("document is ready!");

    /* API CALLS />
      https://phiffer.org/youarehere/yah.php?get=stories
      https://phiffer.org/youarehere/yah.php?get=responses&story=[story ID]
      https://phiffer.org/youarehere/yah.php?get=mp3s
      https://phiffer.org/youarehere/yah.php?twilio=1 (Twilio POST endpoint)
    */

    const PH_NUM = "+16194314373"; // pull this from config file

    // fetch story
    function fetchStory() {
      $.ajax({
          type: "GET",
          dataType: "json",
          url: "/server/yah.php?get=stories"
      })
      .done(function( data ) {

        story = data.stories[PH_NUM];
        renderStory(story);

        // now get people's responses
        fetchResponses(story);

      })
      .fail( function(xhr, textStatus, errorThrown) {
        console.log("xhr responseText: " + xhr.responseText);
        console.log("textStatus: " + textStatus);
      });
    }

    function fetchResponses(story) {
      $.ajax({
          type: "GET",
          dataType: "json",
          url: "/server/yah.php?get=responses&story="+story.id
      })
      .done(function( data ) {

        // Call a function that will turn that data into HTML.
        renderResponses(data.responses);

        // Manually trigger a hashchange to start the app.
        $(window).trigger('hashchange');
      })
      .fail( function(xhr, textStatus, errorThrown) {
        console.log("xhr responseText: " + xhr.responseText);
        console.log("textStatus: " + textStatus);
      });
    }

    function show(url) {

      var view = url.split('/')[0];

      // Hide current page
      $('.current').removeClass('current visible');

      switch( view ) {
        // Homepage.
        case '': 
          $('.story').addClass('current visible');
          $('.responses').addClass('current visible');
          break;

        // About
        case '#about':
          $('.about').addClass('current visible');
          break;

        // 404 Not Found / Error
        default:
          $('.error').addClass('current visible');
      }

    }

    function renderStory(story){

      var $story = $('.story');
      var tmplScript = $("#story-template").html();
      var tmpl = Handlebars.compile(tmplScript);
      $story.append( tmpl(story) );

    }

    function renderResponses(data){
      // Uses Handlebars to create a list of responses using the provided data.
      // This function is called only once on page load.
      var list = $('.responses .responses-list');

      var tmplScript = $("#responses-template").html();
      var tmpl = Handlebars.compile(tmplScript);
      list.append( tmpl(data) );

      // Each products has a data-index attribute.
      // On click change the url hash to open up a preview for this product only.
      // Remember: every hashchange triggers the render function.
      list.find('.item').on('click', function (e) {

        e.preventDefault();

        toggleAudio( $(this).find('audio').get(0), $(this).find('.btn-audio') );

      });

      // hide loading message
      $('.loading').removeClass('visible');
      $('.responses').addClass('current visible');
    }

    // audio controls
    function toggleAudio(audio, btn) {

      if (audio.paused) {

        audio.addEventListener('ended', function() {
          onAudioEnd(audio, btn);
        });

        audio.play();

        $(btn).removeClass('play')
              .addClass('pause');

      } else { 

        onAudioEnd(audio, btn);
      }
    }

    function onAudioEnd(audio, btn) {
    
      audio.removeEventListener('ended', onAudioEnd);

      audio.pause();

      audio.currentTime = 0;

      $(btn).removeClass('pause')
            .addClass('play');
    }

    // callbacks
    $(window).on('hashchange', function(){
      
      show(decodeURI(window.location.hash));
    });

    fetchStory();


    
    // $(".callback").on("click", function() {

    //   $.ajax({
    //       type: "POST",
    //       dataType: "jsonp",
    //       url: "https://phiffer.org/youarehere/yah.php?twilio=1",
    //       crossDomain : true
    //   })
    //   .done(function( data ) {
    //     console.log( "Load was performed: " + data );
    //   })
    //   .fail( function(xhr, textStatus, errorThrown) {
    //     console.log("xhr responseText: " + xhr.responseText);
    //     console.log("textStatus: " + textStatus);
    //   });

    // });

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

}($));