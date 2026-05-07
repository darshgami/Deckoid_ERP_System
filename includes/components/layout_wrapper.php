<?php
/**
 * Layout Helper Functions
 */

function render_header($title = 'Deckoid ERP') {
    $pageTitle = $title;
    include __DIR__ . '/header.php';
}

function render_sidebar() {
    include __DIR__ . '/sidebar.php';
}

function render_navbar() {
    include __DIR__ . '/navbar.php';
}

function render_footer() {
    include __DIR__ . '/footer.php';
}

/**
 * Start the Page Layout
 */
function layout_start($title = 'Deckoid ERP') {
    render_header($title);
    render_sidebar();
    echo '<div class="lg:ml-72 min-h-screen flex flex-col transition-all duration-300">';
    render_navbar();
    echo '<main class="flex-1 p-8">';
}

/**
 * End the Page Layout
 */
function layout_end() {
    echo '</main></div>';
    render_footer();
}
