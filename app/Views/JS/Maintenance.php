<script>
    const BASE_URL = '<?= rtrim(site_url('maintenance'), '/') ?>';
    let currentTab = '<?= esc($tab, 'js') ?>';
    let csrfName  = '<?= csrf_token() ?>';
    let csrfToken = '<?= csrf_hash() ?>';
    let currentFields = <?= json_encode($fields) ?>;
    let currentLabel  = <?= json_encode($tabMeta['label']) ?>;

    let currentPermissions = {
        view: false,
        create: false,
        edit: false,
        delete: false
    };

    let bulkUploadModalInstance = null;
    let allItems    = [];
    let currentPage = 1;
    const pageSize  = 10;


    // =========================================================
    // TOAST
    // =========================================================

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toastMsg');

        toast.className =
            `toast align-items-center text-bg-${type} border-0`;

        document.getElementById('toastText').textContent = msg;

        new bootstrap.Toast(toast, {
            delay: 2200
        }).show();
    }


    // =========================================================
    // API
    // =========================================================

    async function apiGet(path) {
        const response = await fetch(`${BASE_URL}/${path}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        return response.json();
    }


    async function apiPost(path, body) {
        body.append(csrfName, csrfToken);

        const response = await fetch(`${BASE_URL}/${path}`, {
            method: 'POST',

            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },

            body
        });

        const data = await response.json();

        if (data.csrfToken) {
            csrfToken = data.csrfToken;
        }

        return data;
    }


    // =========================================================
    // HELPERS
    // =========================================================

    function escapeHtml(str) {
        const div = document.createElement('div');

        div.textContent = str ?? '';

        return div.innerHTML;
    }


    function fieldInputId(key, prefix) {
        return `${prefix}_${key}`;
    }


    // =========================================================
    // ADD FORM
    // =========================================================

    function renderAddForm(fields) {
        const row = document.getElementById('addFormRow');
        const bulkBtn = document.getElementById('bulkUploadBtn');

        /*
         * IMPORTANT:
         * Hide the complete Add form (and the Bulk Upload entry
         * point) when the current role does not have CREATE
         * permission for this specific module.
         */
        if (!currentPermissions.create) {
            row.innerHTML = '';
            row.style.display = 'none';
            bulkBtn.style.display = 'none';

            return;
        }

        row.style.display = '';
        bulkBtn.style.display = '';

        const colWidth =
            fields.length > 1
                ? Math.floor(10 / fields.length)
                : 6;

        row.innerHTML =
            fields.map(field => `
                <div class="col-md-${colWidth}">
                    <label class="form-label">
                        ${escapeHtml(field.label)}
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="${fieldInputId(field.key, 'new')}"
                        placeholder="Enter ${escapeHtml(
                            field.label.toLowerCase()
                        )}..."
                    />
                </div>
            `).join('')

            +

            `
                <div class="col-auto">
                    <button
                        type="button"
                        class="btn btn-add-item"
                        onclick="addItem()"
                    >
                        <i class="fa fa-plus me-1"></i>
                        Add Item
                    </button>
                </div>
            `;
    }


    // =========================================================
    // TABLE HEADER
    // =========================================================

    function renderTableHead(fields) {
        const headRow =
            document.getElementById('tableHeadRow');

        const columns = fields
            .map(field =>
                `<th>${escapeHtml(field.label)}</th>`
            )
            .join('');

        /*
         * Only display the Actions column if the user actually
         * has an available action.
         */
        const hasActions =
            currentPermissions.edit ||
            currentPermissions.delete;

        headRow.innerHTML =
            `<th>#</th>` +
            columns +
            (hasActions ? `<th>Actions</th>` : '');
    }


    // =========================================================
    // EDIT FIELDS
    // =========================================================

    function renderEditFields(fields, item = {}) {
        const container =
            document.getElementById('editFieldsContainer');

        container.innerHTML = fields.map(field => `
            <label class="form-label">
                ${escapeHtml(field.label)}
            </label>

            <input
                type="text"
                class="form-control mb-2"
                id="${fieldInputId(field.key, 'edit')}"
                value="${escapeHtml(item[field.key] ?? '')}"
            />
        `).join('');
    }


    // =========================================================
    // RENDER ITEMS (full refresh — new data, fields, permissions)
    // =========================================================

    function renderItems(payload) {
        allItems = payload.data || [];

        const label =
            payload.label || 'Item';

        const fields =
            payload.fields ||
            currentFields ||
            [
                {
                    key: 'name',
                    label: 'Name'
                }
            ];


        /*
         * THIS IS THE IMPORTANT PART.
         *
         * Read permissions returned by MaintenanceController.
         */
        currentPermissions = {
            view:   payload.perms?.view   === true,
            create: payload.perms?.create === true,
            edit:   payload.perms?.edit   === true,
            delete: payload.perms?.delete === true
        };


        currentFields = fields;
        currentLabel  = label;


        document.getElementById(
            'panelTitle'
        ).textContent = `${label} Management`;

        document.getElementById(
            'bulkModuleLabel'
        ).textContent = label;

        document.getElementById(
            'bulkFieldHint'
        ).textContent =
            'Columns: ' + fields.map(f => f.key).join(', ');


        /*
         * These functions now use currentPermissions.
         */
        renderAddForm(fields);

        renderTableHead(fields);


        renderTableBody();
    }


    // =========================================================
    // RENDER TABLE BODY (pagination-aware — reused on page change)
    // =========================================================

    function renderTableBody() {

        const fields = currentFields;
        const label  = currentLabel || 'Item';

        const hasActions =
            currentPermissions.edit ||
            currentPermissions.delete;

        const colspan =
            1 +
            fields.length +
            (hasActions ? 1 : 0);

        const tbody =
            document.getElementById('itemsBody');

        const totalItems = allItems.length;


        // -----------------------------------------------------
        // NO DATA
        // -----------------------------------------------------

        if (!totalItems) {

            let message =
                `No ${label.toLowerCase()} items found.`;

            if (currentPermissions.create) {
                message =
                    `No ${label.toLowerCase()} items yet. Add one above.`;
            }

            tbody.innerHTML = `
                <tr>
                    <td
                        colspan="${colspan}"
                        class="no-items"
                    >
                        ${escapeHtml(message)}
                    </td>
                </tr>
            `;

            document.getElementById(
                'itemCount'
            ).textContent = '';

            updatePaginationControls(0, 0, 0);

            return;
        }


        // -----------------------------------------------------
        // CLAMP CURRENT PAGE
        // -----------------------------------------------------

        const totalPages =
            Math.max(1, Math.ceil(totalItems / pageSize));

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        if (currentPage < 1) {
            currentPage = 1;
        }


        // -----------------------------------------------------
        // SLICE CURRENT PAGE
        // -----------------------------------------------------

        const start = (currentPage - 1) * pageSize;
        const end   = Math.min(start + pageSize, totalItems);

        const pageItems = allItems.slice(start, end);


        // -----------------------------------------------------
        // RENDER ROWS
        // -----------------------------------------------------

        tbody.innerHTML = pageItems.map((item, index) => {

            const cells = fields
                .map(field => `
                    <td>
                        ${escapeHtml(item[field.key])}
                    </td>
                `)
                .join('');


            let actions = '';


            /*
             * EDIT
             */
            if (currentPermissions.edit) {

                const encodedItem =
                    encodeURIComponent(
                        JSON.stringify(item)
                    );

                actions += `
                    <button
                        type="button"
                        class="btn btn-edit me-1"
                        onclick="openEdit(
                            ${Number(item.id)},
                            decodeURIComponent('${encodedItem}')
                        )"
                    >
                        <i class="fa fa-pen"></i>
                        Edit
                    </button>
                `;
            }


            /*
             * DELETE
             */
            if (currentPermissions.delete) {

                actions += `
                    <button
                        type="button"
                        class="btn btn-del"
                        onclick="deleteItem(${Number(item.id)})"
                    >
                        <i class="fa fa-trash"></i>
                        Delete
                    </button>
                `;
            }


            return `
                <tr>
                    <td>${start + index + 1}</td>

                    ${cells}

                    ${
                        hasActions
                            ? `<td>${actions}</td>`
                            : ''
                    }
                </tr>
            `;
        }).join('');


        document.getElementById(
            'itemCount'
        ).textContent =
            `${totalItems} item${totalItems !== 1 ? 's' : ''}`;


        updatePaginationControls(start, end, totalItems);
    }


    // =========================================================
    // PAGINATION CONTROLS
    // =========================================================

    function updatePaginationControls(start, end, total) {

        const pageInfo = document.getElementById('pageInfo');
        const prevBtn  = document.getElementById('prevBtn');
        const nextBtn  = document.getElementById('nextBtn');

        if (!total) {
            pageInfo.textContent = '';
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            return;
        }

        pageInfo.textContent =
            `Showing ${start + 1} to ${end} of ${total} entries`;

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = end >= total;
    }


    function changePage(direction) {

        const totalPages =
            Math.max(1, Math.ceil(allItems.length / pageSize));

        const next = currentPage + direction;

        if (next < 1 || next > totalPages) {
            return;
        }

        currentPage = next;

        renderTableBody();
    }


    // =========================================================
    // LOAD ITEMS
    // =========================================================

    async function loadItems() {

        const tbody =
            document.getElementById('itemsBody');

        tbody.innerHTML = `
            <tr>
                <td
                    colspan="${(currentFields.length || 1) + 2}"
                    class="no-items"
                >
                    Loading...
                </td>
            </tr>
        `;


        try {

            const payload =
                await apiGet(`data/${currentTab}`);


            if (!payload.status) {

                showToast(
                    payload.message ||
                    'Unable to load items.',
                    'danger'
                );

                return;
            }


            renderItems(payload);


        } catch (error) {

            console.error(error);

            showToast(
                'Network error while loading items.',
                'danger'
            );
        }
    }


    // =========================================================
    // ADD ITEM
    // =========================================================

    async function addItem() {

        /*
         * Client-side protection.
         *
         * MaintenanceController must ALSO enforce this.
         */
        if (!currentPermissions.create) {

            showToast(
                'You do not have permission to add items to this module.',
                'danger'
            );

            return;
        }


        const body = new FormData();

        body.append('id', '0');


        for (const field of currentFields) {

            const element =
                document.getElementById(
                    fieldInputId(
                        field.key,
                        'new'
                    )
                );


            const value =
                element
                    ? element.value.trim()
                    : '';


            if (!value) {

                showToast(
                    `Please enter ${field.label.toLowerCase()}.`,
                    'warning'
                );

                return;
            }


            body.append(
                field.key,
                value
            );
        }


        const response =
            await apiPost(
                `save/${currentTab}`,
                body
            );


        if (!response.status) {

            showToast(
                response.message ||
                'Unable to add item.',
                'warning'
            );

            return;
        }


        currentFields.forEach(field => {

            const element =
                document.getElementById(
                    fieldInputId(
                        field.key,
                        'new'
                    )
                );

            if (element) {
                element.value = '';
            }
        });


        showToast(
            response.message ||
            'Item added successfully!'
        );


        /*
         * Jump back to page 1 so the freshly added item is
         * immediately visible without hunting through pages.
         */
        currentPage = 1;

        loadItems();
    }


    // =========================================================
    // OPEN EDIT
    // =========================================================

    function openEdit(id, itemJson) {

        if (!currentPermissions.edit) {

            showToast(
                'You do not have permission to edit this module.',
                'danger'
            );

            return;
        }


        let item;

        try {
            item = typeof itemJson === 'string'
                ? JSON.parse(itemJson)
                : itemJson;
        } catch (error) {

            console.error(error);

            showToast(
                'Unable to open this item.',
                'danger'
            );

            return;
        }


        document.getElementById(
            'editId'
        ).value = id;


        renderEditFields(
            currentFields,
            item
        );


        new bootstrap.Modal(
            document.getElementById('editModal')
        ).show();
    }


    // =========================================================
    // SAVE EDIT
    // =========================================================

    async function saveEdit() {

        if (!currentPermissions.edit) {

            showToast(
                'You do not have permission to edit this module.',
                'danger'
            );

            return;
        }


        const id =
            document.getElementById(
                'editId'
            ).value;


        const body =
            new FormData();


        body.append(
            'id',
            id
        );


        for (const field of currentFields) {

            const element =
                document.getElementById(
                    fieldInputId(
                        field.key,
                        'edit'
                    )
                );


            const value =
                element
                    ? element.value.trim()
                    : '';


            if (!value) {

                showToast(
                    `Please enter ${field.label.toLowerCase()}.`,
                    'warning'
                );

                return;
            }


            body.append(
                field.key,
                value
            );
        }


        const response =
            await apiPost(
                `save/${currentTab}`,
                body
            );


        if (!response.status) {

            showToast(
                response.message ||
                'Unable to update item.',
                'warning'
            );

            return;
        }


        const modalElement =
            document.getElementById(
                'editModal'
            );


        const modal =
            bootstrap.Modal.getInstance(
                modalElement
            );


        if (modal) {
            modal.hide();
        }


        showToast(
            response.message ||
            'Item updated!'
        );

        /*
         * Editing doesn't change list length or order in a way
         * the user needs re-navigating for, so stay on the same
         * page — renderTableBody() will clamp it if needed.
         */
        loadItems();
    }


    // =========================================================
    // DELETE ITEM
    // =========================================================

    async function deleteItem(id) {

        if (!currentPermissions.delete) {

            showToast(
                'You do not have permission to delete items from this module.',
                'danger'
            );

            return;
        }


        if (!confirm('Delete this item?')) {
            return;
        }


        const body =
            new FormData();


        const response =
            await apiPost(
                `delete/${currentTab}/${id}`,
                body
            );


        if (!response.status) {

            showToast(
                response.message ||
                'Unable to delete item.',
                'danger'
            );

            return;
        }


        showToast(
            response.message ||
            'Item deleted.'
        );

        /*
         * If deleting the last item on the current page emptied
         * it, renderTableBody() automatically clamps currentPage
         * back to the new last page.
         */
        loadItems();
    }


    // =========================================================
    // BULK UPLOAD
    // =========================================================

    function ensureBulkModal() {

        if (!bulkUploadModalInstance) {
            bulkUploadModalInstance = new bootstrap.Modal(
                document.getElementById('bulkUploadModal')
            );
        }

        return bulkUploadModalInstance;
    }


    function openBulkUpload() {

        if (!currentPermissions.create) {

            showToast(
                'You do not have permission to bulk upload items for this module.',
                'danger'
            );

            return;
        }

        document.getElementById('bulkFileInput').value = '';

        const resultsBox = document.getElementById('bulkResults');
        resultsBox.style.display = 'none';
        document.getElementById('bulkSummary').textContent = '';
        document.getElementById('bulkErrorList').innerHTML = '';

        ensureBulkModal().show();
    }


    function downloadTemplate() {

        if (!currentPermissions.create) {
            return;
        }

        window.location = `${BASE_URL}/bulk-template/${currentTab}`;
    }


    async function submitBulkUpload() {

        if (!currentPermissions.create) {

            showToast(
                'You do not have permission to bulk upload items for this module.',
                'danger'
            );

            return;
        }

        const fileInput = document.getElementById('bulkFileInput');
        const file = fileInput.files[0];

        if (!file) {
            showToast('Please choose a CSV file first.', 'warning');
            return;
        }

        if (!file.name.toLowerCase().endsWith('.csv')) {
            showToast('Only CSV files are supported.', 'warning');
            return;
        }

        const submitBtn = document.getElementById('bulkSubmitBtn');
        const originalLabel = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML =
            '<i class="fa fa-spinner fa-spin me-1"></i>Uploading...';

        const body = new FormData();
        body.append('csv_file', file);

        try {

            const response =
                await apiPost(`bulk/${currentTab}`, body);

            const resultsBox = document.getElementById('bulkResults');
            const summaryBox = document.getElementById('bulkSummary');
            const errorList  = document.getElementById('bulkErrorList');

            resultsBox.style.display = '';
            errorList.innerHTML = '';

            if (!response.status) {

                summaryBox.className = 'alert alert-danger py-2 px-3';
                summaryBox.textContent =
                    response.message || 'Bulk upload failed.';

                showToast(
                    response.message || 'Bulk upload failed.',
                    'danger'
                );

                return;
            }

            summaryBox.className =
                (response.inserted > 0)
                    ? 'alert alert-success py-2 px-3'
                    : 'alert alert-warning py-2 px-3';

            summaryBox.textContent = response.message;

            (response.errors || []).forEach(err => {
                const li = document.createElement('li');
                li.textContent = err;
                errorList.appendChild(li);
            });

            showToast(response.message);

            if (response.inserted > 0) {
                /*
                 * New rows landed — jump to page 1 so they're
                 * visible right away.
                 */
                currentPage = 1;
                loadItems();
            }

        } catch (error) {

            console.error(error);

            showToast(
                'Network error during bulk upload.',
                'danger'
            );

        } finally {

            submitBtn.disabled = false;
            submitBtn.innerHTML = originalLabel;
        }
    }


    // =========================================================
    // ACTIVE TAB
    // =========================================================

    function setActiveTab(tab) {

        currentTab = tab;


        document
            .querySelectorAll(
                '#sidebarNav .nav-link'
            )
            .forEach(link => {

                link.classList.toggle(
                    'active',
                    link.dataset.tab === tab
                );
            });


        /*
         * Reset permissions and pagination while loading the
         * new module.
         */
        currentPermissions = {
            view: false,
            create: false,
            edit: false,
            delete: false
        };

        currentPage = 1;


        loadItems();
    }


    // =========================================================
    // INITIALIZATION
    // =========================================================

    function init() {

        document
            .querySelectorAll(
                '#sidebarNav .nav-link'
            )
            .forEach(link => {

                link.addEventListener(
                    'click',
                    event => {

                        event.preventDefault();

                        const tab =
                            link.dataset.tab;


                        if (!tab) {
                            return;
                        }


                        history.pushState(
                            null,
                            '',
                            `${BASE_URL}/${tab}`
                        );


                        setActiveTab(tab);
                    }
                );
            });


        loadItems();
    }


    // =========================================================
    // BROWSER BACK/FORWARD
    // =========================================================

    window.addEventListener(
        'popstate',
        () => {

            const parts =
                window.location.pathname.split('/');


            const tab =
                parts[parts.length - 1] ||
                'slants';


            setActiveTab(tab);
        }
    );


    // =========================================================
    // ENTER KEY
    // =========================================================

    document.addEventListener(
        'keydown',
        event => {

            if (
                event.key === 'Enter' &&
                event.target.closest('#addFormRow') &&
                currentPermissions.create
            ) {
                addItem();
            }
        }
    );


    init();
</script>