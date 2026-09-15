<?php

$role = strtoupper(session('role') ?? '');

$isWriter  = $role === 'WRITER';
$canCreate = \App\Libraries\Permissions::can('article', 'create', $role);

$dataUrl   = site_url('news/data');
$deleteUrl = site_url('news/delete');
$editUrl   = site_url('create-article');
$viewUrl   = site_url('editor/view-article');

?>

<script>

const NEWS_ARTICLE_CONFIG = <?= json_encode([
    'role'      => $role,
    'isWriter'  => $isWriter,
    'canCreate' => $canCreate,
    'dataUrl'   => $dataUrl,
    'deleteUrl' => $deleteUrl,
    'editUrl'   => $editUrl,
    'viewUrl'   => $viewUrl,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;


class NewsArticle {
    constructor(config = {}) {
        this.config = {
            role: '',
            isWriter: false,
            dataUrl: '',
            deleteUrl: '',
            editUrl: '',
            viewUrl: '',
            ...config
        };

        this.currentPage = 1;
        this.pageSize = 10;
        this.totalEntries = 0;

        this.deleteTarget = null;
        this.searchTimer = null;
    }


    init() {
        this.bindEvents();
        this.fetchArticles();
    }


    bindEvents() {

        const searchInput =
            document.getElementById('searchInput');

        const prevBtn =
            document.getElementById('prevBtn');

        const nextBtn =
            document.getElementById('nextBtn');

        const tableBody =
            document.getElementById('tableBody');

        const confirmDeleteBtn =
            document.getElementById('confirmDeleteBtn');

        searchInput?.addEventListener(
            'input',
            () => this.filterTable()
        );


        /*
         * Previous page
         */
        prevBtn?.addEventListener(
            'click',
            () => this.changePage(-1)
        );


        /*
         * Next page
         */
        nextBtn?.addEventListener(
            'click',
            () => this.changePage(1)
        );


        /*
         * Delete article
         */
        if (this.config.isWriter) {

            tableBody?.addEventListener(
                'click',
                event => {

                    const button =
                        event.target.closest(
                            '.js-delete-article'
                        );

                    if (!button) {
                        return;
                    }

                    this.openDeleteModal(
                        button.dataset.id
                    );
                }
            );


            confirmDeleteBtn?.addEventListener(
                'click',
                () => this.confirmDelete()
            );
        }
    }


    csrfHeaders() {

        const nameElement =
            document.querySelector(
                'meta[name="csrf-name"]'
            );

        const tokenElement =
            document.querySelector(
                'meta[name="csrf-token"]'
            );


        if (!nameElement || !tokenElement) {

            return {
                'X-Requested-With':
                    'XMLHttpRequest'
            };
        }


        return {

            'X-Requested-With':
                'XMLHttpRequest',

            [nameElement.content]:
                tokenElement.content
        };
    }


    escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }


    async fetchArticles() {

        const searchInput =
            document.getElementById(
                'searchInput'
            );

        const search =
            searchInput?.value?.trim() ?? '';


        if (!this.config.dataUrl) {

            console.error(
                'News Article data URL is not configured.'
            );

            return;
        }


        try {

            const url = new URL(
                this.config.dataUrl,
                window.location.origin
            );


            url.searchParams.set(
                'page',
                this.currentPage
            );

            url.searchParams.set(
                'perPage',
                this.pageSize
            );

            url.searchParams.set(
                'search',
                search
            );


            const response =
                await fetch(
                    url.toString(),
                    {
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    `HTTP ${response.status}`
                );
            }


            const json =
                await response.json();


            this.totalEntries =
                Number(json.total ?? 0);


            this.renderTable(
                json.data ?? []
            );

        }
        catch (error) {

            console.error(
                'Failed to fetch articles:',
                error
            );

            this.renderError();
        }
    }


    renderTable(rows) {

        const tbody =
            document.getElementById(
                'tableBody'
            );

        const pageInfo =
            document.getElementById(
                'pageInfo'
            );

        const prevBtn =
            document.getElementById(
                'prevBtn'
            );

        const nextBtn =
            document.getElementById(
                'nextBtn'
            );


        if (!tbody) {
            return;
        }


        /*
         * No data
         */
        if (!rows.length) {

            tbody.innerHTML = `
                <tr>
                    <td
                        colspan="5"
                        class="no-data"
                    >
                        No data available
                    </td>
                </tr>
            `;


            if (pageInfo) {

                pageInfo.textContent =
                    'Showing 0 to 0 of 0 entries';
            }


            if (prevBtn) {
                prevBtn.disabled = true;
            }


            if (nextBtn) {
                nextBtn.disabled = true;
            }


            return;
        }


        const start =
            (this.currentPage - 1) *
            this.pageSize;


        tbody.innerHTML =
            rows.map((article, index) => {

                const summary =
                    article.summary ?? '';


                const shortSummary =
                    summary.length > 80
                        ? `${summary.substring(0, 80)}...`
                        : summary;

                const isLocked =
                    !!article.is_locked;

                const rowClass =
                    isLocked ? 'row-locked' : '';

                const lockBadge =
                    isLocked
                        ? `
                            <span
                                class="badge bg-secondary ms-1"
                                title="Being edited by ${this.escapeHtml(article.locked_by_name)}"
                            >
                                <i class="fa fa-lock"></i>
                                ${this.escapeHtml(article.locked_by_name)}
                            </span>
                        `
                        : '';


                return `
                    <tr class="${rowClass}">

                        <td>
                            ${start + index + 1}
                        </td>

                        <td>
                            <span
                                class="fw-semibold id-chip"
                            >
                                ${this.escapeHtml(article.id)}
                            </span>
                            ${lockBadge}
                        </td>

                        <td>
                            ${this.escapeHtml(shortSummary)}
                        </td>

                        <td>
                            ${this.escapeHtml(article.news_date)}
                        </td>

                        <td>
                            ${this.renderActions(article)}
                        </td>

                    </tr>
                `;

            }).join('');


        const end =
            Math.min(
                start + this.pageSize,
                this.totalEntries
            );


        if (pageInfo) {

            pageInfo.textContent =
                `Showing ${start + 1} to ${end} of ${this.totalEntries} entries`;
        }


        if (prevBtn) {

            prevBtn.disabled =
                this.currentPage === 1;
        }


        if (nextBtn) {

            nextBtn.disabled =
                end >= this.totalEntries;
        }
    }


    renderActions(article) {

        const encodedId =
            encodeURIComponent(
                article.id ?? ''
            );


        /*
         * WRITER
         */
        if (this.config.isWriter) {

            return `
                
                    href="${this.config.editUrl}?id=${encodedId}"
                    class="btn btn-edit me-1"
                >
                    <i class="fa fa-pen"></i>
                    Edit
                </a>

                <button
                    type="button"
                    class="btn btn-del js-delete-article"
                    data-id="${this.escapeHtml(article.id)}"
                >
                    <i class="fa fa-trash"></i>
                    Delete
                </button>
            `;
        }


        /*
         * EDITOR / OTHER ROLES — locked by another editor
         */
        if (article.is_locked && !article.locked_by_self) {

            return `
                <button
                    type="button"
                    class="btn btn-edit"
                    disabled
                    title="Locked by ${this.escapeHtml(article.locked_by_name)}"
                >
                    <i class="fa fa-lock"></i>
                    Locked
                </button>
            `;
        }


        /*
         * EDITOR / OTHER ROLES — open
         */
        return `
            
                href="${this.config.viewUrl}/${encodedId}"
                class="btn btn-edit"
            >
                <i class="fa fa-eye"></i>
                View
            </a>
        `;
    }


    filterTable() {

        clearTimeout(
            this.searchTimer
        );


        this.searchTimer =
            setTimeout(() => {

                this.currentPage = 1;

                this.fetchArticles();

            }, 300);
    }


    changePage(direction) {

        const nextPage =
            this.currentPage + direction;


        if (nextPage < 1) {
            return;
        }


        const maxPage =
            Math.max(
                1,
                Math.ceil(
                    this.totalEntries /
                    this.pageSize
                )
            );


        if (nextPage > maxPage) {
            return;
        }


        this.currentPage =
            nextPage;


        this.fetchArticles();
    }


    openDeleteModal(id) {

        if (!this.config.isWriter) {
            return;
        }


        this.deleteTarget = id;


        const deleteId =
            document.getElementById(
                'deleteId'
            );


        if (deleteId) {

            deleteId.textContent =
                id;
        }


        const modalElement =
            document.getElementById(
                'deleteModal'
            );


        if (!modalElement) {
            return;
        }


        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );


        modal.show();
    }


    async confirmDelete() {

        if (
            !this.config.isWriter ||
            !this.deleteTarget
        ) {
            return;
        }


        try {

            const response =
                await fetch(
                    `${this.config.deleteUrl}/${encodeURIComponent(this.deleteTarget)}`,
                    {
                        method: 'DELETE',

                        headers:
                            this.csrfHeaders()
                    }
                );


            if (!response.ok) {

                throw new Error(
                    `HTTP ${response.status}`
                );
            }


            const json =
                await response.json();


            const modalElement =
                document.getElementById(
                    'deleteModal'
                );


            if (modalElement) {

                const modal =
                    bootstrap.Modal.getInstance(
                        modalElement
                    );

                modal?.hide();
            }


            if (json.status) {

                this.deleteTarget =
                    null;

                await this.fetchArticles();

            }
            else {

                alert(
                    json.message ||
                    'Unable to delete article.'
                );
            }

        }
        catch (error) {

            console.error(
                'Delete article:',
                error
            );

            alert(
                'An error occurred while deleting the article.'
            );
        }
    }


    renderError() {

        const tbody =
            document.getElementById(
                'tableBody'
            );


        if (!tbody) {
            return;
        }


        tbody.innerHTML = `
            <tr>
                <td
                    colspan="5"
                    class="no-data text-danger"
                >
                    Unable to load articles.
                </td>
            </tr>
        `;
    }
}

document.addEventListener(
    'DOMContentLoaded',
    () => {

        const newsArticle =
            new NewsArticle(
                NEWS_ARTICLE_CONFIG
            );


        newsArticle.init();

        window.newsArticle =
            newsArticle;
    }
);

</script>