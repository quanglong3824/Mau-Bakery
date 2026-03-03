<?php
// admin/includes/functions.php

/**
 * Hỗ trợ phân trang
 * @param int $total_records Tổng số bản ghi
 * @param int $current_page Trang hiện tại
 * @param int $limit Số bản ghi trên mỗi trang
 * @return array [offset, total_pages]
 */
function get_pagination_params($total_records, $current_page, $limit) {
    $total_pages = ceil($total_records / $limit);
    if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;
    if ($current_page < 1) $current_page = 1;
    $offset = ($current_page - 1) * $limit;
    
    return [
        'offset' => $offset,
        'total_pages' => $total_pages,
        'current_page' => $current_page
    ];
}

/**
 * Hiển thị giao diện phân trang (Bootstrap style)
 */
function render_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) return '';
    
    $html = '<nav aria-label="Page navigation" class="mt-4"><ul class="pagination justify-content-center">';
    
    // Previous
    $prev_class = ($current_page <= 1) ? 'disabled' : '';
    $prev_url = ($current_page > 1) ? $base_url . '&page_no=' . ($current_page - 1) : '#';
    $html .= "<li class='page-item $prev_class'><a class='page-link' href='$prev_url'>&laquo;</a></li>";
    
    // Pages
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = ($i == $current_page) ? 'active' : '';
        $html .= "<li class='page-item $active_class'><a class='page-link' href='{$base_url}&page_no=$i'>$i</a></li>";
    }
    
    // Next
    $next_class = ($current_page >= $total_pages) ? 'disabled' : '';
    $next_url = ($current_page < $total_pages) ? $base_url . '&page_no=' . ($current_page + 1) : '#';
    $html .= "<li class='page-item $next_class'><a class='page-link' href='$next_url'>&raquo;</a></li>";
    
    $html .= '</ul></nav>';
    return $html;
}
?>