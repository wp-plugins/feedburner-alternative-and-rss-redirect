<?php
define('SFM_CONNECT_LINK','http://www.specificfeeds.com/?');
define(SFM_BETTER_FEED,"http://www.specificfeeds.com/feedburner-alternative");
define(SFM_MAIN_FEED,get_bloginfo('rss2_url'));
$sfmRedirectObj=new sfmRedirectActions();
$feeds_data=$sfmRedirectObj->sfmListActiveRss();
/* main button classes */
$mainfeed_data=$sfmRedirectObj->sfmCheckActiveMainRss();
/* check for feedburner */

 $maintn_cls="process_butt_large  activate_redirect";
 $main_title="Click here to activate redirect";
 $main_text="Click here to activate redirect";
 $show_box1="none";
 $show_box2="none";
 $show_revers=false;
 $main_sub_url="javascript:void(0);";

 $comment_cls="activate_redirect";
 $comment_title="Activate Redirect";
 $comment_text="Activate Redirect";
 $comment_box=false;
 $comment_sub_url="javascript:void(0);";
 $comment_feed_url=$feeds_data['comment_url'];
 $comment_revers=false;

foreach ($mainfeed_data as $feedData) :
       if($feedData['feed_type'] == "main_rss")
       {
         $maintn_cls="process_butt_large  sfrd_finish";
         $main_title="Redirect is active!";
         $main_text="Redirect is active!";
         $main_sub_url=$feedData['feed_url'];
         $show_revers=true;
         if($sfmRedirectObj->sfm_CheckFeedBurner())
         {
          $show_box2="block";
          $show_box1="none";
         }
         else
         {
          $show_box2="none";
          $show_box1="block";
         }
         
         $main_feedId=$feedData['sf_feedid'];
         
       }
       
       if($feedData['feed_type'] =="comment_rss")
       {
         $comment_cls="sfrd_redirect_active";
         $comment_title="Redirect is active!";
         $comment_text="Redirect is active!";
         $comment_box=true;
         $comment_sub_url=$feedData['feed_url'];
         $comment_feed_url=$feedData['feed_url'];
         $comment_revers=true;
         $comment_feedId=$feedData['sf_feedid'];
       }
       
endforeach;



//print_r($feeds_data);
?>

<!-- main admin section area -->

<div class="sfrd_wapper">
	<div class="sfrd_wapper_conatnt">	
        <h2>Welcome to the SpecificFeeds RSS Redirect plugin</h2>
        <p>This plugin takes care of all your RSS redirects so that you can use SpecificFeeds (which is also the <a href="<?php echo SFM_BETTER_FEED; ?>" title="<?php echo SFM_BETTER_FEED; ?>" target="_new"><strong>better Feedburner</strong></a>).</p>
        <p>Click on the button below to activate the redirect of your main RSS feed (<a href="<?php echo SFM_MAIN_FEED; ?>" title="<?php echo SFM_MAIN_FEED; ?>" target="_new"><?php echo SFM_MAIN_FEED; ?></a>) to your feed on SpecificFeeds. <strong>You can always reverse it again</strong>.</p>        	
        <div class="sfrd_wapper_large_button_main">
        <div class="sfrd_wapper_large_button">
               <a href="javascript:void(0);" id="main_rss" red_type="main_rss" title="<?php echo $main_title; ?>"  class="<?php echo $maintn_cls; ?>"><?php echo $main_text; ?></a>
               <a href="<?php echo $main_sub_url; ?>" target="_new" style="display: <?php echo ($show_revers)? "block" : 'none' ?>" title="Open the new feed " class="open_new_feed">Open the new feed</a>
               <a href="javascript:void(0);" style="display: <?php echo ($show_revers)? "block" : 'none' ?>" red_type="main_rss" feed_id="<?php echo $main_feedId; ?>" title="Reverse redirect" class="reverse_redirect">Reverse redirect</a>
        </div>
        </div>
         <!-- activation error box -->
         <div class="sfrd_green_box1 sfrd_three sfm_error" style="display: none;">
            <p></p>        	
        </div> <!-- activation error box -->
        
        <!-- activation green box -->
        <div class="sfrd_green_box1 sfrd_one sfm_box_main_box1" style="display: <?php echo $show_box1; ?>">
            <p>If you're a former Feedburner user make sure to also <strong>redirect your Feedburner feed</strong> to your original feed. <strong class="inc_pop" style="cursor: pointer;"><u>Need instructions?</u></strong></p>
            <p>We also suggest that you connect your feed to an account on SpecificFeeds (it's FREE):</p>
        	<ul>
                	<li>You'll be able to <strong>import email subscribers</strong> (important if you had Feedburner email subscribers) </li>
                    <li>You'll get <strong>access to enlightening statistics</strong></li>
                    <li>You'll get <strong>listed in our blog directory</strong> - getting you more readers!</li>
                </ul>
            <a href="<?php if($main_feedId) echo SFM_CONNECT_LINK.base64_encode("userprofile=wordpress&feed_id=".$main_feedId); ?>" target="_new" id="mainRssconnect" title="Connect feed to a SpecificFeeds account">Connect feed to a SpecificFeeds account</a>
            
	</div>
        
         <div class="sfrd_green_box sfrdFeedBurnerBox" style="display:  <?php echo $show_box2; ?>">
        	<ul>
            	<li>
                <div class="sfrd_list_number">1</div>
                <div class="sfrd_list_contant"><span>Insert the new subscription form</span>
                We noticed you're using a <em>Feedburner subscription form</em> on your website. If you want to continue to use a form you need to insert the new form. Go to <strong onclick="window.location='widgets.php'" style="cursor: pointer;"><u>widgets</u></strong> and drag &amp; drop it to your sidebar.</div>
                </li>
                <li>
                <div class="sfrd_list_number">2</div>
                <div class="sfrd_list_contant"><span>Redirect your Feedburner feed</span>
                Some of your subscribers may have an url like &ldquo;http://feeds.feedburner.com/yourblog&rdquo; in their feed readers. To fix this, follow <strong class="inc_pop"><u>these instructions.</u></strong></div>
                </li>
                <li>
                <div class="sfrd_list_number">3</div>
                <div class="sfrd_list_contant"><span>Connect your feed to a SpecificFeeds account</span>
                We also suggest that you connect your feed to an account on SpecificFeeds (it's FREE):</div>
                <ul>
                	<li>You'll be able to <strong>import email subscribers</strong> (important if you had Feedburner email subscribers)</li>
                    <li>You'll get <strong>access to enlightening statistics</strong></li>
                    <li>You'get <strong>listed in our blog directory</strong> - getting you more readers!</li>
                </ul>
                </li>
            </ul>
            <a href="<?php if($main_feedId) echo SFM_CONNECT_LINK.base64_encode("userprofile=wordpress&feed_id=".$main_feedId); ?>" target="_new" id="mainRssconnect" title="Connect feed to a SpecificFeeds account">Connect feed to a SpecificFeeds account</a>
        </div>
        <!-- end active green box -->
        
        
        <!-- all other active feeds section -->
        <p class="bottom_txt">You also seem to offer some secondary feeds - click on &ldquo;Activate Redirect&rdquo; to apply the redirect for those as well.</p>
        <div class="sfrd_feedmaster_main">
           
           <div class="sfrd_feedmaster_tab">
            	<h3>Comments feed<span><a href="<?php echo $comment_feed_url; ?>" title="<?php echo $comment_feed_url; ?>" target="_new"><?php echo $comment_feed_url; ?></a></span></h3>
                <small>
                 <a href="<?php echo $comment_sub_url; ?>"  target="_new" style="display: <?php echo ($comment_revers)? "block" : 'none' ?>" title="Open the new feed " class="open_new_feed1">Open the new feed</a>
                 <a href="javascript:void(0);" feed_id="<?php echo  $comment_feedId; ?>" red_type="comment_rss" style="display: <?php echo ($comment_revers)? "block" : 'none' ?>" red_type="comment_rss" title="Reverse redirect" class="reverse_redirect1">Reverse redirect</a>
               
                 <a  href="javascript:void(0);" id="comment_rss"  red_type="comment_rss"  class="<?php echo $comment_cls; ?>" title="Activate Redirect"><?php echo $comment_text; ?></a></small>
                <div class=" clear"></div>
            </div>
           <?php if($comment_box) : $feed_connect=SFM_CONNECT_LINK.base64_encode("userprofile=wordpress&feed_id=".$comment_feedId);  include(SFM_DOCROOT."/views/sfm_pop1.php"); endif; ?>
           
           
          <!-- all other active categories feeds section -->
          <?php foreach($feeds_data['categoires'] as $fcat_data)  :
               $activeFdata=$sfmRedirectObj->sfmGetRssDetail(array("category_rss",$fcat_data->cat_ID));
            if(!empty($activeFdata)) : 
          ?>
            <div class="sfrd_feedmaster_tab">
            	<h3>Category &ldquo;<?php echo $fcat_data->cat_name;?>&rdquo; feed<span><a href="<?php echo $activeFdata->feed_url; ?>" title="<?php  echo $activeFdata->feed_url;  ?>" target="_new" id="cat_<?php echo $fcat_data->cat_ID; ?>"><?php  echo $activeFdata->feed_url;  ?></a></span></h3>                
                <small>
                 <a href="<?php echo $activeFdata->feed_url; ?>" target="_new" style="display: block" title="Open the new feed " class="open_new_feed1">Open the new feed</a>
                 <a href="javascript:void(0);" style="display:block" title="Reverse redirect" red_type="category_rss"  feed_id="<?php echo $activeFdata->sf_feedid; ?>" class="reverse_redirect1">Reverse redirect</a>
                 <a href="javascript:void(0);" id="category_rss" red_type="category_rss"   rcat=" <?php echo $fcat_data->cat_ID; ?> " class="sfrd_redirect_active" title="Redirect is active!">Redirect is active!</a>
                </small>
                <div class=" clear"></div>
            </div>
            <?php $feed_connect=SFM_CONNECT_LINK.base64_encode("userprofile=wordpress&feed_id=".$activeFdata->sf_feedid);  include(SFM_DOCROOT."/views/sfm_pop1.php"); ?>
            <?php else : ?>
             <div class="sfrd_feedmaster_tab">
            	<h3>Category &ldquo;<?php echo $fcat_data->cat_name;?>&rdquo; feed<span><a href="<?php echo get_category_feed_link($fcat_data->cat_ID); ?>" title="<?php echo get_category_feed_link($fcat_data->cat_ID); ?>" target="_new" id="cat_<?php echo $fcat_data->cat_ID; ?>"><?php echo get_category_feed_link($fcat_data->cat_ID); ?></a></span></h3>                
                <small>
                 <a href="" target="_new" style="display: none;" title="Open the new feed " class="open_new_feed1">Open the new feed</a>
                 <a href="javascript:void(0);" style="display: none;" red_type="category_rss" title="Reverse redirect" class="reverse_redirect1">Reverse redirect</a>
                 <a href="javascript:void(0);" id="category_rss" red_type="category_rss"  rcat=" <?php echo $fcat_data->cat_ID; ?> " class="activate_redirect" title="Activate Redirect">Activate Redirect</a>
                </small>
                <div class=" clear"></div>
            </div>
            
            <?php endif; ?>
            
            <?php endforeach; ?>  <!-- END all other active categories feeds section -->
           
            <!-- all other active author feeds section -->
             <?php foreach($feeds_data['authors'] as $fauth_data)  :
               $activeFdata=$sfmRedirectObj->sfmGetRssDetail(array("author_rss",$fauth_data['post_author']));
               if(!empty($activeFdata)) : 
             ?>
            <div class="sfrd_feedmaster_tab">
            	<h3>Author &ldquo;<?php echo $fauth_data['user_login']; ?>&rdquo; feed<span><a href="<?php echo $activeFdata->feed_url; ?>" title="<?php echo $activeFdata->feed_url; ?>" target="_new" id="author_<?php echo $fauth_data['post_author']; ?>"><?php echo $activeFdata->feed_url; ?></a></span></h3>
                <small>
                 <a href="<?php echo $activeFdata->feed_url; ?>" target="_new" style="display: block" title="Open the new feed " class="open_new_feed1">Open the new feed</a>
                 <a href="javascript:void(0);" style="display:block" title="Reverse redirect" red_type="author_rss" feed_id="<?php echo $activeFdata->sf_feedid; ?>" class="reverse_redirect1">Reverse redirect</a>
                 <a href="javascript:void(0);" id="author_rss" red_type="author_rss" rauthor="<?php echo $fauth_data['post_author']; ?>" class="sfrd_redirect_active" title="Redirect is active!">Redirect is active!</a>

                </small>
                <div class=" clear"></div>
            </div>
             <?php $feed_connect=SFM_CONNECT_LINK.base64_encode("userprofile=wordpress&feed_id=".$activeFdata->sf_feedid);  include(SFM_DOCROOT."/views/sfm_pop1.php"); ?>
            <?php else : ?>
            <div class="sfrd_feedmaster_tab">
            	<h3>Author &ldquo;<?php echo $fauth_data['user_login']; ?>&rdquo; feed<span><a href="<?php echo get_author_feed_link($fauth_data['post_author']); ?>" title="<?php echo get_author_feed_link($fauth_data['post_author']); ?>" target="_new" id="author_<?php echo $fauth_data['post_author']; ?>"><?php echo get_author_feed_link($fauth_data['post_author']); ?></a></span></h3>
                <small>
                 <a href="" target="_new" style="display: none;" title="Open the new feed " class="open_new_feed1">Open the new feed</a>
                 <a href="javascript:void(0);" style="display: none;" red_type="author_rss" title="Reverse redirect" class="reverse_redirect1">Reverse redirect</a>
                 <a href="javascript:void(0);" id="author_rss" red_type="author_rss" rauthor="<?php echo $fauth_data['post_author']; ?>" class="activate_redirect" title="Activate Redirect">Activate Redirect</a>
                </small>
                <div class=" clear"></div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?><!-- END  other active author feeds section -->
           
            <!-- Custom feeds section -->
            <?php  $CustomFdata=$sfmRedirectObj->sfmGetCustomFeeds(); ?>
           <?php $cnt=0; foreach($CustomFdata as $customfdata)  : ?>
           <div class="sfrd_feedmaster_tab sfm_customFeeds">
            	<h3>&ldquo;Custom feed <?php echo $cnt+1; ?>&rdquo; <span><a href="<?php echo $customfdata->feed_url; ?>" title="<?php echo $customfdata->feed_url; ?>" target="_new" id="custom_<?php echo $customfdata->sf_feedid; ?>"><?php echo $customfdata->feed_url; ?></a></span></h3>
                <small>
                 <a href="<?php echo $customfdata->feed_url; ?>" target="_new" style="display: block" title="Open the new feed " class="open_new_feed1">Open the new feed</a>
                 <a href="javascript:void(0);" style="display:block" title="Reverse redirect" red_type="custom_rss" feed_id="<?php echo $customfdata->sf_feedid; ?>" class="reverse_redirect1">Reverse redirect</a>
                 <a href="javascript:void(0);" id="custom_rss" red_type="custom_rss" class="sfrd_redirect_active" title="Redirect is active!">Redirect is active!</a>

                </small>
                <div class=" clear"></div>
            </div>
           <?php $feed_connect=SFM_CONNECT_LINK.base64_encode("userprofile=wordpress&feed_id=".$customfdata->sf_feedid);  include(SFM_DOCROOT."/views/sfm_pop1.php"); ?>
          <?php $cnt++; endforeach; ?>
           <input type="hidden" id="sfmCusCounter" value="<?php echo $cnt; ?>" />
        </div> <!-- END all other active feeds section -->
        
         
        <p class="bottom_txt">If you have any other feed for which you need a redirect please enter it below:</p>
        <div class="sfrd_feedmaster_add SFMcustomFeedLinks">
         <!-- list all custom links -->
         
            <div class="sfrd_feedmaster_add_row">
            	<input name="sfmcustom_link" id="sfmcustom_link" type="text" class="sfmCustomUrl"  placeholder="http://www.yourblog.com/feed-url"><a href="javascript:void(0);" id="custom_rss" red_type="custom_rss" class="activate_redirect" title="Activate Redirect">Activate Redirect</a>
            </div>
            <!-- activation error box -->
         <div class="sfrd_green_box1 sfrd_customError" style="display: none;" >
            <p></p>        	
        </div> <!-- activation error box -->
            <!--<div class="sfrd_feedmaster_add_another"><a href="javascript:void(0);" id="addCustomFeed" title="+ Add Another" class="sfrd_feedmaster_add_another">+ Add Another</a></div>-->
        </div><!-- END Custom feeds section -->
        
        <p class="sfrd_help"><a href="mailto:support@specificfeeds.com" title="Need help or have questions? Get in touch with us">Need help or have questions? Get in touch with us</a></p>
  </div>
</div><!-- END main admin section -->

<!-- instruction pop-up-->

<div class="sfrd_popup_overlay" style="display: none;"></div>
<div class="sfrd_popup" style="display: none;">
 <a href="javascript:void(0);" title="Close" class="sfrd_close close_incPopUp"><img src="<?php echo SFM_PLUGURL ?>images/close.jpg" alt="Close"></a>
	<div class="sfrd_popup_contant">
    	<h1>How do I redirect my Feedburner feed?</h1>
        <div class="sfrd_row">
       	  <div class="sfrd_left">
          <div class="sfrd_arrow"><img src="<?php echo SFM_PLUGURL ?>images/arrow1.png" alt=""></div>
			<div class="sfrd_contant_middle">
          <p>Go to your Feedburner account and select the feed you want to redirect.</p> 
<p>Make sure that in the &ldquo;edit feed details&rdquo; section you have entered the <strong>original feed</strong> as source.</p>
</div>
</div>
          <div class="sfrd_right"><img src="<?php echo SFM_PLUGURL ?>images/screen1.jpg" alt=""></div>
        </div>
        <div class="sfrd_row1">
        <div class="sfrd_right1"><img src="<?php echo SFM_PLUGURL ?>images/screen2.jpg" alt=""></div>
       	  <div class="sfrd_left1">
          <div class="sfrd_arrow1"><img src="<?php echo SFM_PLUGURL ?>images/arrow2.png" alt=""></div>
			<div class="sfrd_contant_middle1">
          <p>Then go to &ldquo;delete feed&rdquo; and check the box &ldquo;<strong>with permanent redirection</strong>&rdquo;.</p>
</div>
</div>
        </div>
        <p class="sfrd_bottom">All RSS-subscribers who subscribed to your Feedburner feed will now be redirected to your original feed, which in turn redirects to your feed on  <strong>SpecificFeeds.</strong></p>
        <a href="mailto:support@specificfeeds.com" title="Need help or have questions? Get in touch with us">Need help or have questions? Get in touch with us</a>
    </div>
</div><!-- END instruction pop-up-->
