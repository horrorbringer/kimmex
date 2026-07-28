import { OrgChart } from 'd3-org-chart';

const escapeHtml = (value = '') => String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const initials = (value = '') => [...String(value).trim()].slice(0, 2).join('').toUpperCase() || 'KM';

const flattenTree = (nodes, parentId = null) => nodes.flatMap((node) => {
    const currentNode = {
        id: node.id,
        parentId,
        title: node.title,
        type: node.type,
        name: node.name,
        role: node.role,
        image: node.image,
    };

    return [currentNode, ...flattenTree(node.children || [], node.id)];
});

const chartData = (nodes) => {
    const nodesList = flattenTree(nodes || []);
    const roots = nodesList.filter((node) => !node.parentId);

    if (roots.length <= 1) {
        return nodesList;
    }

    const virtualRootId = '__kimmex_org_root__';

    return [
        {
            id: virtualRootId,
            parentId: null,
            isVirtual: true,
            name: 'Kimmex Organization',
        },
        ...nodesList.map((node) => ({
            ...node,
            parentId: node.parentId || virtualRootId,
        })),
    ];
};

const nodeMarkup = (node) => {
    if (node.isVirtual) {
        return `<div class="kimmex-org-chart__virtual-root">${escapeHtml(node.name)}</div>`;
    }

    const avatar = node.image
        ? `<img src="${escapeHtml(node.image)}" alt="" loading="lazy" />`
        : `<span>${escapeHtml(initials(node.name))}</span>`;

    return `
        <article class="kimmex-org-chart__node">
            <div class="kimmex-org-chart__avatar">${avatar}</div>
            <div class="kimmex-org-chart__content">
                <p class="kimmex-org-chart__name">${escapeHtml(node.name)}</p>
                <p class="kimmex-org-chart__title">${escapeHtml(node.title || node.role || node.type)}</p>
                <span class="kimmex-org-chart__type">${escapeHtml(node.type || '')}</span>
            </div>
        </article>
    `;
};

const initialiseOrgChart = (root) => {
    if (root.dataset.orgChartReady === 'true') {
        return;
    }

    const canvas = root.querySelector('[data-org-chart-canvas]');
    const searchInput = root.querySelector('[data-org-chart-search]');

    if (!canvas) {
        return;
    }

    let data = chartData(JSON.parse(root.dataset.chartData || '[]'));
    let chart = null;

    const render = (nextData = data) => {
        data = chartData(nextData);
        canvas.replaceChildren();

        chart = new OrgChart()
            .container(canvas)
            .data(data)
            .svgWidth(canvas.clientWidth || 960)
            .svgHeight(720)
            .nodeWidth((d) => d.data.isVirtual ? 230 : 300)
            .nodeHeight((d) => d.data.isVirtual ? 58 : 130)
            .childrenMargin(() => 70)
            .compact(false)
            .initialZoom(0.85)
            .nodeContent((d) => nodeMarkup(d.data))
            .buttonContent(({ node }) => `<div class="kimmex-org-chart__toggle">${node.children ? '−' : '+'}</div>`)
            .onNodeClick((d) => {
                if (!d.data.isVirtual) {
                    window.dispatchEvent(new CustomEvent('org-chart:edit', { detail: { id: d.data.id } }));
                }
            })
            .render()
            .fit();
    };

    root.addEventListener('click', (event) => {
        const action = event.target.closest('[data-org-chart-action]')?.dataset.orgChartAction;

        if (!action || !chart) {
            return;
        }

        if (action === 'fit') {
            chart.fit();
        } else if (action === 'expand') {
            chart.expandAll();
        } else if (action === 'collapse') {
            chart.collapseAll();
        } else if (action === 'fullscreen') {
            chart.fullscreen(root);
        } else if (action === 'download') {
            chart.exportImg({ full: true, imageName: 'kimmex-organization-chart' });
        }
    });

    searchInput?.addEventListener('input', (event) => {
        const query = event.target.value.trim().toLowerCase();

        chart.clearHighlighting();

        if (query) {
            const match = data.find((node) => `${node.name} ${node.title} ${node.role}`.toLowerCase().includes(query));

            if (match) {
                chart.setHighlighted(match.id).render();
            }
        } else {
            chart.render();
        }
    });

    const resizeObserver = new ResizeObserver(() => {
        if (chart && canvas.clientWidth) {
            chart.svgWidth(canvas.clientWidth).render();
        }
    });

    resizeObserver.observe(canvas);
    root.orgChartUpdate = render;
    root.dataset.orgChartReady = 'true';
    render(JSON.parse(root.dataset.chartData || '[]'));
};

const initialiseAll = () => document.querySelectorAll('[data-org-chart]').forEach(initialiseOrgChart);

document.addEventListener('livewire:init', () => {
    initialiseAll();

    window.Livewire.on('chartUpdated', ({ chartData: nextChartData }) => {
        document.querySelectorAll('[data-org-chart]').forEach((root) => root.orgChartUpdate?.(nextChartData));
    });
});

document.addEventListener('livewire:navigated', initialiseAll);
