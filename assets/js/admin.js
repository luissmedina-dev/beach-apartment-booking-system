/**
 * admin.js
 * Scripts da área administrativa
 *
 * O título da topbar é definido pelo PHP via atributo data:
 *   <header class="admin-topbar" data-page-title="Dashboard">
 * Isso elimina o <script> inline em cada página admin.
 */
(function () {
    'use strict';

    // Preenche o título da topbar a partir do data-attribute do header
    var topbar = document.querySelector('.admin-topbar[data-page-title]');
    var titleEl = document.getElementById('adminTopbarTitle');
    if (topbar && titleEl) {
        titleEl.textContent = topbar.getAttribute('data-page-title');
    }

    // Auto-submit do select de filtro no dashboard (sem onchange inline)
    var filterSelect = document.getElementById('status');
    if (filterSelect && filterSelect.closest('.admin-filter-form')) {
        filterSelect.addEventListener('change', function () {
            this.closest('form').submit();
        });
    }

}());
