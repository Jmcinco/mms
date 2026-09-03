<?php

$role = strtoupper(session('role') ?? '');

$dashboardUrl = site_url(
    strtolower($role) . '/dashboard/data'
);

$isEditor = $role === 'EDITOR';

$newsTypeUrl = $isEditor
    ? site_url('editor/dashboard/news-type')
    : null;

$monitorsUrl = $isEditor
    ? site_url('editor/dashboard/monitors')
    : null;
?>

<script>
const USER_DASHBOARD_CONFIG = <?= json_encode([
    'role'         => $role,
    'dashboardUrl' => $dashboardUrl,
    'isEditor'     => $isEditor,
    'newsTypeUrl'  => $newsTypeUrl,
    'monitorsUrl'  => $monitorsUrl,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

class UserDashboard {

    constructor(config = {}) {
        this.config = {
            role: '',
            dashboardUrl: '',
            isEditor: false,
            newsTypeUrl: null,
            monitorsUrl: null,
            ...config
        };

        this.state = {
            activityItems: [],
            activityPage: 1,
            activityPageSize: 4,

            topNewsData: {
                categories: [],
                subcategories: []
            },
            topNewsMode: 'categories',

            newsTypeScope: 'today',
            newsTypeMonitorId: ''
        };

        this.charts = {
            donut: null,
            weekly: null,
            sparkline1: null,
            sparkline2025: null,
            sparkline2026: null,
            topNews: null,
            overallSlant: null,
            newsByStation: null,
            newsType: null
        };

        this.slantColors = {
            positive: '#0f9d8c',
            negative: '#e0483f',
            neutral: '#b9c2d0'
        };

        this.stationColors = {
            website: '#0a1657',
            web: '#0a1657',
            x: '#000000',
            twitter: '#000000',
            facebook: '#1877f2',
            youtube: '#ff0000',
            instagram: '#e1306c',
            tiktok: '#111111',
            tv: '#1c5fc4',
            television: '#1c5fc4',
            radio: '#0f9d8c',
            print: '#7c8aa3',
            newspaper: '#7c8aa3'
        };

        this.newsTypeColors = {
            news: '#1c5fc4',
            commentary: '#0f9d8c',
            interview: '#f5a623',
            'public opinion': '#e0483f'
        };
    }

    init() {
        this.bindEvents();
        this.loadDashboard();

        if (this.config.isEditor) {
            this.loadMonitors();
            this.loadNewsType();
        }
    }

    bindEvents() {
        this.bindActivityEvents();
        this.bindTopNewsEvents();
        this.bindNewsTypeEvents();
    }

    bindActivityEvents() {
        const prevBtn = this.getElement('activityPrevBtn');
        const nextBtn = this.getElement('activityNextBtn');

        prevBtn?.addEventListener('click', () => {
            this.state.activityPage -= 1;
            this.renderActivityPage();
        });

        nextBtn?.addEventListener('click', () => {
            this.state.activityPage += 1;
            this.renderActivityPage();
        });
    }

    bindTopNewsEvents() {
        const toggle = this.getElement('topNewsToggle');

        toggle?.addEventListener('click', event => {
            const button = event.target.closest('[data-mode]');

            if (!button) {
                return;
            }

            this.state.topNewsMode = button.dataset.mode;

            toggle
                .querySelectorAll('.btn-toggle')
                .forEach(item => {
                    item.classList.toggle('active', item === button);
                });

            this.renderTopNewsChart();
        });
    }

    bindNewsTypeEvents() {
        const scopeToggle = this.getElement('newsTypeScopeToggle');
        const monitorSelect = this.getElement('newsTypeMonitorSelect');

        scopeToggle?.addEventListener('click', event => {
            const button = event.target.closest('[data-scope]');

            if (!button) {
                return;
            }

            this.state.newsTypeScope = button.dataset.scope;

            scopeToggle
                .querySelectorAll('.btn-toggle')
                .forEach(item => {
                    item.classList.toggle('active', item === button);
                });

            this.loadNewsType();
        });

        monitorSelect?.addEventListener('change', event => {
            this.state.newsTypeMonitorId = event.target.value;
            this.loadNewsType();
        });
    }

    getElement(id) {
        return document.getElementById(id);
    }

    fmtNum(value) {
        return Number(value ?? 0).toLocaleString();
    }

    escapeText(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    escapeAttribute(value) {
        return this.escapeText(value);
    }

    setGreeting() {
        const greetingText = this.getElement('greetingText');
        const greetingIcon = this.getElement('greetingIcon');

        if (!greetingText || !greetingIcon) {
            return;
        }

        const hour = new Date().getHours();

        let greeting = 'Good evening';
        let icon = 'fa-moon';

        if (hour < 12) {
            greeting = 'Good morning';
            icon = 'fa-sun';
        } else if (hour < 18) {
            greeting = 'Good afternoon';
            icon = 'fa-cloud-sun';
        }

        const dateStr = new Date().toLocaleDateString(undefined, {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        greetingText.textContent =
            `${greeting} — here's what's happening today, ${dateStr}.`;

        greetingIcon.className = `fa ${icon} me-1`;
    }

    renderActivityPage() {
        const listEl = this.getElement('activityList');
        const paginationEl = this.getElement('activityPagination');
        const pageInfoEl = this.getElement('activityPageInfo');
        const prevBtn = this.getElement('activityPrevBtn');
        const nextBtn = this.getElement('activityNextBtn');

        if (!listEl) {
            return;
        }

        if (!this.state.activityItems.length) {
            listEl.innerHTML = `
                <div class="activity-empty">
                    <i class="fa fa-users-slash mb-2 d-block"
                       style="font-size:1.3rem;color:#cfe2fb;"></i>
                    No recent activity yet.
                </div>
            `;

            if (paginationEl) {
                paginationEl.style.display = 'none';
            }

            return;
        }

        const totalPages = Math.max(
            1,
            Math.ceil(
                this.state.activityItems.length /
                this.state.activityPageSize
            )
        );

        this.state.activityPage = Math.min(
            Math.max(1, this.state.activityPage),
            totalPages
        );

        const start =
            (this.state.activityPage - 1) *
            this.state.activityPageSize;

        const pageItems = this.state.activityItems.slice(
            start,
            start + this.state.activityPageSize
        );

        listEl.innerHTML = pageItems.map(item => `
            <div class="activity-item">
                <div class="act-avatar">
                    ${this.escapeText(item.initials)}
                </div>

                <div class="flex-grow-1">
                    <div class="act-name">
                        ${this.escapeText(item.name)}
                    </div>
                </div>

                <span class="act-role-badge">
                    ${this.escapeText(item.role)}
                </span>
            </div>
        `).join('');

        if (!paginationEl) {
            return;
        }

        if (totalPages <= 1) {
            paginationEl.style.display = 'none';
            return;
        }

        paginationEl.style.display = 'flex';

        if (pageInfoEl) {
            pageInfoEl.textContent =
                `Page ${this.state.activityPage} of ${totalPages}`;
        }

        if (prevBtn) {
            prevBtn.disabled = this.state.activityPage <= 1;
        }

        if (nextBtn) {
            nextBtn.disabled = this.state.activityPage >= totalPages;
        }
    }

    renderTopNewsChart() {
        const rows = (
            this.state.topNewsData[this.state.topNewsMode] || []
        ).slice(0, 20);

        const canvasEl = this.getElement('topNewsChart');
        const emptyEl = this.getElement('topNewsEmpty');
        const wrapper = this.getElement('topNewsChartWrapper');

        if (!canvasEl || !emptyEl || !wrapper) {
            return;
        }

        if (!rows.length) {
            this.destroyChart('topNews');

            wrapper.style.display = 'none';
            emptyEl.style.display = '';

            return;
        }

        wrapper.style.display = '';
        emptyEl.style.display = 'none';

        const labels = rows.map(row => row.label);
        const percents = rows.map(row => Number(row.percent ?? 0));

        const chartHeight = Math.max(280, rows.length * 28);
        wrapper.style.height = `${chartHeight}px`;

        this.destroyChart('topNews');

        this.charts.topNews = new Chart(canvasEl, {
            type: 'bar',

            data: {
                labels,
                datasets: [{
                    data: percents,
                    backgroundColor: '#1c5fc4',
                    borderRadius: 4,
                    maxBarThickness: 18
                }]
            },

            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            title: items =>
                                rows[items[0].dataIndex].label,

                            label: item => {
                                const row = rows[item.dataIndex];

                                return `${row.percent}% (${row.count} article${Number(row.count) !== 1 ? 's' : ''})`;
                            }
                        }
                    },

                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#0e2c52',

                        font: {
                            weight: '600',
                            size: 11
                        },

                        formatter: value => `${value}%`
                    }
                },

                scales: {
                    x: {
                        beginAtZero: true,

                        grid: {
                            color: '#e3ebf5'
                        },

                        ticks: {
                            callback: value => `${value}%`
                        }
                    },

                    y: {
                        grid: {
                            display: false
                        },

                        ticks: {
                            font: {
                                size: 11
                            },

                            autoSkip: false,

                            callback: function(value) {
                                const label = this.getLabelForValue(value);

                                return label.length > 42
                                    ? `${label.slice(0, 42)}…`
                                    : label;
                            }
                        }
                    }
                }
            },

            plugins: [ChartDataLabels]
        });
    }

    renderTopSources(rows) {
        const listEl = this.getElement('topSourcesList');
        const emptyEl = this.getElement('topSourcesEmpty');

        if (!listEl || !emptyEl) {
            return;
        }

        if (!rows?.length) {
            listEl.innerHTML = '';
            emptyEl.style.display = '';
            return;
        }

        emptyEl.style.display = 'none';

        const maxCount = Math.max(
            ...rows.map(row => Number(row.count ?? 0))
        );

        listEl.innerHTML = rows.map((row, index) => {
            const count = Number(row.count ?? 0);

            const widthPct = maxCount > 0
                ? Math.max(4, (count / maxCount) * 100)
                : 0;

            const label = this.escapeText(row.label);

            return `
                <div class="source-row${index === 0 ? ' top' : ''}">
                    <div class="source-label" title="${label}">
                        ${label}
                    </div>

                    <div class="source-bar-track">
                        <div
                            class="source-bar-fill"
                            style="width:${widthPct}%;"
                        ></div>
                    </div>

                    <div class="source-count">
                        ${this.fmtNum(count)}
                    </div>
                </div>
            `;
        }).join('');
    }

    slantColor(label) {
        return this.slantColors[
            String(label ?? '').toLowerCase()
        ] || '#7c8aa3';
    }

    renderOverallSlant(rows) {
        const wrapper = this.getElement('overallSlantWrapper');
        const emptyEl = this.getElement('overallSlantEmpty');
        const legendEl = this.getElement('overallSlantLegend');
        const canvasEl = this.getElement('overallSlantChart');

        if (!wrapper || !emptyEl || !legendEl || !canvasEl) {
            return;
        }

        if (!rows?.length) {
            this.destroyChart('overallSlant');

            wrapper.style.display = 'none';
            emptyEl.style.display = '';

            return;
        }

        wrapper.style.display = 'flex';
        emptyEl.style.display = 'none';

        legendEl.innerHTML = rows.map(row => `
            <div class="slant-legend-item">
                <span class="slant-legend-label">
                    <span
                        class="slant-legend-dot"
                        style="background:${this.slantColor(row.label)};"
                    ></span>

                    ${this.escapeText(row.label)}
                </span>

                <span class="slant-legend-value">
                    <b>${this.fmtNum(row.count)}</b>${row.percent}%
                </span>
            </div>
        `).join('');

        this.destroyChart('overallSlant');

        this.charts.overallSlant = new Chart(canvasEl, {
            type: 'doughnut',

            data: {
                labels: rows.map(row => row.label),

                datasets: [{
                    data: rows.map(row => Number(row.count ?? 0)),
                    backgroundColor: rows.map(row =>
                        this.slantColor(row.label)
                    ),
                    borderWidth: 0
                }]
            },

            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },

                cutout: '65%'
            }
        });
    }

    renderTopSourcesBySlant(rows) {
        const listEl = this.getElement('topSourcesSlantList');
        const emptyEl = this.getElement('topSourcesSlantEmpty');

        if (!listEl || !emptyEl) {
            return;
        }

        if (!rows?.length) {
            listEl.innerHTML = '';
            emptyEl.style.display = '';
            return;
        }

        emptyEl.style.display = 'none';

        listEl.innerHTML = rows.map((row, index) => {
            const positive = Number(row.positive ?? 0);
            const neutral = Number(row.neutral ?? 0);
            const negative = Number(row.negative ?? 0);

            const total = Number(
                row.total ?? (positive + neutral + negative)
            );

            const pct = count =>
                total > 0 ? (count / total) * 100 : 0;

            const segment = (count, className) => {
                const width = pct(count);

                if (width <= 0) {
                    return '';
                }

                const showLabel = width > 8;

                return `
                    <div
                        class="slant-seg ${className}"
                        style="width:${width}%"
                    >
                        ${showLabel ? this.fmtNum(count) : ''}
                    </div>
                `;
            };

            const label = this.escapeText(row.label);

            return `
                <div class="slant-bar-row">
                    <div class="slant-bar-rank">${index + 1}</div>

                    <div
                        class="slant-bar-label"
                        title="${label}"
                    >
                        ${label}
                    </div>

                    <div class="slant-bar-track">
                        ${segment(positive, 'positive')}
                        ${segment(neutral, 'neutral')}
                        ${segment(negative, 'negative')}
                    </div>
                </div>
            `;
        }).join('');
    }

    stationColor(label) {
        const key = String(label ?? '').toLowerCase().trim();

        return this.stationColors[key] || '#1c5fc4';
    }

    renderNewsByStation(rows) {
        const canvasEl = this.getElement('newsByStationChart');
        const emptyEl = this.getElement('newsByStationEmpty');
        const wrapper = this.getElement('newsByStationWrapper');

        if (!canvasEl || !emptyEl || !wrapper) {
            return;
        }

        if (!rows?.length) {
            this.destroyChart('newsByStation');

            wrapper.style.display = 'none';
            emptyEl.style.display = '';

            return;
        }

        wrapper.style.display = '';
        emptyEl.style.display = 'none';

        const sorted = [...rows].sort(
            (a, b) => Number(b.count ?? 0) - Number(a.count ?? 0)
        );

        const labels = sorted.map(row => row.label);
        const counts = sorted.map(row => Number(row.count ?? 0));
        const colors = sorted.map(row => this.stationColor(row.label));

        const chartHeight = Math.max(220, sorted.length * 46);
        wrapper.style.height = `${chartHeight}px`;

        this.destroyChart('newsByStation');

        this.charts.newsByStation = new Chart(canvasEl, {
            type: 'bar',

            data: {
                labels,

                datasets: [{
                    data: counts,
                    backgroundColor: colors,
                    borderRadius: 4,
                    maxBarThickness: 28
                }]
            },

            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: item =>
                                `${this.fmtNum(item.raw)} article${Number(item.raw) !== 1 ? 's' : ''}`
                        }
                    },

                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        color: '#0e2c52',

                        font: {
                            weight: '600',
                            size: 11
                        },

                        formatter: value => this.fmtNum(value)
                    }
                },

                scales: {
                    x: {
                        beginAtZero: true,

                        grid: {
                            color: '#e3ebf5'
                        },

                        ticks: {
                            precision: 0
                        }
                    },

                    y: {
                        grid: {
                            display: false
                        },

                        ticks: {
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            },

            plugins: [ChartDataLabels]
        });
    }

    newsTypeColor(label) {
        return this.newsTypeColors[
            String(label ?? '').toLowerCase()
        ] || '#7c8aa3';
    }

    renderNewsType(rows) {
        const wrapper = this.getElement('newsTypeWrapper');
        const emptyEl = this.getElement('newsTypeEmpty');
        const legendEl = this.getElement('newsTypeLegend');
        const canvasEl = this.getElement('newsTypeChart');

        if (!wrapper || !emptyEl || !legendEl || !canvasEl) {
            return;
        }

        if (!rows?.length) {
            this.destroyChart('newsType');

            wrapper.style.display = 'none';
            emptyEl.style.display = '';

            return;
        }

        wrapper.style.display = 'flex';
        emptyEl.style.display = 'none';

        legendEl.innerHTML = rows.map(row => `
            <div class="slant-legend-item">
                <span class="slant-legend-label">
                    <span
                        class="slant-legend-dot"
                        style="background:${this.newsTypeColor(row.label)};"
                    ></span>

                    ${this.escapeText(row.label)}
                </span>

                <span class="slant-legend-value">
                    <b>${this.fmtNum(row.count)}</b>${row.percent}%
                </span>
            </div>
        `).join('');

        this.destroyChart('newsType');

        this.charts.newsType = new Chart(canvasEl, {
            type: 'pie',

            data: {
                labels: rows.map(row => row.label),

                datasets: [{
                    data: rows.map(row => Number(row.count ?? 0)),
                    backgroundColor: rows.map(row =>
                        this.newsTypeColor(row.label)
                    ),
                    borderWidth: 0
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    async loadNewsType() {
        if (!this.config.isEditor || !this.config.newsTypeUrl) {
            return;
        }

        try {
            const url = new URL(
                this.config.newsTypeUrl,
                window.location.origin
            );

            url.searchParams.set(
                'scope',
                this.state.newsTypeScope
            );

            if (this.state.newsTypeMonitorId) {
                url.searchParams.set(
                    'monitor_id',
                    this.state.newsTypeMonitorId
                );
            }

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load news type data.');
            }

            const data = await response.json();

            if (!data.status) {
                console.error(
                    data.message || 'Unable to load news type data.'
                );
                return;
            }

            this.renderNewsType(data.newsType || []);
        }
        catch (error) {
            console.error('News Type:', error);
        }
    }

    async loadMonitors() {
        if (!this.config.isEditor || !this.config.monitorsUrl) {
            return;
        }

        try {
            const response = await fetch(
                this.config.monitorsUrl,
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Unable to load monitors.');
            }

            const data = await response.json();

            if (!data.status) {
                console.error(
                    data.message || 'Unable to load monitors.'
                );
                return;
            }

            const selectEl =
                this.getElement('newsTypeMonitorSelect');

            if (!selectEl) {
                return;
            }

            selectEl.querySelectorAll(
                'option:not(:first-child)'
            ).forEach(option => option.remove());

            (data.monitors || []).forEach(monitor => {
                const option = document.createElement('option');

                option.value = monitor.id;
                option.textContent = monitor.name;

                selectEl.appendChild(option);
            });
        }
        catch (error) {
            console.error('Monitors:', error);
        }
    }

    renderRecentArticles(articles) {
        const tableEl = this.getElement('recentTable');

        if (!tableEl) {
            return;
        }

        if (!articles?.length) {
            tableEl.innerHTML = `
                <tr>
                    <td colspan="5" class="empty-row">
                        <i class="fa fa-inbox"></i>
                        No articles found.
                    </td>
                </tr>
            `;

            return;
        }

        tableEl.innerHTML = articles.map(article => `
            <tr>
                <td class="id-chip">
                    ${this.escapeText(article.id)}
                </td>

                <td>
                    ${this.escapeText(article.news_date)}
                </td>

                <td>
                    ${this.escapeText(article.type)}
                </td>

                <td>
                    ${this.escapeText(article.medium)}
                </td>

                <td>
                    <span class="badge-archived">
                        ${this.escapeText(article.status)}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    renderUser(user) {
        const userName = this.getElement('userName');
        const userInitials = this.getElement('userInitials');

        if (!user) {
            return;
        }

        const firstName = user.first_name ?? '';
        const lastName = user.last_name ?? '';

        if (userName) {
            userName.textContent =
                `${firstName} ${lastName}`.trim().toUpperCase();
        }

        if (userInitials) {
            userInitials.textContent =
                `${firstName.charAt(0)}${lastName.charAt(0)}`
                    .toUpperCase();
        }
    }

    renderStatistics(statistics = {}) {
        const todayEl = this.getElement('totalToday');
        const total2025El = this.getElement('total2025');
        const total2026El = this.getElement('total2026');

        if (todayEl) {
            todayEl.textContent =
                this.fmtNum(statistics.today);
        }

        if (total2025El) {
            total2025El.textContent =
                this.fmtNum(statistics.total2025);
        }

        if (total2026El) {
            total2026El.textContent =
                this.fmtNum(statistics.total2026);
        }
    }

    renderDonutChart(statistics = {}) {
        const canvasEl = this.getElement('donutChart');

        if (!canvasEl) {
            return;
        }

        this.destroyChart('donut');

        this.charts.donut = new Chart(canvasEl, {
            type: 'doughnut',

            data: {
                labels: ['Today', 'Yesterday'],

                datasets: [{
                    data: [
                        Number(statistics.today ?? 0),
                        Number(statistics.yesterday ?? 0)
                    ],

                    backgroundColor: [
                        '#1c5fc4',
                        '#cfe2fb'
                    ],

                    borderWidth: 0
                }]
            },

            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },

                cutout: '70%'
            }
        });
    }

    renderWeeklyChart(week = {}) {
        const canvasEl = this.getElement('barChart');

        if (!canvasEl) {
            return;
        }

        this.destroyChart('weekly');

        this.charts.weekly = new Chart(canvasEl, {
            type: 'bar',

            data: {
                labels: week.labels || [],

                datasets: [{
                    data: week.values || [],
                    backgroundColor: '#2f6fe0',
                    borderRadius: 6,
                    maxBarThickness: 42
                }]
            },

            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        grid: {
                            color: '#e3ebf5'
                        }
                    },

                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const weekLabel = this.getElement('weekLabel');

        if (weekLabel) {
            weekLabel.innerHTML = week.label || '';
        }
    }

    buildSparkline(chartKey, canvasId, values = []) {
        const canvasEl = this.getElement(canvasId);

        if (!canvasEl || !Array.isArray(values)) {
            return;
        }

        this.destroyChart(chartKey);

        this.charts[chartKey] = new Chart(canvasEl, {
            type: 'line',

            data: {
                labels: values.map(() => ''),

                datasets: [{
                    data: values,

                    borderColor: '#2f6fe0',
                    backgroundColor: 'rgba(47, 111, 224, .10)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    x: {
                        display: false
                    },

                    y: {
                        display: false
                    }
                }
            }
        });
    }

    renderSparklines(data) {
        const values = data.sparkline || [];
        const values2025 = data.sparkline2025 || [];
        const values2026 = data.sparkline2026 || [];

        this.buildSparkline(
            'sparkline1',
            'sparkline1',
            values
        );

        this.buildSparkline(
            'sparkline2025',
            'sparkline2025',
            values2025
        );

        this.buildSparkline(
            'sparkline2026',
            'sparkline2026',
            values2026
        );
    }

    renderDashboard(data) {
        this.renderUser(data.user || {});
        this.renderStatistics(data.statistics || {});

        this.state.activityItems = data.activities || [];
        this.state.activityPage = 1;
        this.renderActivityPage();

        this.renderRecentArticles(data.articles || []);

        this.renderDonutChart(data.statistics || {});
        this.renderWeeklyChart(data.week || {});
        this.renderSparklines(data);

        this.state.topNewsData = {
            categories: data.topNews?.categories || [],
            subcategories: data.topNews?.subcategories || []
        };

        this.renderTopNewsChart();

        this.renderTopSources(data.topSources || []);
        this.renderOverallSlant(data.overallSlant || []);
        this.renderTopSourcesBySlant(
            data.topSourcesBySlant || []
        );
        this.renderNewsByStation(
            data.newsByStation || []
        );
    }

    async loadDashboard() {
        this.setGreeting();

        if (!this.config.dashboardUrl) {
            console.error('Dashboard URL is not configured.');
            return;
        }

        try {
            const response = await fetch(
                this.config.dashboardUrl,
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Unable to load dashboard.'
                );
            }

            const data = await response.json();

            if (!data.status) {
                alert(
                    data.message ||
                    'Unable to load dashboard.'
                );

                return;
            }

            this.renderDashboard(data);
        }
        catch (error) {
            console.error('Dashboard:', error);

            alert('Unable to load dashboard.');
        }
    }

    destroyChart(key) {
        if (this.charts[key]) {
            this.charts[key].destroy();
            this.charts[key] = null;
        }
    }

    destroyAllCharts() {
        Object.keys(this.charts).forEach(key => {
            this.destroyChart(key);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new UserDashboard(
        USER_DASHBOARD_CONFIG
    );

    dashboard.init();

    window.userDashboard = dashboard;
});

window.addEventListener('beforeunload', () => {
    window.userDashboard?.destroyAllCharts();
});
</script>