<?php
/*
Plugin Name: Custom Related Posts Widget
Plugin URI: http://mikelaroy.ca
Description: Creates a widget that displays related Custom Posts by category.
Author: Michael LaRoy
Version: 1.0
Author URI: http://mikelaroy.ca

Copyright 2013, Michael LaRoy 

The plugin is licenced under the General Public License, Version 2 - http://www.gnu.org/licenses/gpl-2.0.html

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.


*/


class RelatedPostWidget extends WP_Widget
{
  function RelatedPostWidget()
  {
    $widget_ops = array('classname' => 'RelatedPostWidget', 'description' => 'Displays a related post' );
    $this->WP_Widget('RelatedPostWidget', 'Related Posts', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {

    // makes the $post variable accessible to the function
    global $post;

    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;


 
    // WIDGET CODE GOES HERE


    // begin custom related loop  
    // get the custom post type's taxonomy terms
          
        $custom_taxterms = wp_get_object_terms( $post->ID,
                    'category', array('fields' => 'ids') );
       
        // arguments for the query
        $args = array(
        'post_type' => 'st_kb', // 'custom_post_type' goes here
        'post_status' => 'publish',
        'posts_per_page' => 3, // you may edit this number
        'orderby' => 'rand', // displays posts randomly
        'tax_query' => array(
            array(
                'taxonomy' => 'category',
                'field' => 'id',
                'terms' => $custom_taxterms
            )
        ),
        'post__not_in' => array ($post->ID),
        );

        $related_items = new WP_Query( $args );
        // loop over query
        if ($related_items->have_posts()) :
        echo '<div>
                <ul>';

        while ( $related_items->have_posts() ) : $related_items->the_post();
        ?>
            <li><a href="<?php the_permalink(); ?>" > 
                   <?php the_title(); ?>
                </a>
            </li>
                
        <?php
        endwhile;
        echo '</ul></div>';
        endif;
        // Reset Post Data
        wp_reset_postdata();
        
          
  
   //  end related custom posts loop
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("RelatedPostWidget");') );?>