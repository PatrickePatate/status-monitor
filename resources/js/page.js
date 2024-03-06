import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling
import moment from 'moment';

window.hours_or_days_metrics = 'days';
window.hours_or_days = 45;

// Function to throttle the execution of a function
function throttle(func, limit) {
    let inThrottle = false;
    console.log('throttle => ',inThrottle);
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
async function initAvailabilityChart(){
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
    let cachedData = sessionStorage.getItem(window.hours_or_days_metrics+'metric'+metric_id);
    if(cachedData) {
        console.log('serve from cache');
        return JSON.parse(cachedData);
    } else {
        console.log('serve from api');
        var endpoint = '';
        if(window.hours_or_days_metrics === "days"){
            endpoint = 'api/metrics/' + metric_id + '?days='+window.hours_or_days;
        } else if(window.hours_or_days_metrics === "hours"){
            endpoint = 'api/metrics/' + metric_id + '?hours='+window.hours_or_days;
        } else { return []; }
        let res = await fetch(endpoint);
        res = await res.json();
        // caching response
        sessionStorage.setItem(window.hours_or_days_metrics+'metric'+metric_id, JSON.stringify(res.data));
        return res.data;
    }

}

async function renderAvailabilityChart(item, metrics) {
    const width = item.offsetWidth;
    let days = 45;
    let gaps = days - 1;
    let gap_size = ((width / 3) / gaps);
    let part_size = (((width / 3) * 2) / days);

    var actual_width = 0;

    let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
    svg.setAttribute('width', '100%');
    svg.setAttribute('height', '40px');

    if (metrics.length < days) {

        let to_complete = days - metrics.length;
        for (var i = 0; i < to_complete; i++) {

            metrics.unshift({
                average_value: -1,
                created_at: null
            });
        }
    }
    metrics.slice(0, days).forEach((m, index) => {
        var fill = '#00866e';
        if(m.average_value < m.warning_under) { fill = '#FCD581' }
        if(m.average_value < m.danger_under){ fill = '#D52941' }
        if(m.average_value == -1) { fill = "#F5F5F5" }


        var rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        rect.setAttribute('x', actual_width);
        rect.setAttribute('y', 0);
        rect.setAttribute('width', part_size);
        rect.setAttribute('height', 35);
        rect.setAttribute('fill', fill);
        rect.setAttribute('data-tippy-content',
            (m.average_value === -1 ?
                'Aucune donnée' :
                "<div style='text-align: center'>" +
                "Le " + moment(m.date).local().toDate().toLocaleDateString() +
                (window.hours_or_days_metrics === "hours" ? "à " + moment(m.date).local().toDate().toLocaleTimeString() : "") +
                "<br>" + Number(m.average_value).toFixed(2) + " " + (m.suffix || '') + "</div>")
        );

        svg.appendChild(rect);
        actual_width = actual_width + gap_size + part_size;
    })

    item.innerHTML = "";
    item.appendChild(svg);

    tippy('[data-tippy-content]', {allowHTML: true});
}


window.addEventListener('resize', throttle(async function (event) {
    let charts = document.querySelectorAll('.service-availability');
    for (const item of charts) {
        if (!item.hasAttribute('data-metric-id')){
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
}, 150));

initAvailabilityChart();
