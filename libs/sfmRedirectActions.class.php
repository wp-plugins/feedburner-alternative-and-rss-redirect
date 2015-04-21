<?php
/* all redirect  opitons class */

class sfmRedirectActions
{
    public $sfm_ActivateRedirectUrl="http://www.specificfeeds.com/wordpress/redirect_plugin_setup";
    public $SFM_REDIRECTION_TABLE ="sfm_redirects";
    public $sfm_UpdateFeedsUrl="http://www.specificfeeds.com/wordpress/updatepermalink";
    public $SFM_CONNECT_LINK="http://www.specificfeeds.com/?";
    public $SFM_SETUP_URL='http://www.specificfeeds.com/rssegtcrons/download_rssmorefeed_data_single/';
    
    function __construct()
    {
        /* process activate redirection  */
	add_action('wp_ajax_ActRedirect',array(&$this,'sfmActivateRedirect'));
	
	/* reverse redirection */
	add_action('wp_ajax_sfmReverseRedirect',array(&$this,'sfmReverseRedirect'));
	
	/* process the feed messages action  */
	add_action('wp_ajax_sfmProcessFeeds',array(&$this,'sfmProcessFeeds'));
	
	/* update feed urls on permalink update */
	add_action( 'admin_init' , array(&$this,'sfmUpdateRedirectedUrls'));
	
	/* activate feed redirection */
	add_action('template_redirect', array(&$this,'sfm_feed_redirect'),10);
	
	/* delete authors feed if user get delete */
	add_action( 'delete_user',array(&$this,'sfmDeleteFeed') );
	
	/* delete category feed if a category get deleted */
	add_action( 'delete_term_taxonomy',array(&$this,'sfmDeleteCatFeed') );
	
	/* Load Header meta */
	add_action( 'wp_head',array(&$this,'sfmHeaderMeta') );    
    }
   
    /* List all RSS links */
    public function sfmListActiveRss()
    {
        /* get the comment feed url */
        $return_data=array();
        
       $comments_link=get_bloginfo('comments_rss2_url');
       $return_data['comment_url']=$comments_link;
        /* get categoires feed url */
        $cat_argu=array( 'type' => 'post',
                         'orderby' => 'name',
                         'order'   => 'ASC',   
                        );
        
        $wp_categoires=get_categories($cat_argu);
        $return_data['categoires']=$wp_categoires;
        /* get the authors */
        global $wpdb;
        $wp_authors=$wpdb->get_results('select DISTINCT p.post_author,u.user_login from '.$wpdb->prefix.'posts p LEFT JOIN '.$wpdb->prefix.'users u on p.post_author=u.ID where p.post_status="publish" and p.post_type="post"',ARRAY_A);       
        $return_data['authors']=$wp_authors;
	/* get the all custom active feeds */
		
        return $return_data;
        
    }
    
    /* return all active rss links */
    public function sfmCheckActiveMainRss()
    {
	global $wpdb;
	$get_feed=$wpdb->get_results('SELECT DISTINCT feed_type,feed_subUrl,sf_feedid,feed_url  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE.' WHERE redirect_status=1',ARRAY_A);
	return $get_feed; exit;
    }
     /* return all active rss links */
    public function sfmGetRssDetail($input_data)
    {
	global $wpdb;
	if(!empty($input_data[1]))
	{
	$qr=" and id_on_blog='".$input_data[1]."'" ;   
	}
	else
	{
	   $qr=''; 
	}
	
	$get_feed=$wpdb->get_row('SELECT *  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where feed_type='".$input_data[0]."' AND redirect_status=1 ".$qr);
	return $get_feed; exit;
    }
      /* return custom links */
    public function sfmGetCustomLink($input_data)
    {
	global $wpdb;
	$get_feed=$wpdb->get_row('SELECT *  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where blog_rss='".$input_data[0]."' and feed_type='custom_rss' AND redirect_status=1 ");
	return $get_feed; exit;
    }
     /* return all active rss links by SF feedId */
    public function sfmGetRssDetailByFeed($input_data)
    {
	global $wpdb;
	$get_feed=$wpdb->get_row('SELECT *  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where sf_feedid='".$input_data[0]."' AND redirect_status=1");
	return $get_feed; exit;
    }
    /* return all custom feed links */
    
    public function sfmGetCustomFeeds()
    {
	global $wpdb;
	$get_feeds=$wpdb->get_results('SELECT *  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where feed_type='custom_rss' AND redirect_status=1");
	return $get_feeds; exit;
    }
    /* fetch the feed url from specificfeeds.com */ 
    public function sfmActivateRedirect()
    {
	global $wpdb;
	/* check for the feed type */
	$url=$this->sfm_ActivateRedirectUrl;
	if(!empty($_POST['rtype']))
	{
	    $data=array('subscriber_type'=>'RWP','web_url'=>get_bloginfo('url'));
	    $blog_url='';
	    switch($_POST['rtype'])
	    {
		case "main_rss" : 	$web_url=html_entity_decode(get_bloginfo('rss2_url'));
					$data['feed_url']=$web_url;
					    
		break;		     
		case "comment_rss" : 	$web_url=html_entity_decode(get_bloginfo('comments_rss2_url'));
					$data['feed_url']=$web_url;
		break;    
		case "custom_rss" :  	$web_url=(isset($_POST['curl']) && !empty($_POST['curl'])) ? trim($_POST['curl']) :'';
					if(!filter_var($web_url, FILTER_VALIDATE_URL))
					{
					    echo json_encode(array('response'=>"invaild_url")); exit;
					}
					if(strpos($web_url,$_SERVER["SERVER_NAME"])<=0)
					{
					     echo json_encode(array('response'=>"diff_url")); exit;
					}
					$data['feed_url']=$web_url;
					$blog_url=$web_url;
		break;
		case "category_rss" : 	$web_url=html_entity_decode(get_category_feed_link(trim($_POST['record_id'])));
					$cat_data=get_category(trim($_POST['record_id']));	
					$data['feed_url']=$web_url;
					$data['category']=trim($cat_data->slug);
		break;
		case "author_rss"   : 	$web_url=html_entity_decode(get_author_feed_link( trim($_POST['record_id'])));
					$user_details=get_userdata (trim($_POST['record_id']));
					$user_name=$user_details->user_login;
					$data['feed_url']=$web_url;
					$data['author']=trim($user_name);
		break;			
	    }
	    /* check for a valid feedurl and avoid the local servers*/
	   /*if($_SERVER['SERVER_NAME']!='localhost') : 	
	   $check_data=$this->sfmValidateFeed($data['feed_url']);
	   if(!$check_data)
	   {
	    echo json_encode(array('response'=>"wrong_feedUrl")); exit;
	   }
	   endif; */
	   
	    $data['feed_url'] = add_query_arg( array('bypass' => 'sfm'), $data['feed_url'] );
	   /* check for feedburner form */
	   if($_POST['rtype']=="main_rss")
	   {
		$isFeedBurner= $this->sfm_CheckFeedBurner(); 
	   }
	   else{
		$isFeedBurner= 0; 
	   }
	    /* send request to speficifeeds.com */
	  
	    $respons=$this->sfm_ProcessRequest($url,$data);
	   	    
	    /* update database on the base of response */
	    if($respons->response=="success" || $respons->response=="exist" )
	    {
		$respons->connect_string=$this->SFM_CONNECT_LINK.base64_encode("userprofile=wordpress&feed_id=".$respons->feed_id);
		if($respons->response=="success")
		{
		$record_id=(isset($_POST['record_id']) && !empty($_POST['record_id']))? $_POST['record_id'] : '';
		$re_data=array('sf_feedid'=>$respons->feed_id,
			       'id_on_blog'=> $record_id,
			       'blog_rss'=>$blog_url,
			       'feed_type'=>$_POST['rtype'],
			       'feed_url'=>$respons->feed_url,
			       'feed_subUrl'=>$respons->redirect_url,
			       'verification_code'=>$respons->code,
			       'redirect_status'=>1
			       );
		$format=array('%d','%d','%s','%s','%s','%s','%s','%d');
		$wpdb->insert($wpdb->prefix.$this->SFM_REDIRECTION_TABLE, $re_data, $format );
		$request_data=array('rq_type'=>$_POST['rtype'],'record_id'=>$record_id,'isfeed'=>$isFeedBurner);
		echo json_encode(array('response'=>"success",'res_data'=>$respons,'request_data'=>$request_data)); exit;
		}
		if($respons->response=="exist")
		{
		    $get_feed=$wpdb->get_row('SELECT * from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE.' where sf_feedid="'.$respons->feed_id.'"');
		    if(!empty($get_feed) && $get_feed->redirect_status==1)
		    {
			  echo json_encode(array('response'=>"exists_url")); exit;
			
		    }elseif( !empty($get_feed) && $get_feed->redirect_status==0)
		    {
			/* update the new values to data base */
			
			$record_id=(isset($_POST['record_id']) && !empty($_POST['record_id']))? $_POST['record_id'] : '';
			$re_data=array('id_on_blog'=> $record_id,
			       'blog_rss'=>$blog_url,
			       'feed_type'=>$_POST['rtype'],
			       'feed_url'=>$respons->feed_url,
			       'feed_subUrl'=>$respons->redirect_url,
			       'verification_code'=>$respons->code,
			       'redirect_status'=>1
			       );		
			$format=array('%d','%d','%s','%s','%s','%s','%s','%d');
			$where_data=array('sf_feedid'=>$respons->feed_id);
			$where_format=array('%d');
			$res=$wpdb->update( $wpdb->prefix.$this->SFM_REDIRECTION_TABLE, $re_data,$where_data, $format , $where_format);
			
			$request_data=array('rq_type'=>$_POST['rtype'],'record_id'=>$record_id,'isfeed'=>$isFeedBurner);
			echo json_encode(array('response'=>"success",'res_data'=>$respons,'request_data'=>$request_data)); exit; 
			
		    }
		    else{
			
			$record_id=(isset($_POST['record_id']) && !empty($_POST['record_id']))? $_POST['record_id'] : '';
			$re_data=array('sf_feedid'=>$respons->feed_id,
			       'id_on_blog'=> $record_id,
			       'blog_rss'=>$blog_url,
			       'feed_type'=>$_POST['rtype'],
			       'feed_url'=>$respons->feed_url,
			       'feed_subUrl'=>$respons->redirect_url,
			       'verification_code'=>$respons->code,
			       'redirect_status'=>1
			       );
			$format=array('%d','%d','%s','%s','%s','%s','%s','%d');
			$wpdb->prefix.$this->SFM_REDIRECTION_TABLE;
			$res=$wpdb->insert($wpdb->prefix.$this->SFM_REDIRECTION_TABLE, $re_data, $format);
			$request_data=array('rq_type'=>$_POST['rtype'],'record_id'=>$record_id,'isfeed'=>$isFeedBurner);
			echo json_encode(array('response'=>"success",'res_data'=>$respons,'request_data'=>$request_data)); exit;
		    }
		
		} /* end of respons output */
		
	    }
	    else
	    {
		echo json_encode(array('response'=>"sf_error")); exit;
	    } /* end of respons condition */
	    
	    
	}
	else
	{
	    echo json_encode(array('response'=>"error")); exit;
	} /* end of post condition */
	
    } /* end sfmActivateRedirect() */
    
    /* reverse the redirection of feeds */
    public function sfmReverseRedirect()
    {
	global $wpdb;
	if(isset($_POST['feed_id']) && !empty($_POST['feed_id']))
	{
	     switch($_POST['feed_type'])
	    {
		case "main_rss" : 	$reverse_url=html_entity_decode(get_bloginfo('rss2_url'));
					
					    
		break;		     
		case "comment_rss" : 	$reverse_url=html_entity_decode(get_bloginfo('comments_rss2_url'));
					
		break;    
		case "custom_rss" :  	$reverse_url='';
		break;
		case "category_rss" :	$fdata= $this->sfmGetRssDetailByFeed(array($_POST['feed_id']));
					$reverse_url=html_entity_decode(get_category_feed_link(trim($fdata->id_on_blog)));
					
		break;
		case "author_rss"   : 	$fdata=$this->sfmGetRssDetailByFeed(array($_POST['feed_id']));
					$reverse_url=html_entity_decode(get_author_feed_link(trim($fdata->id_on_blog)));
		break;			
	    }
	    $wpdb->query('UPDATE '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE.' SET redirect_status=0 WHERE sf_feedid="'.$_POST['feed_id'].'"');
	     echo json_encode(array('response'=>"success",'feed_url'=>$reverse_url)); exit;
	}
	else
	{
	    echo json_encode(array('response'=>"error")); exit;
	}
	
	
    }/* end sfmReverseRedirect() */
    
    /* process the all request to outer server */
    private function sfm_ProcessRequest($url,$data)
    {
	$curl = curl_init();  
        curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $url,
			    CURLOPT_USERAGENT => 'sf rss activation request',
			    CURLOPT_POST => 1,
			    CURLOPT_POSTFIELDS => $data
			    ));
	/* Send the request & save response to $resp */
        $resp = curl_exec($curl);
        $resp=json_decode($resp);
        curl_close($curl);
        return $resp;exit;
    
    }/* end sfm_ProcessRequest() */
    
    /* check if the feedburner form is exists in text widget or not */
    public function sfm_CheckFeedBurner(){
	global $wpdb;
	$isFound="";
	$textWidgetDatas=get_option('widget_text');
	
	foreach($textWidgetDatas as $widget_data)
	{
	    if(strpos($widget_data['text'],'http://feedburner.google.com/fb/a/mailverify')>0 || strpos($widget_data['text'],'https://feedburner.google.com/fb/a/mailverify')>0)
	    {
		return $isFound=1; exit;
	    }
	    else{
		$isFound=0; 
	    }
	    
	    
	}
	return $isFound;
	
     }/* end sfm_CheckFeedBurner() */
     
     /* update feed data if permalink of website get changed */
     public function sfmUpdateRedirectedUrls()
     {
	global $wpdb;
	if(get_option('sfm_permalink_structure')!=get_option('permalink_structure'))
	{
	    $getFeedsData=$wpdb->get_results('SELECT *  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where feed_type!='custom_rss'");
	    foreach($getFeedsData AS $stored_feed)
	    {
		$data_array=array();
		switch($stored_feed->feed_type)
		{
		    case "main_rss" : 	$reverse_url=get_bloginfo('rss2_url');
					$data_array['feed_id']=$stored_feed->sf_feedid;
					$data_array['feed_url']= $reverse_url;   
		    break;		     
		    case "comment_rss" : $reverse_url=get_bloginfo('comments_rss2_url');
					 $data_array['feed_id']=$stored_feed->sf_feedid;
					 $data_array['feed_url']= $reverse_url;   
					    
		    break;    
		    case "category_rss" :  $reverse_url=get_category_feed_link(trim($stored_feed->id_on_blog));
					   $data_array['feed_id']=$stored_feed->sf_feedid;
					   $data_array['feed_url']= $reverse_url; 
		    break;
		    case "author_rss"   :  $reverse_url=get_category_feed_link(trim($stored_feed->id_on_blog));
					   $data_array['feed_id']=$stored_feed->sf_feedid;
					   $data_array['feed_url']= $reverse_url;
		    break; 			   
		}
		$data_array['feed_url'] = add_query_arg( array('bypass' => 'sfm'), $data_array['feed_url'] );
		$response=$this->sfm_ProcessRequest($this->sfm_UpdateFeedsUrl,$data_array);
	    }
	    add_action('admin_notices', array(&$this,'SFMPermaUpdateCustomMsg'));
	    update_option('sfm_permalink_structure', get_option('permalink_structure'));
	}
	
     }/* end sfmUpdateRedirectedUrls() */
     public function SFMPermaUpdateCustomMsg()
     {
	echo "<div class=\"update-nag\" >" . "<p ><b>There may be some issue comes with your custom Redirect feed under \"SpecificFeeds Feedmaster \" plugin, Please <a href='admin.php?page=sfm-options-page'>Re-activate</a> the redirection for custom links. </b></p></div>"; 
     }
     
     /* check for the valid url */
    public function sfmValidateFeed( $rssFeedURL ) {
	$rssValidator = 'http://feedvalidator.org/check.cgi?url=';
	     
	if( $rssValidationResponse = file_get_contents($rssValidator . urlencode($rssFeedURL)) ){
	if( stristr( $rssValidationResponse , 'This is a valid RSS feed' ) !== false ){
	    return true;
	} else {
	    return false;
	}
	} else {
	return false;
	}
    }/* end sfmValidateFeed() */
  
    /* redirect to SF */
    
    function sfm_feed_redirect() {
                global $wp, $wp_query,$feed;
			
		/* check for feed page */
		if(is_feed() &&  strpos($_SERVER['HTTP_USER_AGENT'], "Specificfeeds- http://www.specificfeeds.com" )<=0) :
		 $feed_type="custom";
		 if($this->sfmgetCurrentURL()== get_bloginfo('rss2_url')) :
		    $feed_type="main";
		 endif;
		if(isset($wp_query->query['withcomments']) || $wp_query->query['feed']=="comments-rss2" ) : 
		
		$withcomments=1;
		 $feed_type="comment";
		endif;
		if(isset($wp_query->query['category_name']) || isset($wp_query->query['cat']) || (isset($wp_query->query_vars['cat']) && !empty($wp_query->query_vars['cat']))) : 
		$category_id=$wp_query->query_vars['cat'];
		$feed_type="category";
		endif;
		if(isset($wp_query->query['author_name']) || isset($wp_query->query['author'])) : 
		$author_id=$wp_query->query_vars['author'];
		$feed_type="author";
		endif;
		$cus_data=$this->sfmGetCustomLink(array($this->sfmgetCurrentURL()));
		if(!empty($cus_data))
		{
		    $feed_type="custom";
		}
		//echo $feed_type;
		switch ($feed_type)
		{
		    case "main" 	:  $sfm_data=$this->sfmGetRssDetail(array('main_rss'));
					   $sfmurl = $sfm_data->feed_url;
		    break;
		    case 'comment'	:  $sfm_data=$this->sfmGetRssDetail(array('comment_rss'));
					   $sfmurl = $sfm_data->feed_url;
		    break;
		    case 'category'	:  $sfm_data=$this->sfmGetRssDetail(array('category_rss',$category_id));
					  
					   $sfmurl = $sfm_data->feed_url;
		    break;
		    case 'author'	:  $sfm_data=$this->sfmGetRssDetail(array('author_rss',$author_id));
					   $sfmurl = $sfm_data->feed_url;
				   
	            break;
		    case 'custom' 	:  $sfm_data=$this->sfmGetCustomLink(array($this->sfmgetCurrentURL()));
					   $sfmurl = $sfm_data->feed_url;
		    break;
		
		}
		if($sfmurl && function_exists('status_header')){
                        header("Location:" . $sfmurl);
                        header("HTTP/1.1 302 Temporary Redirect");
                        exit();
                    }
		endif;    
               
            }/* end sfm_feed_redirect() */
	    
	    public function sfmgetCurrentURL()
	    {
		$currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		$currentURL .= $_SERVER["SERVER_NAME"];
	     
		if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
		{
		    $currentURL .= ":".$_SERVER["SERVER_PORT"];
		}
	     
		$currentURL .= $_SERVER["REQUEST_URI"];
		return $currentURL;
	    }/* end sfmgetCurrentURL() */
	   
	    /* delete record on user deletetion */ 
	    public function sfmDeleteFeed($user_id)
	    {
		global $wpdb;
		$wpdb->query('DELETE FROM '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE.' WHERE feed_type="author_rss" AND id_on_blog="'.$user_id.'"');
	    }/* end sfmDeleteFeed() */
	    
	    /* delete record on category deletetion */ 
	    public function sfmDeleteCatFeed($catId)
	    {
		global $wpdb;		
	        $wpdb->query('DELETE FROM '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE.' WHERE feed_type="category_rss" AND id_on_blog="'.$catId.'"');
	    }/* end sfmDeleteCatFeed() */
	    
	    /* fetch messages  */
	      public function sfmProcessFeeds()
	    {
		    if(isset($_POST['feed_id']))
		    {
		    $curl = curl_init();  
		    curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $this->SFM_SETUP_URL.$_POST['feed_id']."/Y",
		    CURLOPT_USERAGENT => 'sf rss request',
		    CURLOPT_POST => 0      
		    ));
		    $resp = curl_exec($curl);
		    curl_close($curl);
		   echo "done"; exit;
		   }
		   else{
		    echo  "wrong feedid"; exit;
		   }
	    }
	    
	 public function sfmHeaderMeta()
	 {
		global $wpdb;
		$getFeedsData=$wpdb->get_results('SELECT *  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where redirect_status=1",ARRAY_A);
		if(!empty($getFeedsData)) {
		    foreach($getFeedsData as $fData)
		    {
			if(!empty($fData['sf_feedid']) && !empty($fData['verification_code']))
			{
			    echo ' <meta name="specificfeeds-verification-code-'.$fData['sf_feedid'].'" content="'.$fData['verification_code'].'"/>';
			}
		    }
		}
	    
	  }
    
}/* end of Class */


?>