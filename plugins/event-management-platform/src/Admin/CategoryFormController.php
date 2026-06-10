<?php

namespace EventManagementPlatform\Admin;

use EventManagementPlatform\Services\CategoryService;
use Exception;

class CategoryFormController {
    private CategoryService $service;

    public function __construct() {
        $this->service = new CategoryService();
    }

    public function render(): void {
        $action = sanitize_text_field( $_GET['action'] ?? 'list' );
        $id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : null;
        $errors = [];
        $notice = '';

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['emp_save_category'] ) ) {
            check_admin_referer( 'emp_save_category', 'emp_save_category_nonce' );

            $cat_data = [
                'name'      => sanitize_text_field( $_POST['name'] ?? '' ),
                'slug'      => sanitize_title( $_POST['slug'] ?? '' ),
                'parent_id' => ! empty( $_POST['parent_id'] ) ? (int) $_POST['parent_id'] : null,
            ];

            try {
                if ( $id ) {
                    $this->service->updateCategory( $id, $cat_data );
                    $notice = __( 'Category updated successfully.', 'event-management-platform' );
                } else {
                    $id = $this->service->createCategory( $cat_data );
                    $notice = __( 'Category created successfully.', 'event-management-platform' );
                    $action = 'edit';
                }
            } catch ( Exception $e ) {
                $errors = json_decode( $e->getMessage(), true ) ?: [ 'general' => $e->getMessage() ];
            }
        }

        if ( $action === 'delete' && $id ) {
            check_admin_referer( 'emp_delete_category_' . $id );
            if ( $this->service->deleteCategory( $id ) ) {
                wp_redirect( admin_url( 'admin.php?page=emp-categories&deleted=true' ) );
                exit;
            }
        }

        if ( isset( $_GET['deleted'] ) && $_GET['deleted'] === 'true' ) {
            $notice = __( 'Category deleted successfully.', 'event-management-platform' );
        }

        $all_categories = $this->service->getCategoryRepository()->all();

        if ( $action === 'add' || $action === 'edit' ) {
            $category = null;
            if ( $action === 'edit' && $id ) {
                $category = $this->service->getCategory( $id );
            }
            include EMP_PATH . 'templates/admin/category-form.php';
        } else {
            $page     = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1;
            $per_page = 10;
            $search   = sanitize_text_field( $_GET['s'] ?? '' );
            
            $filters = [];
            if ( ! empty( $search ) ) {
                $filters['search'] = $search;
            }

            $results    = $this->service->getCategoryRepository()->paginate( $page, $per_page, $filters );
            $categories = $results['items'];
            $total      = $results['total'];
            $pages      = $results['pages'];

            include EMP_PATH . 'templates/admin/category-list.php';
        }
    }
}
