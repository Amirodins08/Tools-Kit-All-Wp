<?php
error_reporting(0);
include('admin/init.php');

function ach_wp_faq_schema_init() {

    function ach_wp_faq_schema_add_header_to_post() {
        global $post; // Get the current post data
        $faq_script_status = get_post_meta($post->ID, 'faq_script_status', true);

        if ($faq_script_status == true) {
            $saved = get_post_meta($post->ID, 'faq_meta', true); // Get the saved values

            $saved = str_replace("\'", "'", $saved);
            if (!empty($saved)) {
                $ques_ans_data = json_decode($saved, true);
                $scrdata = array();
                foreach ($ques_ans_data as $sfdata) {

                    $scrdata[] = array("@type" => "Question", "name" => stripslashes($sfdata['question']), "acceptedAnswer" => array("@type" => "Answer", "text" => stripslashes($sfdata['answer'])));
                }

                if (!empty($scrdata)) {

                    $script_string = json_encode($scrdata);

                    ?>
                    <script type="application/ld+json">
                        {"@context":"https://schema.org","@type":"FAQPage","mainEntity":<?php echo $script_string; ?>}
                    </script>

                    <?php
                }
            }
        }
    }

    add_action('wp_head', 'ach_wp_faq_schema_add_header_to_post');

    function ach_wp_faq_schema_custom_column($column, $post_id) {
        switch ($column) {
            case 'faq_meta':
                $faq_script_status = get_post_meta($post_id, 'faq_script_status', true);
                $class = 'ach_wp_faq_schema_off';

                if ($faq_script_status == true) {
                    $faq_meta = get_post_meta($post_id, 'faq_meta', true);
                    $faq_meta = str_replace("\'", "'", $faq_meta);

                    $faq_meta = json_decode($faq_meta);
                    $class = 'ach_wp_faq_schema_empty';
                    if ($faq_meta && !empty($faq_meta)) {
                        if (count($faq_meta) >= 3) {
                            $class = 'ach_wp_faq_schema_good';
                        } else {
                            $class = 'ach_wp_faq_schema_bad';
                            if (count($faq_meta) == 0) {
                                $class = 'ach_wp_faq_schema_empty';
                            }
                        }
                    }
                }
                echo '<div class="ach_wp_faq_schema_meta_status' . $class . ' ">';
                break;

        }
    }

    // Adding meta column to posts dashboard
    function ach_wp_faq_schema_add_faq_meta_column($columns) {
        $columns['faq_meta'] = __('FAQ Meta');
        return $columns;
    }

    add_filter('manage_posts_columns', 'ach_wp_faq_schema_add_faq_meta_column');
    add_filter('manage_page_posts_columns', 'ach_wp_faq_schema_add_faq_meta_column');

    add_action('manage_posts_custom_column', 'ach_wp_faq_schema_custom_column',10, 2);
    add_action('manage_page_posts_custom_column', 'ach_wp_faq_schema_custom_column', 10, 2);

}
add_action('init', 'ach_wp_faq_schema_init');