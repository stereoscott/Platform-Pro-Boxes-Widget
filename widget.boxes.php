<?php 

class Stereo_BoxesWidget extends WP_Widget {
  
  function Stereo_BoxesWidget() {
   $widget_ops = array('description' => 'Creates a widget to display boxes from a specified box set.' );
   parent::WP_Widget(false, $name = __('Boxes', 'pagelines'), $widget_ops);    
  }

  function widget($args, $instance) {        
 		extract($args, EXTR_SKIP);
    
    $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
    
    echo $before_widget;
		if ( !empty( $title ) ) { 
		  echo $before_title . $title . $after_title; 
		}
				
    // THE TEMPLATE
    global $post; 
    $current_post = $post;
    $box_thumb_size = (isset($post) && pagelines('box_thumb_size', $post->ID)) ? pagelines('box_thumb_size', $post->ID) : 64;
    $box_thumb_type = (isset($post) && pagelines('box_thumb_type', $post->ID)) ? pagelines('box_thumb_type', $post->ID) : 'inline_thumbs';
    $theposts = $this->get_box_posts($instance);
    $boxes = (is_array($theposts)) ? $theposts : array();
    foreach($boxes as $post) : setup_postdata($post); $custom = get_post_custom($post->ID); ?>

    		<div class="dcol_1">
    			<div class="dcol-pad <?php echo $box_thumb_type;?>">	
    				<?php if(get_post_meta($post->ID, 'the_box_icon', true)):?>
    						<div class="fboxgraphic">
    							<img src="<?php echo get_post_meta($post->ID, 'the_box_icon', true);?>" style="width:<?php echo $box_thumb_size;?>px">
    			            </div>
    				<?php endif;?>

    					<div class="fboxinfo fix">
    						<div class="fboxtitle"><h3><?php the_title(); ?></h3></div>
    						<div class="fboxtext"><?php the_content(); ?><?php edit_post_link(__('[Edit Box]', 'pagelines'), '', '');?></div>

    					</div>
    					<?php pagelines_register_hook( 'pagelines_box_inside_bottom', $this->id ); // Hook ?>
    			</div>
    		</div>

    <?php endforeach;
    
    echo $after_widget;
  }

  function update($new_instance, $old_instance) {      
    $instance['box_set'] = intval($new_instance['box_set']);
    $instance['box_items'] = intval($new_instance['box_items']);
    $instance['title'] = strip_tags($new_instance['title']);
 
    return $new_instance;
  }

  function form($instance) { 
    $instance = wp_parse_args( (array) $instance, array('box_set' => false));
   	$title = strip_tags($instance['title']);
	  $terms_array = get_terms('box-sets');
	  $boxitems = isset($instance['box_items']) ? absint($instance['box_items']) : false;
  ?>  
  
  <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
	
  <p>
  <label for="<?php echo $this->get_field_id('box_set'); ?>" class="screen-reader-text"><?php _e('Select Box Set'); ?></label>
	<?php if(is_array($terms_array) && !empty($terms_array)):?>
			<select class="widefat" id="<?php echo $this->get_field_id('box_set'); ?>" name="<?php echo $this->get_field_name('box_set'); ?>">
        <option value=""><?php _e('All Boxes'); ?></option>
				<?php foreach($terms_array as $term):?>
					<option value="<?php echo $term->slug;?>" <?php if($instance['box_set']==$term->slug) echo 'selected';?>><?php echo $term->name; ?></option>
				<?php endforeach;?>
			</select>
	<?php else:?>
		<div class="meta-message">No sets have been created.</div>
	<?php endif;?>
	</p>
  			
		<p><label for="<?php echo $this->get_field_id('box_items'); ?>"><?php _e('Number of boxes to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('box_items'); ?>" name="<?php echo $this->get_field_name('box_items'); ?>" type="text" value="<?php echo $boxitems; ?>" size="3" /></p>
  <?php 
  }
  
  function get_box_posts($instance){
		global $post;
		
		if (!isset($this->the_widget_boxes) && isset($post)) {
			
			$query_args = array('post_type' => 'boxes', 'orderby' =>'ID');
		
			if ($instance['box_set']) {
				$query_args = array_merge($query_args, array( 'box-sets' => $instance['box_set'] ) );
			}
	
			if ($instance['box_items']) {
				$query_args = array_merge($query_args, array( 'showposts' => $instance['box_items'] ) );
			}
		
			$boxes_query = new WP_Query($query_args);
		
		 	$this->the_widget_boxes = $boxes_query->posts;
			
		 	if(is_array($this->the_widget_boxes)) return $this->the_widget_boxes;
			else return array();
			
		} elseif(isset($post)) {
			return $this->the_widget_boxes;
		}
	
	}

} 
register_widget('Stereo_BoxesWidget');



