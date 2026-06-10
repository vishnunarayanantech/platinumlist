jQuery(document).ready(function($) {
    // 1. Slug Generator
    var $titleInput = $('#emp-event-title');
    var $slugInput  = $('#emp-event-slug');

    if ($titleInput.length && $slugInput.length) {
        $titleInput.on('blur', function() {
            if (!$slugInput.val()) {
                var slug = $titleInput.val()
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                $slugInput.val(slug);
            }
        });
    }

    // 2. Featured Image Media Uploader
    var featuredFrame;
    $('#emp-select-featured-image').on('click', function(e) {
        e.preventDefault();

        if (featuredFrame) {
            featuredFrame.open();
            return;
        }

        featuredFrame = wp.media({
            title: 'Select Featured Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        featuredFrame.on('select', function() {
            var attachment = featuredFrame.state().get('selection').first().toJSON();
            $('#emp-featured-image-id').val(attachment.id);
            $('#emp-featured-image-preview').html('<img src="' + attachment.url + '" style="max-width:100%; height:auto;" />');
            $('#emp-remove-featured-image').show();
        });

        featuredFrame.open();
    });

    $('#emp-remove-featured-image').on('click', function(e) {
        e.preventDefault();
        $('#emp-featured-image-id').val('');
        $('#emp-featured-image-preview').html('');
        $(this).hide();
    });

    // 3. Gallery Media Uploader
    var galleryFrame;
    $('#emp-add-gallery-images').on('click', function(e) {
        e.preventDefault();

        if (galleryFrame) {
            galleryFrame.open();
            return;
        }

        galleryFrame = wp.media({
            title: 'Add Images to Gallery',
            button: { text: 'Add to gallery' },
            multiple: true
        });

        galleryFrame.on('select', function() {
            var selection = galleryFrame.state().get('selection');
            var ids = $('#emp-gallery-ids').val() ? $('#emp-gallery-ids').val().split(',') : [];

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                if (ids.indexOf(attachment.id.toString()) === -1) {
                    ids.push(attachment.id);
                    var itemHtml = '<div class="emp-gallery-item" data-id="' + attachment.id + '">' +
                        '<img src="' + (attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url) + '" />' +
                        '<div class="emp-gallery-remove">&times;</div>' +
                        '</div>';
                    $('#emp-gallery-container').append(itemHtml);
                }
            });

            $('#emp-gallery-ids').val(ids.join(','));
        });

        galleryFrame.open();
    });

    // Remove from gallery
    $(document).on('click', '.emp-gallery-remove', function() {
        var $item = $(this).closest('.emp-gallery-item');
        var id = $item.data('id').toString();
        var ids = $('#emp-gallery-ids').val().split(',');

        var index = ids.indexOf(id);
        if (index > -1) {
            ids.splice(index, 1);
        }

        $('#emp-gallery-ids').val(ids.join(','));
        $item.remove();
    });

    // 4. FAQ Repeater Field
    $('#emp-add-faq').on('click', function(e) {
        e.preventDefault();
        var index = $('.emp-faq-repeater-item').length;
        var html = '<div class="emp-faq-repeater-item">' +
            '<button class="emp-faq-remove">Remove</button>' +
            '<div class="emp-form-group">' +
            '<label>Question</label>' +
            '<input type="text" name="faqs[' + index + '][question]" class="emp-form-control" placeholder="e.g. Is food provided?" />' +
            '</div>' +
            '<div class="emp-form-group">' +
            '<label>Answer</label>' +
            '<textarea name="faqs[' + index + '][answer]" class="emp-form-control" rows="3" placeholder="e.g. Yes, free lunch is included."></textarea>' +
            '</div>' +
            '</div>';
        $('#emp-faq-repeater-container').append(html);
    });

    $(document).on('click', '.emp-faq-remove', function(e) {
        e.preventDefault();
        $(this).closest('.emp-faq-repeater-item').remove();

        // Re-index FAQ names
        $('.emp-faq-repeater-item').each(function(index) {
            $(this).find('input[name*="[question]"]').attr('name', 'faqs[' + index + '][question]');
            $(this).find('textarea[name*="[answer]"]').attr('name', 'faqs[' + index + '][answer]');
        });
    });

    // 5. Content Sections Repeater
    $(document).on('click', '#emp-add-section', function(e) {
        e.preventDefault();
        var index = $('.emp-section-repeater-item').length;
        var html =
            '<div class="emp-section-repeater-item" style="border:1px solid #444;border-radius:8px;padding:16px;margin-bottom:12px;background:#1e1e2e;">' +
                '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">' +
                    '<strong>Section ' + (index + 1) + '</strong>' +
                    '<button type="button" class="emp-section-remove emp-btn emp-btn-danger" style="padding:4px 10px;font-size:12px;">Remove</button>' +
                '</div>' +
                '<div class="emp-form-group">' +
                    '<label>Section Title</label>' +
                    '<input type="text" name="sections[' + index + '][title]" class="emp-form-control" placeholder="e.g. About the Artist" />' +
                '</div>' +
                '<div class="emp-form-group">' +
                    '<label>Section Description</label>' +
                    '<textarea name="sections[' + index + '][content]" class="emp-form-control" rows="5" placeholder="Enter section content here..."></textarea>' +
                '</div>' +
            '</div>';
        $('#emp-sections-repeater-container').append(html);
    });

    $(document).on('click', '.emp-section-remove', function(e) {
        e.preventDefault();
        $(this).closest('.emp-section-repeater-item').remove();

        // Re-index names and heading numbers after removal
        $('.emp-section-repeater-item').each(function(index) {
            $(this).find('input[name*="[title]"]').attr('name', 'sections[' + index + '][title]');
            $(this).find('textarea[name*="[content]"]').attr('name', 'sections[' + index + '][content]');
            $(this).find('strong').text('Section ' + (index + 1));
        });
    });
});
