<?php
// Toast component
function renderToast($message, $type = 'info', $attributes = []) {
    $classes = 'fixed bottom-4 right-4 z-50 p-4 rounded-md shadow-lg';

    if ($type === 'success') {
        $classes .= ' bg-success text-white';
    } elseif ($type === 'warning') {
        $classes .= ' bg-warning text-white';
    } elseif ($type === 'error') {
        $classes .= ' bg-error text-white';
    } else {
        $classes .= ' bg-info text-white';
    }

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    echo '<div class="' . $classes . '"' . $attrString . '>' . htmlspecialchars($message) . '</div>';
}
?>