(function ($) {
    $(document).on('click', '#ach_wp_faq_schema_add_new', function () {
        var totalFAQs = $('.faqs-card').length;
        var newFaqNumber = totalFAQs + 1;
        var html = '<div class="faqs-card p-4 pr-5 mb-3 bg-light position-relative">\n' +
            '                    <button class="btn btn-danger ach_wp_faq_schema_faq_delete_button btn-sm position-absolute mr-auto"><i\n' +
            '                                class="dashicons dashicons-no-alt"></i></button>\n' +
            '<textarea placeholder="Question #' + newFaqNumber + '" class="form-control question" name="question[' + newFaqNumber + ']"></textarea>\n' +
            '                    <textarea placeholder="Answer #' + newFaqNumber + '" class="form-control answer" name="answer[' + newFaqNumber + ']"></textarea>\n' +
            '                </div>';

        $('.faqs-container').append(html)
    });


    $(document).on('click', '.ach_wp_faq_schema_faq_delete_button', function () {
        $(this).parents('.faqs-card').remove();
        updatePlaceholdersFAQ();
    });

    $(document).on('click', '#faq_toggler', function () {
        if ($(this).is(':checked')) {
            $('.faqs-container').removeClass('faq-disabled')
            $('#ach_wp_faq_schema_add_new').removeClass('faq-disabled').attr('disabled', false);
        } else {
            $('.faqs-container').addClass('faq-disabled');
            $('#ach_wp_faq_schema_add_new').addClass('faq-disabled').attr('disabled', true);

        }
    })


    function updatePlaceholdersFAQ() {
        var counter = 0;
        $('.faqs-card').each(function (key, value) {
            counter++;
            $(this).find('textarea.question').attr('placeholder', 'Question #' + counter);
            $(this).find('textarea.answer').attr('placeholder', 'Answer #' + counter)
        })
    }

})(jQuery);