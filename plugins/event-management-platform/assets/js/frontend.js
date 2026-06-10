jQuery(document).ready(function($) {
    // FAQ Accordion
    $('.emp-fe-faq-q').on('click', function() {
        var $answer = $(this).next('.emp-fe-faq-a');
        
        // Slide toggle target answer
        $answer.slideToggle(200);
        
        // Toggle active icon or rotation (if implemented in CSS)
        $(this).toggleClass('open');
    });
});
