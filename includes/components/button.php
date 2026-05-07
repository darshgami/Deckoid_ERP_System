<?php
// Button component
function renderButton($text, $type = 'primary', $attributes = []) {
    $classes = 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed';

    if ($type === 'primary') {
        $classes .= ' bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 disabled:bg-primary-300';
    } elseif ($type === 'secondary') {
        $classes .= ' border border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-50 focus:ring-primary-500';
    }

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    echo '<button class="' . $classes . '"' . $attrString . '>' . htmlspecialchars($text) . '</button>';
}
?>