<?php
/**
 * Deckoid ERP - Reusable UI Component Helpers
 * 
 * This file provides helper functions to render consistent UI components
 * using Tailwind CSS classes as per the design system.
 */

/**
 * Button Component
 * Variants: primary, secondary, danger, ghost
 */
function ui_button($label, $onclick = '', $variant = 'primary', $type = 'button', $extraClass = '') {
    $base = "px-6 py-3 font-bold rounded-2xl transition-all duration-200 flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50 disabled:pointer-events-none ";
    
    $variants = [
        'primary'   => 'bg-primary-600 text-white hover:bg-primary-700 shadow-lg shadow-primary-200',
        'secondary' => 'bg-white border border-neutral-200 text-neutral-700 hover:bg-neutral-50 shadow-sm',
        'danger'    => 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white',
        'ghost'     => 'bg-transparent text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900',
        'dark'      => 'bg-neutral-900 text-white hover:bg-neutral-800 shadow-lg'
    ];
    
    $class = $base . ($variants[$variant] ?? $variants['primary']) . ' ' . $extraClass;
    
    return "<button type=\"$type\" onclick=\"$onclick\" class=\"$class\">$label</button>";
}

/**
 * Status Badge Component
 */
function ui_badge($label, $type = 'info') {
    $base = "px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-widest ";
    
    $types = [
        'success' => 'bg-green-50 text-green-600',
        'warning' => 'bg-orange-50 text-orange-600',
        'error'   => 'bg-red-50 text-red-600',
        'info'    => 'bg-blue-50 text-blue-600',
        'hot'     => 'bg-red-50 text-red-600', // Alias
        'warm'    => 'bg-orange-50 text-orange-600', // Alias
        'cold'    => 'bg-blue-50 text-blue-600' // Alias
    ];
    
    $class = $base . ($types[strtolower($type)] ?? $types['info']);
    
    return "<span class=\"$class\">$label</span>";
}

/**
 * Card Component Wrapper
 */
function ui_card_start($extraClass = '', $padding = 'p-10') {
    return "<div class=\"bg-white rounded-[2.5rem] shadow-sm border border-neutral-100 overflow-hidden $padding $extraClass\">";
}

function ui_card_end() {
    return "</div>";
}

/**
 * Input Component
 */
function ui_input($name, $label = '', $placeholder = '', $type = 'text', $value = '', $required = false) {
    $html = "";
    if ($label) {
        $html .= "<label class=\"block text-sm font-bold text-neutral-700 ml-1 mb-2\">$label" . ($required ? ' *' : '') . "</label>";
    }
    $req = $required ? 'required' : '';
    $html .= "<input type=\"$type\" name=\"$name\" value=\"$value\" placeholder=\"$placeholder\" $req autocomplete=\"off\"
              class=\"w-full bg-neutral-50 border-transparent rounded-2xl py-3.5 px-5 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-medium\">";
    
    return $html;
}

/**
 * Select Component
 */
function ui_select($name, $options = [], $label = '', $selected = '', $required = false) {
    $html = "";
    if ($label) {
        $html .= "<label class=\"block text-sm font-bold text-neutral-700 ml-1 mb-2\">$label" . ($required ? ' *' : '') . "</label>";
    }
    $req = $required ? 'required' : '';
    $html .= "<select name=\"$name\" $req autocomplete=\"off\" class=\"w-full bg-neutral-50 border-transparent rounded-2xl py-3.5 px-5 focus:bg-white focus:border-primary-100 focus:ring-4 focus:ring-primary-50 transition-all outline-none text-sm font-medium cursor-pointer\">";
    foreach ($options as $val => $text) {
        $sel = ($val == $selected) ? 'selected' : '';
        $html .= "<option value=\"$val\" $sel>$text</option>";
    }
    $html .= "</select>";
    
    return $html;
}

/**
 * Empty State Component
 */
function ui_empty_state($title, $description, $icon = 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4') {
    return "
    <div class=\"flex flex-col items-center justify-center py-20 text-center\">
        <div class=\"w-24 h-24 bg-neutral-50 rounded-full flex items-center justify-center text-neutral-200 mb-6\">
            <svg class=\"w-10 h-10\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path d=\"$icon\"></path></svg>
        </div>
        <h3 class=\"text-xl font-bold text-neutral-900\">$title</h3>
        <p class=\"text-neutral-400 mt-2 max-w-xs mx-auto\">$description</p>
    </div>";
}
