<?php
/**
 * Callback functions for comments
 *
 * @since infinite-photography 1.0.0
 *
 * @param $comment
 * @param $args
 * @param $depth
 * @return void
 *
 */
if ( !function_exists('infinite_photography_commment_list') ) :

    function infinite_photography_commment_list($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment;
        extract($args, EXTR_SKIP);
        if ('div' == $args['style']) {
            $tag = 'div';
            $add_below = 'comment';
        }
        else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        ?>
        <<?php echo $tag ?>
        <?php comment_class(empty($args['has_children']) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
        <?php if ('div' != $args['style']) : ?>
            <div id="div-comment-<?php comment_ID() ?>" class="comment-body clearfix">
        <?php endif; ?>
        <div class="comment-author vcard">
            <?php if ($args['avatar_size'] != 0) echo get_avatar($comment, '64'); ?>
            <?php printf(__('<cite class="fn">%s</cite>', 'infinite-photography' ), get_comment_author_link()); ?>
        </div>
        <?php if ($comment->comment_approved == '0') : ?>
            <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'infinite-photography'); ?></em>
            <br/>
        <?php endif; ?>
        <div class="comment-meta commentmetadata">
            <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                <i class="fa fa-clock-o"></i>
                <?php
                /* translators: 1: date, 2: time */
                printf(__('%1$s at %2$s', 'infinite-photography'), get_comment_date(), get_comment_time()); ?>
            </a>
            <?php edit_comment_link(__('(Edit)', 'infinite-photography'), '  ', ''); ?>
        </div>
        <?php comment_text(); ?>
        <div class="reply">
            <?php comment_reply_link( array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
        </div>
        <?php if ('div' != $args['style']) : ?>
            </div>
        <?php endif; ?>
        <?php
    }
endif;

/**
 * Select sidebar according to the options saved
 *
 * @since infinite-photography 1.0.0
 *
 * @param null
 * @return string
 *
 */
if ( !function_exists('infinite_photography_sidebar_selection') ) :
    function infinite_photography_sidebar_selection( ) {
        global $infinite_photography_customizer_all_values;
        if(
            'left-sidebar' == $infinite_photography_customizer_all_values['infinite-photography-sidebar-layout'] ||
            'no-sidebar' == $infinite_photography_customizer_all_values['infinite-photography-sidebar-layout']
        ){
            $infinite_photography_global_class = $infinite_photography_customizer_all_values['infinite-photography-sidebar-layout'];
        }
        else{
            $infinite_photography_global_class= 'right-sidebar';
        }
        return $infinite_photography_global_class;
    }
endif;

/**
 * Return content of fixed lenth
 *
 * @since infinite-photography 1.0.0
 *
 * @param string $infinite_photography_content
 * @param int $length
 * @return string
 *
 */
if ( ! function_exists( 'infinite_photography_words_count' ) ) :
    function infinite_photography_words_count( $infinite_photography_content = null, $length = 16 ) {

        $length = absint( $length );
        $source_content = preg_replace( '`\[[^\]]*\]`', '', $infinite_photography_content );
        $trimmed_content = wp_trim_words( $source_content, $length, '...' );
        return $trimmed_content;

    }
endif;

/**
 * BreadCrumb Settings
 */
if( ! function_exists( 'infinite_photography_breadcrumbs' ) ):

    function infinite_photography_breadcrumbs() {

        wp_reset_postdata();
        global $post;
        $trans_here = __( 'You are here', 'infinite-photography' );
        $trans_home = __( 'Home', 'infinite-photography' );
        $search_result_text = __( 'Search Results for ', 'infinite-photography' );

        $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
        $delimiter = '<span class="bread_arrow"> > </span>'; // delimiter between crumbs
        $home = $trans_home; // text for the 'Home' link
        $showHomeLink = 1;

        $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
        $before = '<span class="current">'; // tag before the current crumb
        $after = '</span>'; // tag after the current crumb

        $homeLink = esc_url( home_url() );

        if (is_home() || is_front_page()) {

            if ($showOnHome == 1) echo '<div id="infinite-photography-breadcrumbs"><div class="breadcrumb-container"><a href="' . $homeLink . '">' . $home . '</a></div></div>';

        } else {
            if($showHomeLink == 1){
                echo '<div id="infinite-photography-breadcrumbs" class="clearfix"><span class="breadcrumb">'.esc_attr( $trans_here ).'</span><div class="breadcrumb-container"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
            } else {
                echo '<div id="infinite-photography-breadcrumbs" class="clearfix"><span class="breadcrumb">'.esc_attr( $trans_here ).'</span><div class="breadcrumb-container">' . $home . ' ' . $delimiter . ' ';
            }

            if ( is_category() ) {
                $thisCat = get_category(get_query_var('cat'), false);
                if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
                echo $before .  single_cat_title('', false) . $after;

            } elseif ( is_search() ) {
                echo $before . $search_result_text.' "' . get_search_query() . '"' . $after;

            } elseif ( is_day() ) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
                echo $before . get_the_time('d') . $after;

            } elseif ( is_month() ) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                echo $before . get_the_time('F') . $after;

            } elseif ( is_year() ) {
                echo $before . get_the_time('Y') . $after;

            } elseif ( is_single() && !is_attachment() ) {
                if ( get_post_type() != 'post' ) {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
                    if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
                } else {
                    $cat = get_the_category();
                    if( !empty( $cat ) ){
                        $cat = $cat[0];
                        $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');

                        if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                        echo $cats;
                    }
                    if ($showCurrent == 1) echo $before . get_the_title() . $after;
                }

            } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
                $post_type = get_post_type_object(get_post_type());
                echo $before . $post_type->labels->singular_name . $after;

            } elseif ( is_attachment() ) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                if( !empty( $cat ) ){
                    $cat = $cat[0];
                    echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
                }
                echo '<a href="' . esc_url( get_permalink( $parent ) ) . '">' . $parent->post_title . '</a>';
                if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

            } elseif ( is_page() && !$post->post_parent ) {
                if ($showCurrent == 1) echo $before . get_the_title() . $after;

            } elseif ( is_page() && $post->post_parent ) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_post($parent_id);
                    $breadcrumbs[] = '<a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo $breadcrumbs[$i];
                    if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
                }
                if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

            } elseif ( is_tag() ) {
                echo $before . __('Posts tagged : ','infinite-photography' ) . single_tag_title('', false) . '"' . $after;

            } elseif ( is_author() ) {
                global $author;
                $userdata = get_userdata($author);
                echo $before . __('Author: ', 'infinite-photography' ) . $userdata->display_name . $after;

            } elseif ( is_404() ) {
                echo $before . __( 'Error 404', 'infinite-photography' ) . $after;
            }
            else {
                /*nothing to do*/
            }
            if ( get_query_var('paged') ) {
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
                echo __('Page' , 'infinite-photography') . ' ' . get_query_var('paged');
                if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
            }
            echo '</div></div>';
        }
    }
endif;