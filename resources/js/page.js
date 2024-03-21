import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling
import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc';
dayjs.extend(utc);

window.hours_or_days_metrics = sessionStorage.getItem('hours_or_days_metrics')??'days';
window.hours_or_days = sessionStorage.getItem('hours_or_days')??45;


// Function to throttle the execution of a function
function throttle(func, limit) {
    let inThrottle = false;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function getMetricsSpanLabel(){
    switch (window.hours_or_days_metrics){
        case('days'):
            return "Derniers "+window.hours_or_days+" jours";
        case('hours'):
            return "Dernières "+window.hours_or_days+" heures";
    }
}
async function initAvailabilityChart(){
    document.getElementById('metrics_hours_or_days').value = window.hours_or_days;
    document.getElementById('metrics_hours_or_days_type').value = window.hours_or_days_metrics;

    // clear cache
    let charts = document.querySelectorAll('.service-availability');

    for (const item of charts) {
        // checking if availability area actually have a metric id
        if (!item.hasAttribute('data-metric-id')){
            item.remove();
            continue;
        }
        let metric = item.getAttribute('data-metric-id');
        sessionStorage.removeItem('metric'+metric);
        // fetching data from api and cache them to avoid infinite refetch when window resize
        await getMetric(metric).then((data) => {
            renderAvailabilityChart(item, data).catch((error) => { console.error('Unable to render availability chart.',error); });
        });

    }

}
async function getMetric(metric_id){
    // data are cached ?
    let cachedData = JSON.parse(sessionStorage.getItem(window.hours_or_days_metrics+'metric'+metric_id));
    //force refresh data every five minutes
    let served_at = new Date(cachedData?.served_at);
    let time_diff = Math.floor(((new Date()) - served_at) / 1000 / 60) % 60;
    if(cachedData && time_diff < 3) {
        console.log('serve from cache');
        return cachedData.data;
    } else {
        console.log('serve from api');
        var endpoint = '';
        if(window.hours_or_days_metrics === "days"){
            endpoint = '/api/metrics/' + metric_id + '?days=90';
        } else if(window.hours_or_days_metrics === "hours"){
            endpoint = '/api/metrics/' + metric_id + '?hours=48';
        } else { return []; }
        let res = await fetch(endpoint);
        res = await res.json();
        // caching response
        sessionStorage.setItem(window.hours_or_days_metrics+'metric'+metric_id, JSON.stringify({data: res.data, served_at: new Date()}));
        return res.data;
    }

}

async function renderAvailabilityChart(item, metrics) {
    const width = item.offsetWidth;
    let days = window.hours_or_days;
    let gaps = Math.max(days,20) - 1;
    let gap_size = ((width / 3) / gaps);
    let part_size = (((width / 3) * 2) / Math.max(days,20));

    var actual_width = 0;

    let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute('width', '100%');
    svg.setAttribute('height', '40px');

    let data = [];

    let now = dayjs(new Date()).utc();
    let format = window.hours_or_days_metrics === "days" ? 'YYYY-MM-DD' : 'YYYY-MM-DD HH:00:00';
    for (var y = 0; y < (Math.max(days,20)); y++) {
        let metric = metrics.find(o => o.date === now.format(format));

        if(metric !== undefined){
            data.unshift(metric);
        }else{
            data.unshift({
                average_value: -1,
                date: now.format(format)
            })
        }
        now = now.subtract(1,window.hours_or_days_metrics);
    }

    data.forEach((m, index) => {
        var fill = '#00866e';
        if(m.warning_under !== null && m.average_value < m.warning_under) { fill = '#FCD581' }
        if(m.danger_under !== null && m.average_value < m.danger_under){ fill = '#D52941' }
        if(m.warning_upper !== null && m.average_value > m.warning_upper) { fill = '#FCD581' }
        if(m.danger_upper !== null && m.average_value > m.danger_upper){ fill = '#D52941' }
        if(m.average_value === -1) { fill = "#F5F5F5" }


        var rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        rect.setAttribute('x', actual_width);
        rect.setAttribute('y', 0);
        rect.setAttribute('width', part_size);
        rect.setAttribute('height', 35);
        rect.setAttribute('fill', fill);
        rect.setAttribute('data-tippy-content',
            (m.average_value === -1 ?
                "<div style='text-align: center'>" +
                "Le " + (dayjs(m.date).utc(true)).local().format('DD/MM/YY') +
                (window.hours_or_days_metrics === "hours" ? " à " + (dayjs(m.date).utc(true)).local().format('HH:mm:ss') : "") + "<br>" +
                '<em>Aucune donnée</em>' +
                '</div>' :
                "<div style='text-align: center'>" +
                "Le " + (dayjs(m.date).utc(true)).local().format('DD/MM/YY') +
                (window.hours_or_days_metrics === "hours" ? " à " + (dayjs(m.date).utc(true)).local().format('HH:mm:ss') : "") +
                "<br>" + Math.round(Number(m.average_value)*100)/100 + " " + (m.suffix || '') + "</div>")
        );

        svg.appendChild(rect);
        actual_width = actual_width + gap_size + part_size;
    })

    item.querySelector('.service-availability-chart').innerHTML = "";
    item.querySelector('.service-availability-chart').appendChild(svg);

    tippy('[data-tippy-content]', {allowHTML: true});
}

async function rerender_charts() {
    let charts = document.querySelectorAll('.service-availability');
    for (const item of charts) {
        if (!item.hasAttribute('data-metric-id')) {
            item.remove();
            continue;
        }
        let metric = item.getAttribute('data-metric-id');
        await getMetric(metric).then((data) => {
            renderAvailabilityChart(item, data).catch((error) => {
                console.error('Unable to render availability chart.', error);
            });
        });
    }
    tippy('[data-tippy-content]', {allowHTML: true});
    document.querySelectorAll('.metrics_span_label').forEach((label)=>{
        label.innerHTML = getMetricsSpanLabel();
    });
}
// OBSERVER
window.addEventListener('resize', throttle(async function (event) { rerender_charts(); }, 150));
document.getElementById('metrics_hours_or_days').addEventListener('change', (e)=>{
    switch(document.getElementById('metrics_hours_or_days_type').value){
        case('days'):
            if(e.target.value > 90){ e.target.value = 90; }
            if(e.target.value < 0){ e.target.value = 0; }
            break;
        case('hours'):
            if(e.target.value > 48){ e.target.value = 48; }
            if(e.target.value < 0){ e.target.value = 0; }
            break;

    }
    window.hours_or_days = e.target.value;
    sessionStorage.setItem('hours_or_days', e.target.value);
    window.hours_or_days_metrics = document.getElementById('metrics_hours_or_days_type').value;
    sessionStorage.setItem('hours_or_days_metrics', document.getElementById('metrics_hours_or_days_type').value);
    rerender_charts();
});
document.getElementById('metrics_hours_or_days_type').addEventListener('change', (e)=>{
    switch(e.target.value){
        case('days'):
            document.getElementById('metrics_hours_or_days').value = 45;
            break;
        case('hours'):
            document.getElementById('metrics_hours_or_days').value = 12;
            break;
    }

    window.hours_or_days = document.getElementById('metrics_hours_or_days').value;
    sessionStorage.setItem('hours_or_days', document.getElementById('metrics_hours_or_days').value);
    window.hours_or_days_metrics = e.target.value;
    sessionStorage.setItem('hours_or_days_metrics', e.target.value);
    rerender_charts();
});

// INIT
document.querySelectorAll('.metrics_span_label').forEach((label)=>{
    label.innerHTML = getMetricsSpanLabel();
});
document.querySelectorAll('.badge-icon').forEach((badge_icon)=>{
    badge_icon.addEventListener('click', (e) => {
        let url = e.target.getAttribute('data-badge-url');
        navigator.clipboard.writeText(url);
        e.target._tippy.setContent('Copié !');
        e.target._tippy.show();
        setTimeout(()=>{
            e.target._tippy.setContent('Copier l\'URL vers le badge de statut du service');
        },3000)
    })
});
initAvailabilityChart();
