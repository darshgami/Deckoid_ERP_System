<?php
// Loading component
function renderLoading($message = 'Loading...', $attributes = []) {
    $classes = 'flex items-center justify-center p-4';

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    $html = '<div class="' . $classes . '"' . $attrString . '>';
    $html .= '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>';
    $html .= '<span class="ml-2 text-neutral-600">' . htmlspecialchars($message) . '</span>';
    $html .= '</div>';

    echo $html;
}
?>