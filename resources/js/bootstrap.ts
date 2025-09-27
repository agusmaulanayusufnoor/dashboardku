import axios from 'axios';

// Pastikan axios mengirim cookie (penting untuk login session SPA)
axios.defaults.withCredentials = true;
axios.defaults.baseURL = location.origin;
// Set header X-Requested-With untuk request AJAX
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Ambil token CSRF dari <meta name="csrf-token">
const tokenMeta = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');

if (tokenMeta) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
} else {
    console.error(
        'CSRF token not found! Pastikan <meta name="csrf-token" content="{{ csrf_token() }}"> ada di app.blade.php'
    );
}

// Optional: log setiap request/response untuk debugging
axios.interceptors.request.use((config) => {
    console.log('➡️ Axios request:', config.method?.toUpperCase(), config.url);
    return config;
});

axios.interceptors.response.use(
    (response) => {
        console.log('⬅️ Axios response: Berhasil ', response.status, response.config.url);
        return response;
    },
    (error) => {
        console.error('❌ Axios error:', error.response?.status, error.response?.data);
        return Promise.reject(error);
    }
);

export default axios;
