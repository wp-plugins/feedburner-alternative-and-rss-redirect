<?php
/* Front end newsletter widget */


class sfmWidget extends WP_Widget {

	public $SFM_REDIRECTION_TABLE='sfm_redirects';
	public function __construct() {
		parent::__construct(
			'sfm-widget', __('RSS Redirect', 'RSS Redirect'), $widget_ops, $control_ops
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'sfm_enqueue_scripts' ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'sfm_print_scripts' ), 9999 );
	}
	public function sfm_enqueue_scripts( $hook_suffix ) {
		if ( 'widgets.php' !== $hook_suffix ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'underscore' );
	}
        public function sfm_print_scripts() {
		?>
		<script>
			jQuery("body").bind("ajaxComplete", function(){
				jQuery( '#widgets-right .widget:has(.color-picker)' ).each( function () {
						initColorPicker( jQuery( this ) );
					} );
				function initColorPicker( widget ) {
					widget.find( '.color-picker' ).wpColorPicker( {
						change: _.throttle( function() { // For Customizer
							jQuery(this).trigger( 'change' );
						}, 3000 )
					});
				}

			  });
			( function( $ ){
				function initColorPicker( widget ) {
					widget.find( '.color-picker' ).wpColorPicker( {
						change: _.throttle( function() { // For Customizer
							$(this).trigger( 'change' );
						}, 3000 )
					});
				}

				function onFormUpdate( event, widget ) {
					initColorPicker( widget );
				}

				$( document ).on( 'widget-added widget-updated', onFormUpdate );

				$( document ).ready( function() {
					$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
						initColorPicker( $( this ) );
					} );
				} );
			}( jQuery ) );
		</script>
		<?php
	}
	
	function sfmWidget() {
        $widget_ops = array( 'classname' => 'sfm', 'description' => __('SpecificFeeds Feedmaster', 'SpecificFeeds Feedmaster') );
        $control_ops = array( 'width' => 450, 'height' => 350, 'id_base' => 'sfm-widget' );
        $this->WP_Widget( 'sfm-widget', __('SpecificFeeds Feedmaste', 'SpecificFeeds Feedmaste'), $widget_ops, $control_ops );	
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		/*Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;
		      
		echo $before_widget;
		if(isset($instance['sfm_border']) && $instance['sfm_border']=='yes')
		{
		    $border="border:1px solid ".$instance['sfm_border_color'].";"; 
		}
		else{
		    $border="";
		}
		
                ?>
		
                <div class="sfm_widget_sec" style="background-color: <?php echo $instance['sfm_back_color']; ?> ;  <?php echo $border; ?>;">   
                    <?php /* Display the widget title */
		if ( $title ) echo  "<span class='sfmTitle' style='margin-bottom:19px;font-family:". $instance['sfm_font'].";font-size: ".$instance['sfm_font_size'].";color: ".$instance['sfm_font_color']." ;'>".$title."</span>" ;
		/* Link the main icons function */
                 echo $this->sfm_newsLetterForm($this->id);
               ?>
               </div>
              <?php
	     echo $after_widget;
	}
	
	public function sfm_newsLetterForm($form_id)
	{
	    global $wpdb;
	    $form_id="news-".$form_id;
	    /* get the feedid of blog rss */
	   //echo 'SELECT sf_feedid  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where feed_type='main_rss' OR blog_rss='".html_entity_decode(get_bloginfo('rss2_url'))."'";
	    $get_feed=$wpdb->get_row('SELECT sf_feedid  from '.$wpdb->prefix.$this->SFM_REDIRECTION_TABLE." where feed_type='main_rss' OR blog_rss='".html_entity_decode(get_bloginfo('rss2_url'))."'");
	  
	    ob_start();
	    ?>
	 
	<div class="sfmNewsLetter"  >
		<form action="" method="post" target="popupwindow" id="<?php echo $form_id; ?>" accept-charset="utf-8" onsubmit="return processfurther(this);">
			<span class="sfrd_inputHolder"><input type="email" class="feedemail" name="data[Widget][email]" id="widgetemail" required value=""  /></span>
			<span class="sfrd_buttonHolder"><input type="submit" name="commit"  value="Subscribe"  /></span>
			<input type="hidden" class="feedid" value="<?php echo $get_feed->sf_feedid; ?>" name="data[Widget][feed_id]" id="sffeed_id"/>
			<input id="sffeedtype" type="hidden" class="feedtype" value="8" name="data[Widget][feedtype]">
			<script type="text/javascript" src="http://www.specificfeeds.com/newwidgets/emailjs"></script>
			<script type="text/javascript"> init_box('<?php echo $form_id; ?>'); </script>
		</form>    
	    </div>   
	    
	    
	<?php
	$frontForm= ob_get_clean();
	return $frontForm;exit;
	}
	
	/*Update the widget */ 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['sfm_font'] = strip_tags( $new_instance['sfm_font'] );
		$instance['sfm_font_size'] = strip_tags( $new_instance['sfm_font_size'] );
		$instance['sfm_font_color'] = strip_tags( $new_instance['sfm_font_color'] );
		$instance['sfm_back_color'] = strip_tags( $new_instance['sfm_back_color'] );
		$instance['sfm_border'] = strip_tags( $new_instance['sfm_border'] );
		$instance['sfm_border_color'] = strip_tags( $new_instance['sfm_border_color'] );
		return $instance;
	}
	
	/* Set up some default widget settings. */
	function form( $instance ) {
		$defaults = array( 'title' =>"" );
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
		    <label style="font-weight:bold;" for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Heading:', 'SpecificFeeds Feedmaster'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"  name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo ($instance['title'])? $instance['title'] : 'Enter your email address'; ?>" placeholder="" class="widefat" style="width:100%;"  />
		</p>
		<p>
		    <label style="font-weight:bold;" for="<?php echo $this->get_field_id( 'sfm_font' ); ?>"><?php _e('Font:', 'SpecificFeeds Feedmaster'); ?></label>
		    <select name="<?php echo $this->get_field_name( 'sfm_font' ); ?>" id="<?php echo $this->get_field_id( 'sfm_font' ); ?>" style="width: 100%">
                                <option value="Arial, Helvetica, sans-serif" <?php echo ($instance['sfm_font']=='Arial, Arial, Helvetica, sans-serif') ?  'selected="true"' : '' ;?>>Arial</option>
                                <option value="Arial Black, Gadget, sans-serif" <?php echo ($instance['sfm_font']=='Arial Black, Gadget, sans-serif') ?  'selected="true"' : '' ;?>>Arial Black</option>
                                <option value="Calibri" <?php echo ($instance['sfm_font']=='Calibri') ?  'selected="true"' : '' ;?>>Calibri</option>
                                <option value="Comic Sans MS" <?php echo ($instance['sfm_font']=='Comic Sans MS') ?  'selected="true"' : '' ;?>>Comic Sans MS</option>
                                <option value="Courier New" <?php echo ($instance['sfm_font']=='Courier New') ?  'selected="true"' : '' ;?>>Courier New</option>
                                <option value="Georgia" <?php echo ($instance['sfm_font']=='Georgia') ?  'selected="true"' : '' ;?>>Georgia</option>
                                <option value="Helvetica,Arial,sans-serif" <?php echo ($instance['sfm_font']=='Helvetica,Arial,sans-serif') ?  'selected="true"' : '' ;?>>Helvetica</option>
				<option value="Impact" <?php echo ($instance['sfm_font']=='Impact') ?  'selected="true"' : '' ;?>>Impact</option>
                                <option value="Lucida Console" <?php echo ($instance['sfm_font']=='Lucida Console') ?  'selected="true"' : '' ;?>>Lucida Console</option>
				<option value="Tahoma,Geneva" <?php echo ($instance['sfm_font']=='Tahoma,Geneva') ?  'selected="true"' : '' ;?>>Tahoma</option>
                                <option value="Times New Roman" <?php echo ($instance['sfm_font']=='Times New Roman') ?  'selected="true"' : '' ;?>>Times New Roman</option>
                                <option value="Trebuchet MS" <?php echo ($instance['sfm_font']=='Trebuchet MS') ?  'selected="true"' : '' ;?>>Trebuchet MS</option>
                                <option value="Verdana" <?php echo ($instance['sfm_font']=='Verdana') ?  'selected="true"' : '' ;?>>Verdana</option>
                            
                            </select>
		</p>
		<p>
		    <label style="font-weight:bold;" for="<?php echo $this->get_field_id( 'sfm_font_size' ); ?>"><?php _e('Font Size:', 'SpecificFeeds Feedmaster'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'sfm_font_size' ); ?>" name="<?php echo $this->get_field_name( 'sfm_font_size' ); ?>" value="<?php echo ($instance['sfm_font_size'])? $instance['sfm_font_size'] : '11px'; ?>"  class="widefat" placeholder="eg.15px" style="width:100%;" />
		</p>
		<p>
		    <label style="font-weight:bold;" for="<?php echo $this->get_field_id( 'sfm_font_color' ); ?>"><?php _e('Font Color:', 'SpecificFeeds Feedmaster'); ?></label><br/>
		    <input type="text" id="<?php echo $this->get_field_id( 'sfm_font_size' ); ?>" name="<?php echo $this->get_field_name( 'sfm_font_color' ); ?>" value="<?php echo ($instance['sfm_font_color'])? $instance['sfm_font_color'] : ''; ?>" class="color-picker widefat" style="width:100%;"  /> 
		</p>
		<p>
		    <label style="font-weight:bold;" for="<?php echo $this->get_field_id( 'sfm_back_color' ); ?>"><?php _e('Background Color:', 'SpecificFeeds Feedmaster'); ?></label><br/>
		    <input type="text" id="<?php echo $this->get_field_id( 'sfm_back_color' ); ?>" name="<?php echo $this->get_field_name( 'sfm_back_color' ); ?>" value="<?php echo ($instance['sfm_back_color'])? $instance['sfm_back_color'] : ''; ?>" class="color-picker widefat"  />
		</p>
		<p>
		    <label style="font-weight:bold;" for="<?php echo $this->get_field_id( 'sfm_border' ); ?>"><?php _e('Border :', 'SpecificFeeds Feedmaster'); ?></label><br/>
		   <label style="font-weight:bold;">Yes</label>  <input type="radio" id="<?php echo $this->get_field_id( 'sfm_border' ); ?>" name="<?php echo $this->get_field_name( 'sfm_border' ); ?>" <?php echo ($instance['sfm_border']=="yes")? "checked='true'":"checked='true'"; ?> value="yes"  /> &nbsp; <label style="font-weight:bold;">No</label> <input type="radio" id="<?php echo $this->get_field_id( 'sfm_border' ); ?>" name="<?php echo $this->get_field_name( 'sfm_border' ); ?>"  <?php echo ($instance['sfm_border']=="no")? "checked='true'":''; ?> value="no"  />
		</p>
		<p>
		    <label style="font-weight:bold;" for="<?php echo $this->get_field_id( 'sfm_border_color' ); ?>"><?php _e('Border Color:', 'SpecificFeeds Feedmaster'); ?></label><br/>
		    <input type="text" id="<?php echo $this->get_field_id( 'sfm_border_color' ); ?>" name="<?php echo $this->get_field_name( 'sfm_border_color' ); ?>" value="<?php echo ($instance['sfm_border_color'])? $instance['sfm_border_color'] : '#ededed'; ?>" class="color-picker widefat" style="width:100%;"  /> 
		</p>
		<?php if($this->number!='__i__') : ?> 
		<p><label>Use shortcode <strong>[sfm_newsletter id='<?php echo $this->number; ?>']</strong> any where to display this widget in template.</label></p>
		<?php endif; ?>	     
	<?php
	}
} /* END OF widget Class */



?>