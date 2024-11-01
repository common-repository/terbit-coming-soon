<?php 

$sc_jdt = get_option('squaretrix_comingsoon_options'); 

global $squaretrix_comingsoon;

?>

<!DOCTYPE html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->

<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->

<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->

<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]--><head>

  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title><?php

    bloginfo( 'name' );

    $site_description = get_bloginfo( 'description' );

    ?></title>

  <meta name="description" content="<?php echo esc_attr($site_description);?>">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Oswald:400&amp;subset=latin,latin-ext">



        <!-- load bootstrap css library -->

    <link rel="stylesheet" href="<?php echo plugins_url('template/css/bootstrap.css',dirname(__FILE__)); ?>"> 

    <link rel="stylesheet" href="<?php echo plugins_url('template/css/bootstrap-responsive.min.css',dirname(__FILE__)); ?>">       

    <!-- load template specific css styles -->

    <link rel="stylesheet" href="<?php echo plugins_url('template/css/styles.css',dirname(__FILE__)); ?>">  

     



<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>

<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.pack.js" type="text/javascript"></script>

<style type="text/css">

    body{
<?php if(($sc_jdt['comingsoon_no_background'])!=true):?>
        background: <?php echo $sc_jdt['comingsoon_custom_bg_color'];?> url('<?php echo (empty($sc_jdt['comingsoon_custom_bg_image']) ? plugins_url('template/images/bg.png',dirname(__FILE__)) : $sc_jdt['comingsoon_custom_bg_image']); ?>') repeat;
<?php else:?>
background-color:<?php echo $sc_jdt['comingsoon_custom_bg_color'];?> !important;
<?php endif;?>
        <?php if(!empty($sc_jdt['comingsoon_background_strech'])):?>

          background-repeat: no-repeat;

          background-attachment: fixed;

          background-position: top center;

          -webkit-background-size: cover;

          -moz-background-size: cover;

          -o-background-size: cover;

          background-size: cover;

		  

        <?php endif;?>

	}

<?php if($sc_jdt['comingsoon_font_color'] == 'white'):?>

    body{

        color:#fff;

        

    }

    <?php elseif($sc_jdt['comingsoon_font_color'] == 'gray'):?>

    body{

        color:#666;

        

    }

    <?php elseif($sc_jdt['comingsoon_font_color'] == 'black'):?>

    body{

        color:#000;

       

    }

    <?php endif;?>		

	#countdown li span {

	color: <?php echo $sc_jdt['comingsoon_theme_color'];?> !important;

	}

	#newslettersubmit:hover {

color: <?php echo $sc_jdt['comingsoon_theme_color'];?> !important;



}



#newslettersubmit {

background: <?php echo $sc_jdt['comingsoon_theme_color'];?> !important;

}



#newsletterform, #newsletterform p, .newsletter-info {



color: <?php echo $sc_jdt['comingsoon_theme_color'];?> !important;

}

a, a:hover {

color:<?php echo $sc_jdt['comingsoon_theme_color'];?> !important;	

}
.header-wrapper {
background: url(<?php echo (empty($sc_jdt['comingsoon_box_bg']) ? plugins_url('template/images/header_bg.png',dirname(__FILE__)) : plugins_url('template/images/header_bg'.$sc_jdt['comingsoon_box_bg'].'.png',dirname(__FILE__))); ?>) no-repeat top center;
}
	

</style>		

<?php  do_action( 'sc_head'); ?>



  </head>

    <!-- #start header section -->    

    <div class="header-wrapper">



        <div class="container">

        

        	<!-- logo -->

        	<div id="logo">

            	<a href="#"><img src="<?php echo (empty($sc_jdt['comingsoon_image']) ? plugins_url('template/images/logo.png',dirname(__FILE__)) : $sc_jdt['comingsoon_image']); ?>" alt="Coming Soon" /></a>

            </div><!-- Newsletter start -->

          <div class="cs-title">

            	<h2><?php echo $sc_jdt['comingsoon_headline'] ?></h2>

                

            <ul id="countdown">

                <li>

                    <span class="days">00</span>

                    <div class="clearboth"></div>

                    <p class="timeRefDays capitalization">days</p>

                </li>

                <li>

                    <span class="hours">00</span>

                    <div class="clearboth"></div>

                    <p class="timeRefHours capitalization">hours</p>

                </li>

                <li>

                    <span class="minutes">00</span>                    

                    <div class="clearboth"></div>

                    <p class="timeRefMinutes capitalization">minutes</p>

                </li>

                <li>

                    <span class="seconds">00</span>

                    <div class="clearboth"></div>

                    <p class="timeRefSeconds capitalization">seconds</p>

                </li>

            </ul>

           

            	

                <div class="span8 newsletter-parent">

                

                <?php if(!empty($sc_jdt['comingsoon_customhtml'])): ?>

            

                <?php echo $sc_jdt['comingsoon_customhtml'] ?>

          

            <?php endif; ?>

            <?php if($sc_jdt['comingsoon_mailinglist'] == 'feedburner' && !empty($sc_jdt['comingsoon_feedburner_address'])): ?>

              <form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $sc_jdt['comingsoon_feedburner_address']; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true"  id="newsletterform">

                    <input type="hidden" value="<?php echo $sc_jdt['comingsoon_feedburner_address']; ?>" name="uri"/>

                    <input type="hidden" name="loc" value="en_US"/>

                    <input id="email-address" type="text" name="email" value="Enter your email address"

                            onfocus="if(this.value=='Enter your email address') {this.value='';}" onblur="if(this.value=='') {this.value='Enter your email address'}" />

                    

                    <input type="submit" id="newslettersubmit" name="sign-up" value="<?php _e('Sign up', 'terbit-coming-soon') ?>" />

          </form>

            <?php endif; ?>  

                

                    <span class="newsletter-info"> <?php echo $sc_jdt['comingsoon_form_notice'] ?></span>				

                </div>            	



            

            </div>

            

            <input type="hidden" id="timer" value="<?php echo(empty($sc_jdt['comingsoon_comstime']) ?'30 November 2013 12:00:00':$sc_jdt['comingsoon_comstime']); ?>" >

	        <!-- start countdown timer section -->            

            

        	<!-- end countdown timer section -->

            

        </div>



    </div>

    <!-- #end header section -->   

   



        <!-- #start top bar(phone and email) section -->

    <div class="top-bar">

    

    	<div class="container">

			 <?php if(!empty($sc_jdt['comingsoon_call_us'])): ?>Call us: <?php echo $sc_jdt['comingsoon_call_us'] ?> <img src="<?php echo plugins_url('template/images/call.png',dirname(__FILE__)); ?>" alt="">&nbsp;&nbsp; /<?php endif; ?> &nbsp;&nbsp;  <?php if(!empty($sc_jdt['comingsoon_email'])): ?>Email us : <a href="mailto:<?php echo $sc_jdt['comingsoon_email'] ?>"><?php echo $sc_jdt['comingsoon_email'] ?></a> <img src="<?php echo plugins_url('template/images/email.png',dirname(__FILE__)); ?>" alt="">  <?php endif; ?> 

        </div>

    </div>

    <!-- #end top bar(phone and email) section -->  

	<!-- #start main content section -->     

    <div class="main-content"><div class="centerblock">

        		<br>

            	<?php echo shortcode_unautop(wpautop(convert_chars(wptexturize($sc_jdt['comingsoon_description'])))) ?>

                

            </div>

    	<div class="container">



             

            <div class="row-fluid">

                             

                <!-- #start contact button section -->   

                     <h2 >STAY IN TOUCH WITH US!<br/>

                     <?php if(!empty($sc_jdt['comingsoon_facebook'])): ?><a href="<?php echo $sc_jdt['comingsoon_facebook'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/facebook.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                      <?php if(!empty($sc_jdt['comingsoon_twitter'])): ?><a href="<?php echo $sc_jdt['comingsoon_twitter'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/twitter.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                      <?php if(!empty($sc_jdt['comingsoon_customhtml'])): ?><a href="<?php echo $sc_jdt['comingsoon_email'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/email.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                      <?php if(!empty($sc_jdt['comingsoon_google_plus'])): ?><a href="<?php echo $sc_jdt['comingsoon_google_plus'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/google-plus.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                      <?php if(!empty($sc_jdt['comingsoon_pinterest'])): ?><a href="<?php echo $sc_jdt['comingsoon_pinterest'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/pinterest.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                      <?php if(!empty($sc_jdt['comingsoon_reddit'])): ?><a href="<?php echo $sc_jdt['comingsoon_reddit'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/reddit.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                      <?php if(!empty($sc_jdt['comingsoon_stumbleupon'])): ?><a href="<?php echo $sc_jdt['comingsoon_stumbleupon'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/stumbleupon.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                      <?php if(!empty($sc_jdt['comingsoon_rss'])): ?><a href="<?php echo $sc_jdt['comingsoon_rss'] ?>" class="classname" role="button" data-toggle="modal"><span><img src="<?php echo plugins_url('template/images/rss-feeds.png',dirname(__FILE__)); ?>" alt=""> </span></a><?php endif; ?>

                     

                    

                     

                     </h2>

                 <!-- #end contact button section -->   

                 

            </div>

                     

        </div><!-- end of .container -->

    </div>

	<!-- #end main content section -->     

     

     

    



    

    <!-- load jquery library -->

    <script type="text/javascript" src="<?php echo plugins_url('template/js/jquery-191.min.js',dirname(__FILE__)); ?>"></script>

      

    <!-- load bootstrap library -->        

    <script type="text/javascript" src="<?php echo plugins_url('template/js/bootstrap.min.js',dirname(__FILE__)); ?>"></script>

    

    <!-- load custom scripts -->         

    <script type="text/javascript" src="<?php echo plugins_url('template/js/custom.js',dirname(__FILE__)); ?>"></script>

            

    <!-- load twitter library to display latest tweets --> 

    <script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>

    <script type="text/javascript" src="http://api.twitter.com/1/statuses/user_timeline/sreenubfa.json?callback=twitterCallback2&amp;count=3"></script>



    

</html>



<?php exit(); ?>