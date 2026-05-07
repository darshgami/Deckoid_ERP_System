<?php
// Card component
function renderCard($content, $attributes = []) {
    $classes = 'bg-white overflow-hidden shadow rounded-lg';

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    echo '<div class="' . $classes . '"' . $attrString . '>' . $content . '</div>';
}
?>