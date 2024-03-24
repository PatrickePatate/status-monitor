import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
dayjs.extend(utc);

document.querySelectorAll('[data-local-date]').forEach((item) => {
    item.innerHTML = (dayjs(item.getAttribute('data-local-date')).utc(true)).local().format('DD/MM/YYYY HH:mm:ss');
})
