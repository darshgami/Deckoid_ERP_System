<?php
// Input component
function renderInput($name, $label = '', $type = 'text', $value = '', $attributes = []) {
    $classes = 'block w-full rounded-md border-neutral-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm';

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    $html = '';
    if ($label) {
        $html .= '<label for="' . htmlspecialchars($name) . '" class="block text-sm font-medium text-neutral-700">' . htmlspecialchars($label) . '</label>';
    }
    $html .= '<input type="' . htmlspecialchars($type) . '" name="' . htmlspecialchars($name) . '" id="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" class="' . $classes . '"' . $attrString . '>';

    echo $html;
}
?>