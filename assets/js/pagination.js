/**
 * Reusable Pagination Component for Deckoid ERP
 * Matches requirements from Phase 6 of prompt.md
 */

function renderPagination(pagination, onPageChange) {
    const container = document.getElementById('pagination');
    if (!container) return;
    
    container.innerHTML = '';
    
    // Fallback for missing pagination data
    if (!pagination || pagination.pages <= 1) return;

    // Previous Button
    const prevBtn = document.createElement('button');
    prevBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
    prevBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page > 1 ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
    prevBtn.onclick = () => {
        if (pagination.page > 1) onPageChange(pagination.page - 1);
    };
    container.appendChild(prevBtn);

    // Desktop/Full Pagination Logic
    const maxPages = pagination.pages;
    const current = pagination.page;
    
    // Page Numbers (Desktop View)
    let start = Math.max(1, current - 2);
    let end = Math.min(maxPages, start + 4);
    if (end - start < 4) {
        start = Math.max(1, end - 4);
    }

    // Add first page and ellipsis if needed
    if (start > 1) {
        container.appendChild(createPageBtn(1, current, onPageChange));
        if (start > 2) container.appendChild(createEllipsis());
    }

    // Add numbered pages
    for (let i = start; i <= end; i++) {
        container.appendChild(createPageBtn(i, current, onPageChange));
    }

    // Add last page and ellipsis if needed
    if (end < maxPages) {
        if (end < maxPages - 1) container.appendChild(createEllipsis());
        container.appendChild(createPageBtn(maxPages, current, onPageChange));
    }

    // Next Button
    const nextBtn = document.createElement('button');
    nextBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
    nextBtn.className = `w-9 h-9 rounded-xl flex items-center justify-center transition-all ${pagination.page < pagination.pages ? 'text-neutral-500 hover:bg-neutral-100' : 'text-neutral-200 cursor-not-allowed'}`;
    nextBtn.onclick = () => {
        if (pagination.page < pagination.pages) onPageChange(pagination.page + 1);
    };
    container.appendChild(nextBtn);
    
    // Add Mobile View Logic (Page X of Y)
    // We achieve this via responsive CSS hiding the numbered buttons on small screens,
    // and showing a text node instead.
    
    const mobileText = document.createElement('span');
    mobileText.className = 'md:hidden text-xs font-bold text-neutral-500 uppercase mx-2';
    mobileText.textContent = `Page ${current} of ${maxPages}`;
    
    // Insert mobile text in the middle
    container.insertBefore(mobileText, nextBtn);
}

function createPageBtn(page, current, onPageChange) {
    const btn = document.createElement('button');
    btn.textContent = page;
    btn.className = `hidden md:flex w-9 h-9 rounded-xl items-center justify-center shrink-0 font-bold text-xs transition-all ${page === current ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-105' : 'text-neutral-500 hover:bg-neutral-100'}`;
    btn.onclick = () => onPageChange(page);
    return btn;
}

function createEllipsis() {
    const span = document.createElement('span');
    span.className = 'hidden md:flex w-9 h-9 items-end justify-center pb-2 text-neutral-400 font-bold';
    span.textContent = '...';
    return span;
}

function updatePaginationInfo(pagination, textElementId = 'paginationInfo', itemName = 'records') {
    const infoEl = document.getElementById(textElementId);
    if (!infoEl) return;
    
    if (!pagination || pagination.total === 0) {
        infoEl.textContent = `No ${itemName} to display`;
        return;
    }
    
    const start = ((pagination.page - 1) * pagination.limit) + 1;
    const end = Math.min(pagination.page * pagination.limit, pagination.total);
    infoEl.textContent = `Showing ${start} to ${end} of ${pagination.total} ${itemName}`;
}
