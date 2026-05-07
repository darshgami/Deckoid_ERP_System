<?php
// Modal component
function renderModal($id, $title, $content, $attributes = []) {
    $classes = 'fixed inset-0 z-50 overflow-y-auto';

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    $html = '<div id="' . htmlspecialchars($id) . '" class="' . $classes . '"' . $attrString . ' style="display: none;">';
    $html .= '<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">';
    $html .= '<div class="fixed inset-0 bg-neutral-500 bg-opacity-75 transition-opacity" onclick="closeModal(\'' . htmlspecialchars($id) . '\')"></div>';
    $html .= '<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">';
    $html .= '<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">';
    $html .= '<div class="sm:flex sm:items-start">';
    $html .= '<div class="mt-3 text-center sm:mt-0 sm:text-left w-full">';
    $html .= '<h3 class="text-lg leading-6 font-medium text-neutral-900">' . htmlspecialchars($title) . '</h3>';
    $html .= '<div class="mt-2">' . $content . '</div>';
    $html .= '</div></div></div>';
    $html .= '<div class="bg-neutral-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">';
    $html .= '<button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-neutral-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-neutral-700 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal(\'' . htmlspecialchars($id) . '\')">Close</button>';
    $html .= '</div></div></div></div>';

    echo $html;
}
?>