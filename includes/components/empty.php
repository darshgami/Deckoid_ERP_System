<?php
// Empty state component
function renderEmptyState($title, $description, $attributes = []) {
    $classes = 'text-center py-12';

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    $html = '<div class="' . $classes . '"' . $attrString . '>';
    $html .= '<svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
    $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />';
    $html .= '</svg>';
    $html .= '<h3 class="mt-2 text-sm font-medium text-neutral-900">' . htmlspecialchars($title) . '</h3>';
    $html .= '<p class="mt-1 text-sm text-neutral-500">' . htmlspecialchars($description) . '</p>';
    $html .= '</div>';

    echo $html;
}
?>