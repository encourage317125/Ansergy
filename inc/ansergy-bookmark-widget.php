<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 7/22/2016
 * Time: 4:22 PM
 */
// Creating the widget
class ansergy_bookmark_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
// Base ID of your widget
            'ansergy_bookmark_widget',

// Widget name will appear in UI
            __('Ansergy Bookmark Widget', 'ansergy_bookmark_widget_domain'),

// Widget description
            array( 'description' => __( 'Bookmark your customized charts and tables', 'ansergy_bookmark_widget_domain' ), )
        );
    }

// Creating widget front-end
// This is where the action happens
    public function widget( $args, $instance ) {
        global $wpdb;
        $title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

    // This is where you run the code and display the output
        $categories = get_terms('bookmark_categories', array(
            'hide_empty' => 0
        ));
        $current_user = wp_get_current_user();
        $table_bookmarks = $wpdb->prefix.'dbookmarks';
        $table_bookmarks_cat = $wpdb->prefix.'dbookmarks_cat';
        $user_global = get_userdatabylogin($global_user);
        $cond = '';
        if(!empty($user_global)){
            $cond = 'or post_author = '.$user_global->ID;
        }

        $bookmarks_list = $wpdb->get_results("SELECT wp_terms.*, wp_posts.* FROM wp_posts LEFT JOIN wp_term_relationships ON ( wp_term_relationships.object_id = wp_posts.ID ) LEFT JOIN wp_term_taxonomy ON ( wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id )LEFT JOIN wp_terms ON ( wp_terms.term_id = wp_term_taxonomy.term_id) where (post_author = $current_user->ID $cond) and post_type = 'bookmarks' ORDER BY wp_terms.name DESC, wp_posts.post_date DESC");

        foreach($bookmarks_list as $bookmark){
            if(empty($bookmark->term_id)){
                $formatBookmark[$bookmark->post_title] = $bookmark;
            }
            else{
                $formatBookmark[$bookmark->name][] = $bookmark;
                $author_cat_list[$bookmark->term_id] = $bookmark;
            }
        }
        ?>
        <div id="bookmarks-list-widget" style="text-align: left;">

            <ul class="<?php if(count($formatBookmark) > 7){ echo 'scroller';}else { echo 'no-scroller';} ?>" id="scroller">
                <?php
                if(!empty($formatBookmark)){
                    foreach($formatBookmark as $catkey=>$catval) {

                        if(is_array($catval)){
                            $nodeType='{"type":"folder"}';
                            echo "<li data-term-id={$catval[0]->term_id} data-slug={$catval[0]->slug} data-jstree={$nodeType}><a href='#'>".$catkey."</a>";
                            $sclass = count($catval)>7 ? "scroller" : "no-scroller" ;
                            if($user_global->ID != $catval[0]->post_author or $current_user->ID == $user_global->ID){
                                echo '<a  href="'.get_admin_url().'post.php?category='.$catval[0]->term_id.'&post_type=bookmarks&action=browserbookmark&type=delete"  title="Delete Category"  id="deletebookmark_'.$catval->ID.'" class="delete"></a>';
                                echo '<span title="Edit Category"  id="editcategory_'.$catval[0]->term_id.'" class="edit edit_cat">&nbsp;</span>';
                            }
                            echo '<ul>';
                            foreach($catval as $key=>$value) {
                                $bookmark_url	= get_post_meta($value->ID, '_D_Plugin_Bookmarks-bookmarks-url', true);
                                $bookmark_title	= get_the_title($value->ID);
                                $nodeType='{"type":"file"}';
                                echo "<li data-bookmark-url={$bookmark_url} data-bookmark-id={$value->ID} data-jstree={$nodeType}><a target='_blank' href='".$bookmark_url."'>".$bookmark_title."</a>";
                                if($user_global->ID != $value->post_author or $current_user->ID == $user_global->ID){
                                    echo '<a href="'.get_admin_url().'post.php?post='.$value->ID.'&post_type=bookmarks&action=browserbookmark&type=delete"  id="deletebookmark_'.$value->ID.'" class="delete"></a><span title="Edit Bookmark" id="bookmark_'.$value->ID.'" class="edit edit_bookmark">&nbsp;</span></li>';
                                }
                            }
                            echo '</ul>';
                        }
                        else{

                            $bookmark_url	= get_post_meta($catval->ID, '_D_Plugin_Bookmarks-bookmarks-url', true);
                            $bookmark_title	= get_the_title($catval->ID);
                            $nodeType='{"type":"file"}';
                            echo "<li data-bookmark-url={$bookmark_url} data-bookmark-id={$catval->ID} data-jstree={$nodeType}><a onclick='javascript: return onLoadBookmark(".$catval->ID.")' href='".$bookmark_url."' class='ansergy_bookmark' data-bookmark='".$catVal->ID."' target='_blank'>".$bookmark_title."</a>";
                            if($user_global->ID != $catval->post_author or $current_user->ID == $user_global->ID){
                                echo '<a href="'.get_admin_url().'post.php?post='.$catval->ID.'&post_type=bookmarks&action=browserbookmark&type=delete"  title="Delete Bookmark"  id="deletebookmark_'.$catval->ID.'" class="delete"></a>';
                                echo '<span title="Edit Bookmark"  id="editbookmark_'.$catval->ID.'" class="edit edit_bookmark">&nbsp;</span>';
                            }

                        }
                        echo '</li>';
                    }
                }

                if(empty($formatBookmark)){
                    echo '<p>No Bookmarks Added!</p>';
                }
                ?>


            </ul>
        </div>
        <?php
        echo $args['after_widget'];
    }

// Widget Backend
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'ansergy_bookmark_widget_domain' );
        }
// Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
    <?php
    }

// Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} // Class ansergy_bookmark_widget ends here

// Register and load the widget
function ansergy_bookmark_load_widget() {
    register_widget( 'ansergy_bookmark_widget' );
}
add_action( 'widgets_init', 'ansergy_bookmark_load_widget' );