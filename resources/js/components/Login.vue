<template><!-- bck me() nxt step app.js registar componente -->
    <div class="container"> <!-- bck login.blade.php -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Tudo o que não seja html o vue descarta -->
                <div class="card">
                    <div class="card-header">Login</div>

                    <div class="card-body">
                        <form method="POST" action="" @submit.prevent="login($event)">
                            <input type="hidden" name="_token" :value="token_csrf">
                            <!--
                                Next step - Intercetar o envio do formulário, efetuar uma
                                requisição do token jwt e, depois, de facto efetuar a 
                                submisão do form para estabelecer a autenticação por 
                                sessão - @submit.prevent="login($event)"
                                $event - detalhes do evento
                            -->
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="" required autocomplete="email" autofocus v-model="email">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" v-model="password">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">

                                        <label class="form-check-label" for="remember">
                                            Relembrar-me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>

                                    <a class="btn btn-link" href="">
                                        Esqueceu a sua password?
                                    </a>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- {{valor}} - {{valor2}} - {{valorRandom}} -->
        <!-- {{token_csrf}} - Conseguimos recuperar o token no frontend da aplicação,
        teremos de o colocar no input -->

        <!-- {{email}} / {{password}} -->
    </div>
</template>

<script>
    export default {
        // props:['valor', 'valor2', 'valorRandom'], - Props são análogas ao atributo data
        props: ['token_csrf'],
        data: function (){
            return {
                'email': '', // com isso, podemos efetuar o vmodel em input email e psw
                'password' : ''
            }
        },
        methods: {
            login(e){
                // console.log("chegámos até aqui")
                // fetch () - suportado na maioria dos browsers atualmente
                // fetch() - Por meio dele, podemos efetuar requisiçoes Http
                // fetch(url, configuracao)

                /*console.log(this.email , this.password)
                return false;*/

                let url = "http://localhost:8000/api/login" // nosso endpoint
                // configuracao - objeto literal
                let configuracao = {
                    method: 'post',
                    // if i wanna use form-data para utilizar imagens, UrlSearchParams
                    body: new URLSearchParams({
                        'email': this.email,
                        'password': this.password
                    })
                }
                fetch(url, configuracao)
                    .then(response => response.json())
                    // Recuperámos a msm resposta por meio do Postman
                    .then(response => {
                        // console.log(response)
                        // precisamos de armazenar o token no frontend em cookies
                        // console.log(response.token)
                        if(response.token){
                            // Inserção do token ao frontend da aplicação
                            // Podemos ver em Application
                            document.cookie = 'token=' + response.token
                            // Após a inserção do token ao frontend da aplicação, vamos 
                            // finalmente enviar/submeter o formulário
                            e.target.submit()
                        }

                    })
                    .catch(error => console.log(error))

                    // nxt step componente Home
            }
        },
    }
</script>
