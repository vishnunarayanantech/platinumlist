<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="emp-admin-wrap">
    <div class="emp-admin-header">
        <h1><?php echo $category ? esc_html__( 'Edit Category', 'event-management-platform' ) : esc_html__( 'Add New Category', 'event-management-platform' ); ?></h1>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=emp-categories' ) ); ?>" class="emp-btn emp-btn-secondary">
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

    <div class="emp-card" style="max-width: 600px;">
        <form method="post" action="">
            <?php wp_nonce_field( 'emp_save_category', 'emp_save_category_nonce' ); ?>

            <div class="emp-form-group">
                <label for="emp-cat-name"><?php esc_html_e( 'Category Name', 'event-management-platform' ); ?> *</label>
                <input type="text" id="emp-cat-name" name="name" value="<?php echo esc_attr( $category ? $category->name : '' ); ?>" class="emp-form-control" required />
            </div>

            <div class="emp-form-group">
                <label for="emp-cat-slug"><?php esc_html_e( 'Category Slug', 'event-management-platform' ); ?></label>
                <input type="text" id="emp-cat-slug" name="slug" value="<?php echo esc_attr( $category ? $category->slug : '' ); ?>" class="emp-form-control" placeholder="<?php esc_attr_e( 'Auto-generated from name if left empty', 'event-management-platform' ); ?>" />
            </div>

            <div class="emp-form-group">
                <label for="emp-cat-parent"><?php esc_html_e( 'Parent Category', 'event-management-platform' ); ?></label>
                <select id="emp-cat-parent" name="parent_id" class="emp-form-control">
                    <option value=""><?php esc_html_e( '-- None --', 'event-management-platform' ); ?></option>
                    <?php foreach ( $all_categories as $ac ) : ?>
                        <?php 
                        // Avoid selecting itself as parent
                        if ( $category && $ac->id === $category->id ) {
                            continue;
                        }
                        ?>
                        <option value="<?php echo esc_attr( $ac->id ); ?>" <?php selected( $category ? $category->parent_id : '', $ac->id ); ?>>
                            <?php echo esc_html( $ac->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" name="emp_save_category" class="emp-btn">
                <?php esc_html_e( 'Save Category', 'event-management-platform' ); ?>
            </button>
        </form>
    </div>
</div>
