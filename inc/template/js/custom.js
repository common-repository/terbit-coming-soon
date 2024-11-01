jQuery.noConflict();
	
var emailEntered;

	
jQuery(document).ready(function() {
	var timer = jQuery("#timer").val();
	
	/* countdown timer call */
	jQuery("#countdown").countdown({
	
		date: timer,
		format: "on"
	},
	
	function() {
		/* callback function */
	});	
	
	
	/* entire block clickable for header contact box(present in all interior pages) */	
	jQuery(".social-media li").click(function(){
    	window.location=jQuery(this).find("a").attr("href");
		return false;
	});
	


	/* newsletter script */
	jQuery("#newslettersubmit").click(function() {
			jQuery(".error").hide();
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			var emailaddressVal = jQuery("#email-address").val();
			
			if(emailaddressVal == 'Enter your email address') { 
				emailaddressVal = ''; 
			}
			
			//alert(emailaddressVal);
			
			if(emailaddressVal == '') {
				jQuery(".newsletter-message").html('<div class="newsletter-note n-err fade in"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter an email address before submitting.</div>');
				return false; 
			}
			else if(!emailReg.test(emailaddressVal)) {
				jQuery(".newsletter-message").html('<div class="newsletter-note n-err fade in"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid email address.</div>');
				return false; 
			} 
			else {
				emailEntered = escape(jQuery('#email-address').val());
			}

	});
	
	jQuery('#newsletterform').submit(function() {
		jQuery(".newsletter-message").html('<div class="newsletter-note n-conf fade in"><button type="button" class="close" data-dismiss="alert">&times;</button>Adding your email address...</div>');
		jQuery.ajax({
			url: 'includes/store-address.php', // proper url to your "store-address.php" file
			data: 'ajax=true&email=' + emailEntered,
			success: function() {
				jQuery('.newsletter-message').html('<div class="newsletter-note n-conf fade in"><button type="button" class="close" data-dismiss="alert">&times;</button>Congratulations! Check your email for confirmation of your subscription.</div>');
			}
		});
		return false;
	});

		
		
});/* end of document ready */





/* countdown timer script */
(function($) {
	$.fn.countdown = function(options, callback) {

		//custom 'this' selector
		thisEl = $(this);

		//array of custom settings
		var settings = { 
			'date': null,
			'format': null
		};

		//append the settings array to options
		if(options) {
			$.extend(settings, options);
		}
		
		//main countdown function
		function countdown_proc() {
			
			eventDate = Date.parse(settings['date']) / 1000;
			currentDate = Math.floor($.now() / 1000);
			
			if(eventDate <= currentDate) {
				callback.call(this);
				clearInterval(interval);
			}
			
			seconds = eventDate - currentDate;
			
			days = Math.floor(seconds / (60 * 60 * 24)); //calculate the number of days
			seconds -= days * 60 * 60 * 24; //update the seconds variable with no. of days removed
			
			hours = Math.floor(seconds / (60 * 60));
			seconds -= hours * 60 * 60; //update the seconds variable with no. of hours removed
			
			minutes = Math.floor(seconds / 60);
			seconds -= minutes * 60; //update the seconds variable with no. of minutes removed
			
			//conditional Ss
			if (days == 1) { thisEl.find(".timeRefDays").text("day"); } else { thisEl.find(".timeRefDays").text("days"); }
			if (hours == 1) { thisEl.find(".timeRefHours").text("hour"); } else { thisEl.find(".timeRefHours").text("hours"); }
			if (minutes == 1) { thisEl.find(".timeRefMinutes").text("minute"); } else { thisEl.find(".timeRefMinutes").text("minutes"); }
			if (seconds == 1) { thisEl.find(".timeRefSeconds").text("second"); } else { thisEl.find(".timeRefSeconds").text("seconds"); }
			
			//logic for the two_digits ON setting
			if(settings['format'] == "on") {
				days = (String(days).length >= 2) ? days : "0" + days;
				hours = (String(hours).length >= 2) ? hours : "0" + hours;
				minutes = (String(minutes).length >= 2) ? minutes : "0" + minutes;
				seconds = (String(seconds).length >= 2) ? seconds : "0" + seconds;
			}
			
			//update the countdown's html values.
			if(!isNaN(eventDate)) {
				thisEl.find(".days").text(days);
				thisEl.find(".hours").text(hours);
				thisEl.find(".minutes").text(minutes);
				thisEl.find(".seconds").text(seconds);
			} else { 
				alert("Invalid date. Here's an example: 12 Tuesday 2012 17:30:00");
				clearInterval(interval); 
			}
		}
		
		//run the function
		countdown_proc();
		
		//loop the function
		interval = setInterval(countdown_proc, 1000);
		
	}
}) (jQuery);


