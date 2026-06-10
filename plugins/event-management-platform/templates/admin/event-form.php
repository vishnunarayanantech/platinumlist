<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php echo $event ? esc_html__( 'Edit Event', 'event-management-platform' ) : esc_html__( 'Create New Event', 'event-management-platform' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-events' ) ); ?>" class="emp-btn emp-btn-secondary">
            <?php esc_html_e( 'Back to List', 'event-management-platform' ); ?>
        </a>
    </div>

    <?php if ( ! empty( $notice ) ) : ?>
        <div class="emp-notice"><?php echo esc_html( $notice ); ?></div>
    <?php endif; ?>

    <?php if ( ! empty( $errors ) ) : ?>
        <div class="emp-notice emp-notice-error">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ( $errors as $field => $err ) : ?>
                    <li><strong><?php echo esc_html( ucfirst( $field ) ); ?>:</strong> <?php echo esc_html( $err ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <?php wp_nonce_field( 'emp_save_event', 'emp_save_event_nonce' ); ?>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <!-- Left Column: Core Fields -->
            <div style="flex: 2; min-width: 300px;">
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Basic Information', 'event-management-platform' ); ?></h2>

                    <div class="emp-form-group">
                        <label for="emp-event-title"><?php esc_html_e( 'Event Title', 'event-management-platform' ); ?> *</label>
                        <input type="text" id="emp-event-title" name="title" value="<?php echo esc_attr( $event ? $event->title : '' ); ?>" class="emp-form-control" required />
                    </div>

                    <div class="emp-form-group">
                        <label for="emp-event-slug"><?php esc_html_e( 'Slug', 'event-management-platform' ); ?> *</label>
                        <input type="text" id="emp-event-slug" name="slug" value="<?php echo esc_attr( $event ? $event->slug : '' ); ?>" class="emp-form-control" required />
                        <span style="font-size:12px; color: var(--emp-text-muted);"><?php esc_html_e( 'Used in frontend URL structure (/events/{slug})', 'event-management-platform' ); ?></span>
                    </div>

                    <div class="emp-form-group">
                        <label for="emp-event-short-desc"><?php esc_html_e( 'Short Description', 'event-management-platform' ); ?></label>
                        <textarea id="emp-event-short-desc" name="short_description" class="emp-form-control" rows="3"><?php echo esc_textarea( $event ? $event->short_description : '' ); ?></textarea>
                    </div>

                    <div class="emp-form-group">
                        <label><?php esc_html_e( 'Full Description', 'event-management-platform' ); ?></label>
                        <?php 
                        $content = $event ? $event->full_description : '';
                        wp_editor( $content, 'full_description', [
                            'textarea_name' => 'full_description',
                            'media_buttons' => true,
                            'textarea_rows' => 12,
                            'tinymce'       => true,
                            'quicktags'     => true
                        ] );
                        ?>
                    </div>
                </div>

                <!-- Content Sections Repeater -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Content Sections', 'event-management-platform' ); ?></h2>
                    <p style="color:var(--emp-text-muted); margin-top:0;"><?php esc_html_e( 'Add extra content blocks, each with a title and description.', 'event-management-platform' ); ?></p>

                    <div id="emp-sections-repeater-container">
                        <?php
                        if ( $event && ! empty( $event->sections ) ) {
                            foreach ( $event->sections as $index => $section ) {
                                ?>
                                <div class="emp-section-repeater-item" style="border:1px solid var(--emp-border); border-radius:8px; padding:16px; margin-bottom:12px; background:rgba(0,0,0,0.1);">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                                        <strong style="color:var(--emp-text);"><?php echo esc_html__( 'Section', 'event-management-platform' ) . ' ' . ( $index + 1 ); ?></strong>
                                        <button type="button" class="emp-section-remove emp-btn emp-btn-danger" style="padding:4px 10px; font-size:12px;"><?php esc_html_e( 'Remove', 'event-management-platform' ); ?></button>
                                    </div>
                                    <div class="emp-form-group">
                                        <label><?php esc_html_e( 'Section Title', 'event-management-platform' ); ?></label>
                                        <input type="text" name="sections[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $section->title ); ?>" class="emp-form-control" placeholder="<?php esc_attr_e( 'e.g. About the Artist', 'event-management-platform' ); ?>" />
                                    </div>
                                    <div class="emp-form-group">
                                        <label><?php esc_html_e( 'Section Description', 'event-management-platform' ); ?></label>
                                        <textarea name="sections[<?php echo $index; ?>][content]" class="emp-form-control" rows="5"><?php echo esc_textarea( $section->content ); ?></textarea>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <button type="button" id="emp-add-section" class="emp-btn emp-btn-secondary" style="margin-top:10px;">
                        <?php esc_html_e( '+ Add Section', 'event-management-platform' ); ?>
                    </button>
                </div>

                <!-- Media Gallery -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Image Gallery', 'event-management-platform' ); ?></h2>
                    <p style="color:var(--emp-text-muted);"><?php esc_html_e( 'Manage additional images for the event slider/gallery.', 'event-management-platform' ); ?></p>
                    
                    <button type="button" id="emp-add-gallery-images" class="emp-btn emp-btn-secondary">
                        <?php esc_html_e( 'Add Images to Gallery', 'event-management-platform' ); ?>
                    </button>

                    <input type="hidden" id="emp-gallery-ids" name="gallery_attachment_ids" value="<?php echo esc_attr( $event ? implode( ',', $event->gallery ) : '' ); ?>" />

                    <div id="emp-gallery-container" class="emp-gallery-grid">
                        <?php 
                        if ( $event && ! empty( $event->gallery ) ) {
                            foreach ( $event->gallery as $attachment_id ) {
                                $img_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                                if ( $img_url ) {
                                    echo '<div class="emp-gallery-item" data-id="' . esc_attr( $attachment_id ) . '">';
                                    echo '<img src="' . esc_url( $img_url ) . '" />';
                                    echo '<div class="emp-gallery-remove">&times;</div>';
                                    echo '</div>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- FAQ Repeater -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Frequently Asked Questions (FAQs)', 'event-management-platform' ); ?></h2>
                    <div id="emp-faq-repeater-container">
                        <?php 
                        if ( $event && ! empty( $event->faqs ) ) {
                            foreach ( $event->faqs as $index => $faq ) {
                                ?>
                                <div class="emp-faq-repeater-item">
                                    <button class="emp-faq-remove"><?php esc_html_e( 'Remove', 'event-management-platform' ); ?></button>
                                    <div class="emp-form-group">
                                        <label><?php esc_html_e( 'Question', 'event-management-platform' ); ?></label>
                                        <input type="text" name="faqs[<?php echo $index; ?>][question]" value="<?php echo esc_attr( $faq->question ); ?>" class="emp-form-control" />
                                    </div>
                                    <div class="emp-form-group">
                                        <label><?php esc_html_e( 'Answer', 'event-management-platform' ); ?></label>
                                        <textarea name="faqs[<?php echo $index; ?>][answer]" class="emp-form-control" rows="3"><?php echo esc_textarea( $faq->answer ); ?></textarea>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <button type="button" id="emp-add-faq" class="emp-btn emp-btn-secondary" style="margin-top: 15px;">
                        <?php esc_html_e( '+ Add FAQ Item', 'event-management-platform' ); ?>
                    </button>
                </div>

                <!-- SEO Options -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'SEO Settings', 'event-management-platform' ); ?></h2>
                    <div class="emp-form-group">
                        <label for="emp-seo-title"><?php esc_html_e( 'Meta Title', 'event-management-platform' ); ?></label>
                        <input type="text" id="emp-seo-title" name="seo_title" value="<?php echo esc_attr( $event ? $event->seo_title : '' ); ?>" class="emp-form-control" placeholder="<?php esc_attr_e( 'Leave blank to use event title', 'event-management-platform' ); ?>" />
                    </div>
                    <div class="emp-form-group">
                        <label for="emp-seo-desc"><?php esc_html_e( 'Meta Description', 'event-management-platform' ); ?></label>
                        <textarea id="emp-seo-desc" name="seo_description" class="emp-form-control" rows="3" placeholder="<?php esc_attr_e( 'Leave blank to use event short description', 'event-management-platform' ); ?>"><?php echo esc_textarea( $event ? $event->seo_description : '' ); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Meta details -->
            <div style="flex: 1; min-width: 250px;">
                <!-- Publish / Save card -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Publish Settings', 'event-management-platform' ); ?></h2>

                    <div class="emp-form-group">
                        <label for="emp-event-status"><?php esc_html_e( 'Status', 'event-management-platform' ); ?></label>
                        <select id="emp-event-status" name="status" class="emp-form-control">
                            <option value="draft" <?php selected( $event ? $event->status : 'draft', 'draft' ); ?>><?php esc_html_e( 'Draft', 'event-management-platform' ); ?></option>
                            <option value="published" <?php selected( $event ? $event->status : '', 'published' ); ?>><?php esc_html_e( 'Published', 'event-management-platform' ); ?></option>
                            <option value="cancelled" <?php selected( $event ? $event->status : '', 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'event-management-platform' ); ?></option>
                        </select>
                    </div>

                    <div class="emp-form-group">
                        <label for="emp-event-timezone"><?php esc_html_e( 'Timezone', 'event-management-platform' ); ?></label>
                        <input type="text" id="emp-event-timezone" name="timezone" value="<?php echo esc_attr( $event ? $event->timezone : 'UTC' ); ?>" class="emp-form-control" />
                    </div>

                    <div class="emp-form-group">
                        <label for="emp-event-start"><?php esc_html_e( 'Start Datetime', 'event-management-platform' ); ?></label>
                        <input type="datetime-local" id="emp-event-start" name="start_datetime" value="<?php echo esc_attr( $event && $event->start_datetime ? date( 'Y-m-d\TH:i', strtotime( $event->start_datetime ) ) : '' ); ?>" class="emp-form-control" />
                    </div>

                    <div class="emp-form-group">
                        <label for="emp-event-end"><?php esc_html_e( 'End Datetime', 'event-management-platform' ); ?></label>
                        <input type="datetime-local" id="emp-event-end" name="end_datetime" value="<?php echo esc_attr( $event && $event->end_datetime ? date( 'Y-m-d\TH:i', strtotime( $event->end_datetime ) ) : '' ); ?>" class="emp-form-control" />
                    </div>

                    <button type="submit" name="emp_save_event" class="emp-btn" style="width: 100%; margin-top: 15px;">
                        <?php esc_html_e( 'Save Event', 'event-management-platform' ); ?>
                    </button>
                </div>

                <!-- Featured Image -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Featured Image', 'event-management-platform' ); ?></h2>
                    
                    <div id="emp-featured-image-preview" style="margin-bottom: 15px; text-align: center;">
                        <?php 
                        if ( $event && $event->featured_image_id ) {
                            $img_url = wp_get_attachment_image_url( $event->featured_image_id, 'medium' );
                            if ( $img_url ) {
                                echo '<img src="' . esc_url( $img_url ) . '" style="max-width: 100%; height: auto; border-radius: 8px;" />';
                            }
                        }
                        ?>
                    </div>

                    <input type="hidden" id="emp-featured-image-id" name="featured_image_id" value="<?php echo esc_attr( $event ? $event->featured_image_id : '' ); ?>" />
                    
                    <div style="display: flex; gap: 8px;">
                        <button type="button" id="emp-select-featured-image" class="emp-btn emp-btn-secondary" style="flex: 1;">
                            <?php esc_html_e( 'Set Image', 'event-management-platform' ); ?>
                        </button>
                        <button type="button" id="emp-remove-featured-image" class="emp-btn emp-btn-danger" style="display: <?php echo $event && $event->featured_image_id ? 'inline-flex' : 'none'; ?>;">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Venue dropdown select -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Venue Location', 'event-management-platform' ); ?></h2>
                    <div class="emp-form-group">
                        <select name="venue_id" class="emp-form-control">
                            <option value=""><?php esc_html_e( '-- Select Venue --', 'event-management-platform' ); ?></option>
                            <?php foreach ( $venues as $v ) : ?>
                                <option value="<?php echo esc_attr( $v->id ); ?>" <?php selected( $event ? $event->venue_id : '', $v->id ); ?>>
                                    <?php echo esc_html( $v->name ); ?> (<?php echo esc_html( $v->city ); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-venues&action=add' ) ); ?>" style="font-size:12px; color:var(--emp-primary); text-decoration:none;">
                        <?php esc_html_e( '+ Add New Venue', 'event-management-platform' ); ?>
                    </a>
                </div>

                <!-- Category dropdown select -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Event Category', 'event-management-platform' ); ?></h2>
                    <div class="emp-form-group">
                        <select name="category_id" class="emp-form-control">
                            <option value=""><?php esc_html_e( '-- Select Category --', 'event-management-platform' ); ?></option>
                            <?php foreach ( $categories as $c ) : ?>
                                <option value="<?php echo esc_attr( $c->id ); ?>" <?php selected( $event ? $event->category_id : '', $c->id ); ?>>
                                    <?php echo esc_html( $c->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-categories&action=add' ) ); ?>" style="font-size:12px; color:var(--emp-primary); text-decoration:none;">
                        <?php esc_html_e( '+ Add New Category', 'event-management-platform' ); ?>
                    </a>
                </div>

                <!-- Organizers checklist -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Organizers / Promoters', 'event-management-platform' ); ?></h2>
                    <div class="emp-form-group" style="max-height: 180px; overflow-y: auto; border: 1px solid var(--emp-border); padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.15);">
                        <?php 
                        $assigned_org_ids = [];
                        if ( $event && ! empty( $event->organizers ) ) {
                            $assigned_org_ids = array_map( fn($o) => $o->id, $event->organizers );
                        }
                        
                        if ( empty( $organizers ) ) : ?>
                            <p style="font-size:12px; color:var(--emp-text-muted); margin:0;"><?php esc_html_e( 'No organizers created yet.', 'event-management-platform' ); ?></p>
                        <?php else :
                            foreach ( $organizers as $o ) : ?>
                                <label style="display:flex; align-items:center; gap:8px; margin-bottom:8px; font-weight:normal; font-size:13px; cursor:pointer;">
                                    <input type="checkbox" name="organizer_ids[]" value="<?php echo esc_attr( $o->id ); ?>" <?php checked( in_array( $o->id, $assigned_org_ids, true ) ); ?> />
                                    <?php echo esc_html( $o->name ); ?>
                                </label>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-organizers&action=add' ) ); ?>" style="font-size:12px; color:var(--emp-primary); text-decoration:none;">
                        <?php esc_html_e( '+ Add New Organizer', 'event-management-platform' ); ?>
                    </a>
                </div>

                <!-- Tags comma input -->
                <div class="emp-card">
                    <h2><?php esc_html_e( 'Tags', 'event-management-platform' ); ?></h2>
                    <div class="emp-form-group">
                        <input type="text" name="tags" value="<?php echo esc_attr( $event ? implode( ', ', $event->tags ) : '' ); ?>" class="emp-form-control" placeholder="e.g. concert, virtual, rock" />
                        <span style="font-size: 11px; color: var(--emp-text-muted);"><?php esc_html_e( 'Separate tags with commas', 'event-management-platform' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
