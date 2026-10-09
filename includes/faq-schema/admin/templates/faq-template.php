<?php
$faq_status = 'checked';
$faq_status_class = '';
if(!isset($faq_script_status) || $faq_script_status == false){
    $faq_status = '';
    $faq_status_class = 'faq-disabled';

}
if (isset($faqArray) && (count($faqArray) > 0) ) {
    ?>
    <fieldset id="ach_wp_faq_schema_wrapper">

        <div class="bg-light faqs-toggle-wrapper text-center py-2">
            <h6 class="mb-0">Enable Schema

                <label class="switch mb-0 ml-3">
                    <input id="faq_toggler" <?php echo $faq_status ?> name="faq_script_status"  type="checkbox">
                    <span class="slider round"></span>
                </label></h6>
        </div>
        <div class="faqs-container <?php echo $faq_status_class ?>">

            <?php wp_nonce_field('ach_wp_faq_schema_form_metabox_nonce', 'ach_wp_faq_schema_form_metabox_process') ?>
            <?php

            foreach ($faqArray as $key=>$faq){
                $key++
                ?>

                <div class="faqs-card p-4 pr-5 my-3 bg-light position-relative">
                    <button class="btn btn-danger  ach_wp_faq_schema_faq_delete_button btn-sm position-absolute mr-auto"><i class="dashicons dashicons-no-alt"></i></button>

                    <textarea placeholder="Question #<?php echo $key ?>" class="form-control question" name="question[<?php echo $key ?>]"><?php echo $faq['question'] ?></textarea>

                    <textarea placeholder="Answer #<?php echo $key ?>" class="form-control answer" name="answer[<?php echo $key ?>]"><?php echo $faq['answer'] ?></textarea>
                </div>
                <?php
            }
            ?>
        </div>


        <button type="button" class="btn button btn-light btn-outline-dark mx-auto d-block mt-2 <?php echo $faq_status_class ?>" id="ach_wp_faq_schema_add_new">
            &plus; <?php echo __('Add another FAQ', 'ach_toolkit') ?></button>
    </fieldset>
    <?php
}else{
    ?>
    <fieldset id="ach_wp_faq_schema_wrapper">
        <div class="bg-light faqs-toggle-wrapper text-center py-2">
            <h6 class="mb-0">Enable Schema

                <label class="switch mb-0 ml-3">
                    <input id="faq_toggler" <?php echo $faq_status ?> name="faq_script_status"  type="checkbox">
                    <span class="slider round"></span>
                </label></h6>
        </div>


        <?php wp_nonce_field('ach_wp_faq_schema_form_metabox_nonce','ach_wp_faq_schema_form_metabox_process') ?>
        <div class="faqs-container <?php echo $faq_status_class ?>">

            <div class="faqs-card p-4 pr-5 my-3 bg-light position-relative">
                <button class="btn btn-danger ach_wp_faq_schema_faq_delete_button btn-sm position-absolute mr-auto"><i
                            class="dashicons dashicons-no-alt"></i></button>
                <textarea placeholder="Question #1" class="form-control question" name="question[]"></textarea>

                <textarea placeholder="Answer #1" class="form-control answer" name="answer[]"></textarea>
            </div>
        </div>
        <button type="button" class="btn button btn-light btn-outline-dark mx-auto d-block mt-2 <?php echo $faq_status_class ?>" id="ach_wp_faq_schema_add_new">
            &plus; <?php echo __('Add another FAQ', 'ach_toolkit') ?></button>

    </fieldset>
    <?php
}
?>