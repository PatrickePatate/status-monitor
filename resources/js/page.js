import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling
import moment from 'moment';

function renderAvailabilityChart(item){
    if(!item.hasAttribute('data-metric-id')) return;
    let metric = item.getAttribute('data-metric-id');
    fetch('api/metrics/'+metric)
        .then((res) => (res.json()))
        .then((res) => {
            let metrics = res.data;
            const width = item.offsetWidth;
            let days = 45;
            let gaps = days-1;
            let gap_size = ((width/3)/gaps);
            let part_size = (((width/3)*2)/days);

            var actual_width = 0;

            let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.setAttribute('width', '100%');
            svg.setAttribute('height', '40px');

            if(metrics.length < days) {

                let to_complete = days - metrics.length;
                for(var i=0;i<to_complete;i++){

                    metrics.unshift({
                        value: -1,
                        created_at: null
                    });
                }
            }
            metrics.slice(0,days).forEach((m, index) => {

                var rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
                rect.setAttribute('x', actual_width);
                rect.setAttribute('y', 0);
                rect.setAttribute('width', part_size);
                rect.setAttribute('height', 35);
                rect.setAttribute('fill', (m.value === -1 ? '#F5F5F5' : (m.value === 0 ? '#D52941': '#00866e')));
                rect.setAttribute('data-tippy-content', (m.value === -1 ? 'Aucune donnée' : "<div style='text-align: center'>Le "+moment(m.created_at).local().toDate().toLocaleDateString()+" à "+moment(m.created_at).local().toDate().toLocaleTimeString()+"<br>"+m.value+" "+(m.suffix||'')+"</div>"));

                svg.appendChild(rect);
                actual_width = actual_width + gap_size + part_size;
            })

            item.innerHTML = "";
            item.appendChild(svg);

            tippy('[data-tippy-content]',{allowHTML: true});
        })
        .catch(error => {
            console.error(error);
            return;
        });
}

let charts = document.querySelectorAll('.service-availability');
charts.forEach((item) => {
    renderAvailabilityChart(item);
})

window.addEventListener('resize', function(event) {
    charts.forEach((item) => {
        renderAvailabilityChart(item);
    });
    tippy('[data-tippy-content]', {allowHTML: true});
}, true);

