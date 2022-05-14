const { default: axios } = require('axios');

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

axios.interceptors.request.use(
    config => {
        /* ---- 'Accept': 'application/json',
        'Authorization': this.token, ---- */
        config.headers['Accept']= 'application/json'

        let token = document.cookie.split(';').find(indice => {
            return indice.includes('token=')
        })

        token = token.split('=')[1]
        token = 'Bearer ' + token

        config.headers.Authorization = token
        console.log('Intercetando o request antes do envio', config)
        return config // nxt jwt.php
    },
    error => {
        console.log("Erro na requisição: ", error)
        return Promise.reject(error)
    })

axios.interceptors.response.use(
    response => {
        console.log('Intercetando a resposta antes do envio', response)
        return response
    },
    error => {
        console.log("Erro na resposta: ", error.response)

        if( error.response.status == 401 && error.response.data.message == 'Token has expired')
        {
            console.log('Fazer uma nova requisição para a rota refresh!')

            axios.post('http://localhost:8000/api/refresh')
                .then(response => {
                    console.log('Refresh com sucesso! ')
                    console.log(response)
                    /*
                        In the first refresh, will be sucess but if I don't save the 
                        token in cookies, in the second refresh I will have the 500 
                        error : 'The token has been blacklisted'
                    */
                   document.cookie = 'token=' + response.data.token
                   console.log('Token Atualizado : ' , response.data)
                   window.location.reload()          
                })
        }
        return Promise.reject(error)
    })
    
