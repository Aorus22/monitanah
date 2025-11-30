<script>
let selectedSensorNo = 1;
let selectedParameter = 'ph';
let summaryLoaded = false;
let summaryCharts = [];
let sse = null;
const sseUrl = document.querySelector('meta[name="sse-url"]')?.getAttribute('content') || 'http://localhost:8081';

function updateCardVisibility() {
    const cards = {
        ph: document.getElementById('card-ph'),
        suhu: document.getElementById('card-suhu'),
        tds: document.getElementById('card-tds')
    };
    Object.entries(cards).forEach(([key, el]) => {
        if (!el) return;
        el.style.display = key === selectedParameter ? 'flex' : 'none';
    });
}

function toggleChartVisibility() {
    const wraps = {
        ph: document.getElementById('phChartWrap'),
        suhu: document.getElementById('suhuChartWrap'),
        tds: document.getElementById('tdsChartWrap'),
    };
    Object.entries(wraps).forEach(([key, el]) => {
        if (!el) return;
        el.style.display = key === selectedParameter ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const sensorDropdown = document.getElementById('sensorDropdown');
    const parameterDropdown = document.getElementById('parameterDropdown');
    const tabBtnRealtime = document.getElementById('tab-btn-realtime');
    const tabBtnSummary = document.getElementById('tab-btn-summary');
    const tabRealtime = document.getElementById('tab-realtime-section');
    const tabSummary = document.getElementById('tab-summary-section');
    const historyCard = document.getElementById('history-card-container');

    sensorDropdown.addEventListener('change', function() {
        selectedSensorNo = parseInt(this.value, 10);
        renderFromState();
    });

    parameterDropdown.addEventListener('change', function() {
        selectedParameter = this.value;
        updateCardVisibility();
        toggleChartVisibility();
        renderFromState();
    });

    const activateTab = (tab) => {
        if (tab === 'realtime') {
            tabBtnRealtime.classList.replace('tab-inactive', 'tab-active');
            tabBtnSummary.classList.replace('tab-active', 'tab-inactive');
            tabRealtime.style.display = 'block';
            tabSummary.style.display = 'none';
            if (historyCard) historyCard.style.display = 'block';
            renderFromState();
        } else {
            tabBtnSummary.classList.replace('tab-inactive', 'tab-active');
            tabBtnRealtime.classList.replace('tab-active', 'tab-inactive');
            tabRealtime.style.display = 'none';
            tabSummary.style.display = 'block';
            if (historyCard) historyCard.style.display = 'none';
            if (!summaryLoaded) {
                summaryLoaded = true;
                buildSummaryTab();
            } else {
                buildSummaryTab();
            }
        }
    };
    tabBtnRealtime.addEventListener('click', () => activateTab('realtime'));
    tabBtnSummary.addEventListener('click', () => activateTab('summary'));

    updateCardVisibility();
    toggleChartVisibility();
    initState().then(() => {
        startSSE();
        activateTab('realtime');
    });
});

function formatTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
}
function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', { day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' });
}

async function fetchRealtimeData() {
    const loader = document.getElementById('realtime-loader');
    const errorElement = document.getElementById('realtime-error');
    if (loader) loader.style.display = 'block';
    if (errorElement) errorElement.classList.add('hidden');
    try {
        const res = await fetch(`/api/sensor/realtime?parameter=${selectedParameter}&sensor_no=${selectedSensorNo}`);
        if (!res.ok) throw new Error('Response not OK');
        return await res.json();
    } catch (error) {
        console.error('Error fetching realtime data:', error);
        if (errorElement) errorElement.classList.remove('hidden');
        return null;
    } finally {
        if (loader) loader.style.display = 'none';
    }
}
async function fetchRealtimeAll() {
    try {
        const res = await fetch('/api/sensor/realtime/all');
        if (!res.ok) throw new Error('Response not OK');
        return await res.json();
    } catch (error) {
        console.error('Error fetching realtime all:', error);
        return [];
    }
}

async function fetchHistory() {
    const loader = document.getElementById('chart-loader');
    if (loader) loader.style.display = 'block';
    try {
        const res = await fetch(`/api/sensor/history?parameter=${selectedParameter}&sensor_no=${selectedSensorNo}`);
        if (!res.ok) throw new Error('Response not OK');
        return await res.json();
    } catch (error) {
        console.error('Error fetching history data:', error);
        return null;
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

function prettyValue(parameter, value) {
    if (value == null || isNaN(parseFloat(value))) return '-';
    const v = parseFloat(value);
    if (parameter === 'ph') return v.toFixed(2);
    if (parameter === 'suhu') return `${v.toFixed(1)}°C`;
    if (parameter === 'tds') return `${v.toFixed(0)}%`;
    return v;
}

const state = {
    realtime: {}, // key: param-sensor => {parameter,sensor_no,value,updated_at}
    histories: {} // key: param-sensor => array
};

async function initState() {
    const rt = await fetchRealtimeAll();
    rt.forEach(item => {
        state.realtime[`${item.parameter}-${item.sensor_no}`] = item;
    });
    const hist = await fetchAllHistory();
    Object.entries(hist).forEach(([k, arr]) => {
        state.histories[k] = arr;
    });
    renderFromState();
}

function renderFromState() {
    const sensorKey = `${selectedParameter}-${selectedSensorNo}`;
    const data = state.realtime[sensorKey] || null;

    const sensorIdElement = document.getElementById('sensor_id');
    if (sensorIdElement) sensorIdElement.textContent = `${selectedParameter.toUpperCase()}-${selectedSensorNo}`;

    const phDisplay = document.getElementById('ph');
    const phBar = document.getElementById('ph-bar');
    const phStatus = document.getElementById('ph_status');
    const suhuDisplay = document.getElementById('suhu');
    const suhuBar = document.getElementById('suhu-bar');
    const suhuStatus = document.getElementById('suhu-status');
    const tdsDisplay = document.getElementById('tds');
    const tdsBar = document.getElementById('tds-bar');
    const tdsStatus = document.getElementById('tds-status');

    // reset
    if (phDisplay) { phDisplay.textContent = '-'; if (phBar) phBar.style.width='0%'; if (phStatus) phStatus.textContent='-'; }
    if (suhuDisplay) { suhuDisplay.textContent='-'; if (suhuBar) suhuBar.style.width='0%'; if (suhuStatus) suhuStatus.textContent='-'; }
    if (tdsDisplay) { tdsDisplay.textContent='-'; if (tdsBar) tdsBar.style.width='0%'; if (tdsStatus) tdsStatus.textContent='-'; }

    if (data) {
        if (data.parameter === 'ph' && phDisplay) {
            const val = parseFloat(data.value);
            if (!isNaN(val)) {
                phDisplay.textContent = val.toFixed(2);
                const pct = Math.min(Math.max(((val - 4) / 4) * 100, 0), 100);
                if (phBar) { phBar.style.width = `${pct}%`; phBar.style.backgroundColor = val >=6 && val<=7.5 ? '#34d399' : '#f87171'; }
                if (phStatus) phStatus.textContent = val < 6 ? 'Terlalu rendah' : (val > 7.5 ? 'Terlalu tinggi' : 'Optimal');
            }
        }
        if (data.parameter === 'suhu' && suhuDisplay) {
            const val = parseFloat(data.value);
            if (!isNaN(val)) {
                suhuDisplay.textContent = `${val.toFixed(1)}°C`;
                let width='0%', color='#facc15', status='Terlalu dingin';
                if (val < 22) { width='33%'; color='#facc15'; status='Terlalu dingin'; }
                else if (val <= 30) { width='66%'; color='#34d399'; status='Optimal'; }
                else { width='100%'; color='#de2121'; status='Terlalu panas'; }
                if (suhuBar) { suhuBar.style.width = width; suhuBar.style.backgroundColor = color; }
                if (suhuStatus) suhuStatus.textContent = status;
            }
        }
        if (data.parameter === 'tds' && tdsDisplay) {
            const val = parseFloat(data.value);
            if (!isNaN(val)) {
                tdsDisplay.textContent = `${val.toFixed(0)}%`;
                let width='0%', color='#facc15', status='Terlalu rendah';
                if (val < 20) { width='33%'; color='#facc15'; status='Terlalu rendah'; }
                else if (val <= 80) { width='66%'; color='#34d399'; status='Optimal'; }
                else { width='100%'; color='#de2121'; status='Berlebihan'; }
                if (tdsBar) { tdsBar.style.width = width; tdsBar.style.backgroundColor = color; }
                if (tdsStatus) tdsStatus.textContent = status;
            }
        }
    }

    // charts
    const hist = state.histories[sensorKey] || [];
    const chartError = document.getElementById('chart-error');
    if (hist.length === 0) {
        if (chartError) {
            chartError.classList.remove('hidden');
            chartError.innerHTML = `<i class="fas fa-info-circle mr-2"></i><span>Tidak ada data history untuk sensor ${selectedSensorNo} (${selectedParameter}).</span>`;
        }
        [phChart, suhuChart, tdsChart].forEach(ch => { if (ch) { ch.data.labels=[]; ch.data.datasets[0].data=[]; ch.update(); } });
    } else {
        if (chartError) chartError.classList.add('hidden');
        const labels = hist.map(d => formatTime(d.created_at));
        const values = hist.map(d => d.value);
        const chartMap = { ph: phChart, suhu: suhuChart, tds: tdsChart };
        const target = chartMap[selectedParameter];
        Object.entries(chartMap).forEach(([key, chart]) => {
            if (!chart) return;
            if (chart === target) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = values;
            } else {
                chart.data.labels = [];
                chart.data.datasets[0].data = [];
            }
            chart.update();
        });
    }
    toggleChartVisibility();
}

async function saveSnapshot() {
    const sensorKey = `${selectedParameter}-${selectedSensorNo}`;
    const data = state.realtime[sensorKey];
    if (!data) return;
    const payload = {
        parameter: selectedParameter,
        sensor_no: selectedSensorNo,
        value: data.value != null ? parseFloat(data.value) : null,
        status_pump_ph: data.status_pump_ph != null ? Boolean(data.status_pump_ph) : false,
        status_pump_ppm: data.status_pump_ppm != null ? Boolean(data.status_pump_ppm) : false
    };
    await fetch('/api/sensor/history', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    });
    // local append
    if (!state.histories[sensorKey]) state.histories[sensorKey] = [];
    state.histories[sensorKey].push({ parameter:selectedParameter, sensor_no:selectedSensorNo, value: payload.value, created_at: new Date().toISOString() });
    renderFromState();
}

async function fetchAvailableSensors(parameter) {
    try {
        const res = await fetch(`/api/sensor/available?parameter=${parameter}`);
        if (!res.ok) throw new Error('fail fetch sensors');
        const data = await res.json();
        return (data && data.length) ? data : [1];
    } catch (err) {
        return [1];
    }
}
async function fetchRealtimeBy(parameter, sensorNo) {
    try {
        const res = await fetch(`/api/sensor/realtime?parameter=${parameter}&sensor_no=${sensorNo}`);
        if (!res.ok) throw new Error('res not ok');
        return await res.json();
    } catch { return null; }
}
async function fetchHistoryBy(parameter, sensorNo, limit = 20) {
    try {
        const res = await fetch(`/api/sensor/history?parameter=${parameter}&sensor_no=${sensorNo}`);
        if (!res.ok) throw new Error('res not ok');
        const data = await res.json();
        return Array.isArray(data) ? data.slice(-limit) : [];
    } catch { return []; }
}
async function fetchAllHistory() {
    try {
        const res = await fetch('/api/sensor/history/all');
        if (!res.ok) throw new Error('res not ok');
        const data = await res.json();
        return data || {};
    } catch {
        return {};
    }
}

async function buildSummaryTab() {
    summaryCharts.forEach(c => c.destroy());
    summaryCharts = [];
    const loader = document.getElementById('summary-loader');
    const container = document.getElementById('summary-content');
    if (!loader || !container) return;
    loader.style.display = 'block';
    container.innerHTML = '';
    const params = ['ph','suhu','tds'];
    try {
        // Use in-memory state to avoid extra fetches
        const realtimeMap = state.realtime;
        const historiesAll = state.histories;
        const availableByParam = {};
        Object.keys(historiesAll).forEach(key => {
            const [p, s] = key.split('-');
            if (!availableByParam[p]) availableByParam[p] = [];
            availableByParam[p].push(parseInt(s, 10));
        });
        Object.keys(realtimeMap).forEach(key => {
            const [p, s] = key.split('-');
            if (!availableByParam[p]) availableByParam[p] = [];
            if (!availableByParam[p].includes(parseInt(s, 10))) {
                availableByParam[p].push(parseInt(s, 10));
            }
        });

        for (const param of params) {
            const sensors = availableByParam[param] && availableByParam[param].length ? availableByParam[param] : [1];
            const section = document.createElement('div');
            section.className = 'space-y-3';
            section.innerHTML = `
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                    <h3 class="text-lg font-semibold text-emerald-800 uppercase">${param}</h3>
                </div>
            `;
            const grid = document.createElement('div');
            grid.className = 'flex flex-wrap justify-center gap-4';

            for (const sensorNo of sensors) {
                const realtime = realtimeMap[`${param}-${sensorNo}`] || null;
                const history = historiesAll[`${param}-${sensorNo}`] || [];
                const latestTime = realtime && realtime.updated_at ? formatDateTime(realtime.updated_at) : '-';
                const cardColor = param === 'ph' ? 'bg-emerald-50' : (param === 'suhu' ? 'bg-orange-50' : 'bg-amber-50');
                const barColor = param === 'ph' ? '#34d399' : (param === 'suhu' ? '#f97316' : '#f59e0b');
                const optimalText = param === 'ph' ? 'Optimal: 6.0 - 7.5' : (param === 'suhu' ? 'Optimal: 22°C - 30°C' : 'Optimal: 20% - 80%');

                const wrap = document.createElement('div');
                wrap.className = 'flex flex-col items-center gap-3 w-full sm:w-2/3 md:w-1/2 lg:w-2/5';

                const card = document.createElement('div');
                card.className = `p-6 sm:p-5 rounded-xl flex flex-col items-center shadow ${cardColor} w-full`;
                card.setAttribute('data-sum-card', `${param}-${sensorNo}`);
                card.innerHTML = `
                    <div class="flex items-center justify-between w-full mb-2">
                        <div class="text-sm font-medium text-slate-600 uppercase">${param} - Sensor ${sensorNo}</div>
                        <div class="text-xs text-slate-500 summary-time">${latestTime}</div>
                    </div>
                    <div class="text-3xl font-bold text-emerald-700 text-center mb-1 summary-value">${prettyValue(param, realtime?.value)}</div>
                    <div class="text-xs text-gray-500 mb-2">${optimalText}</div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-1">
                        <div style="width:0%;background:${barColor};height:100%;transition:width 0.5s" class="summary-bar" data-param="${param}" data-value="${realtime?.value ?? ''}"></div>
                    </div>
                `;

                const chartBox = document.createElement('div');
                chartBox.className = 'w-full bg-white rounded-xl shadow-lg border border-slate-200 p-4 chart-box summary';
                chartBox.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-semibold text-emerald-800">Grafik History</div>
                        <div class="text-xs text-slate-500">Sensor ${sensorNo}</div>
                    </div>
                    <canvas id="summary-chart-${param}-${sensorNo}" height="220"></canvas>
                `;

                wrap.appendChild(card);
                wrap.appendChild(chartBox);
                grid.appendChild(wrap);

                const ctx = chartBox.querySelector(`#summary-chart-${param}-${sensorNo}`).getContext('2d');
                const labels = history.map(h => formatTime(h.created_at));
                const data = history.map(h => h.value);
                const colorMap = { ph:'rgba(14,165,233,1)', suhu:'rgba(249,115,22,1)', tds:'rgba(139,92,246,1)' };
                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: param.toUpperCase(),
                            data,
                            borderColor: colorMap[param] || 'rgba(14,165,233,1)',
                            backgroundColor: (colorMap[param] || 'rgba(14,165,233,1)').replace('1)', '0.12)'),
                            borderWidth: 3,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { display: false }, y: { display: false } }
                    }
                });
                summaryCharts.push(chart);
            }
            section.appendChild(grid);
            container.appendChild(section);
        }

        document.querySelectorAll('.summary-bar').forEach(bar => {
            const param = bar.getAttribute('data-param');
            const rawVal = parseFloat(bar.getAttribute('data-value'));
            if (isNaN(rawVal)) { bar.style.width = '0%'; return; }
            if (param === 'ph') {
                const pct = Math.min(Math.max(((rawVal - 4) / 4) * 100, 0), 100);
                bar.style.width = `${pct}%`;
            } else if (param === 'suhu') {
                let width = rawVal < 22 ? 33 : rawVal <=30 ? 66 : 100;
                bar.style.width = `${width}%`;
            } else if (param === 'tds') {
                let width = rawVal < 20 ? 33 : rawVal <=80 ? 66 : 100;
                bar.style.width = `${width}%`;
            }
        });
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

// Chart initialization
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    tension: 0.3,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#0f766e',
            bodyColor: '#334155',
            borderColor: '#e2e8f0',
            borderWidth: 1,
            padding: 12,
            displayColors: false
        }
    },
    interaction: { intersect: false, mode: 'index' },
    elements: { point: { radius: 0, hoverRadius: 6 } },
    scales: { x: { grid: { display:false }}, y: { beginAtZero:false, grid:{ color:'rgba(0,0,0,0.05)' } } }
};

const phCtx = document.getElementById('phChart').getContext('2d');
const phChart = new Chart(phCtx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'pH', data: [], backgroundColor: 'rgba(14,165,233,0.1)', borderColor: 'rgba(14,165,233,1)', borderWidth: 3, fill: true }] },
    options: { ...chartOptions, plugins: { ...chartOptions.plugins, title: { display: true, text: 'Tingkat pH Tanah', font: { size:16, weight:'bold' }, padding: { bottom:16 } } } }
});
const suhuCtx = document.getElementById('suhuChart').getContext('2d');
const suhuChart = new Chart(suhuCtx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Suhu (°C)', data: [], backgroundColor: 'rgba(249,115,22,0.1)', borderColor: 'rgba(249,115,22,1)', borderWidth: 3, fill: true }] },
    options: { ...chartOptions, plugins: { ...chartOptions.plugins, title: { display: true, text: 'Suhu Tanah (°C)', font: { size:16, weight:'bold' }, padding: { bottom:16 } } } }
});
const tdsCtx = document.getElementById('tdsChart').getContext('2d');
const tdsChart = new Chart(tdsCtx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'TDS (%)', data: [], backgroundColor: 'rgba(139,92,246,0.1)', borderColor: 'rgba(139,92,246,1)', borderWidth: 3, fill: true }] },
    options: { ...chartOptions, plugins: { ...chartOptions.plugins, title: { display: true, text: 'Tingkat Kelembapan (%)', font: { size:16, weight:'bold' }, padding: { bottom:16 } } } }
});

// SSE handling
async function initState() {
    // initial fetch
    const rt = await fetchRealtimeAll();
    rt.forEach(item => {
        state.realtime[`${item.parameter}-${item.sensor_no}`] = item;
    });
    const histAll = await fetchAllHistory();
    Object.entries(histAll).forEach(([k, arr]) => {
        state.histories[k] = arr;
    });
    renderFromState();
}

function startSSE() {
    if (sse) return;
    sse = new EventSource(`${sseUrl}/events`);
        sse.onmessage = (event) => {
        try {
            const parsed = JSON.parse(event.data);
            if (parsed.type === 'realtime') {
                const p = parsed.payload;
                state.realtime[`${p.parameter}-${p.sensor_no}`] = p;
            } else if (parsed.type === 'history') {
                const h = parsed.payload;
                const key = `${h.parameter}-${h.sensor_no}`;
                if (!state.histories[key]) state.histories[key] = [];
                state.histories[key].push(h);
                if (state.histories[key].length > 100) {
                    state.histories[key] = state.histories[key].slice(-100);
                }
            }
            renderFromState();
            const summarySection = document.getElementById('tab-summary-section');
            if (summarySection && summarySection.style.display !== 'none') {
                buildSummaryTab();
            }
        } catch (err) {
            console.warn('SSE parse error', err);
        }
    };
    sse.onerror = () => {
        // try reconnect
        if (sse) sse.close();
        sse = null;
        setTimeout(startSSE, 2000);
    };
}

function stopSSE() {
    if (sse) { sse.close(); sse = null; }
}
</script>
