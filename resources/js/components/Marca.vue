<template>
    <!-- Nxt - Registar component app.js e criar e registar um componente que encapsule o 
    conteúdo que é impresso quando estamos a trabalhar com inputs. Basicamente, o 
    componente vai receber como parâmetros, o label , o input e o texto de ajuda e, 
    esse componente vai ser responsável por formar o conteúdo html necessário para 
    impressão em tela. Forma de criarmos componentes que podem ser reutilizados. - 
    InputContainer -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo="Procurar Marcas">
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component id="inputId" titulo="ID" id-help="idHelp" texto-ajuda="Informe o ID do registo">
                                <!-- Nós só não vamos passar o input para o componente 
                                InputContainer porque vamos precisar de o manipular no 
                                contexto do componente Marca -->
                                    <input type="number" class="form-control" id="inputID" aria-describedby="idHelp" placeholder="ID" v-model="busca.id">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component id="inputNome" titulo="Nome" id-help="nomeHelp" texto-ajuda="Informe o Nome da Marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome" v-model="busca.nome">
                                </input-container-component>
                                <!-- Só não está a imprimir o input porque em 
                                InputContainer falta pegar o input (conteúdo interno)
                                através do slot -->
                            </div>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisarMarcas()">Pesquisar</button>
                    </template>
                </card-component>

                <!-- Card de Listagem de Marcas -->

                <!-- Nxt - Registar componente Table em app.js e criar e registar 
                um componente que encapsule o conteúdo que é impresso quando estamos a 
                trabalhar com tables. Basicamente, vamos transformar a nossa table num
                componente vue. -->

                <card-component titulo="Listagem Marcas">
                    <template v-slot:conteudo>
                        <table-component 
                            :visualizar="{visivel: true, dataToggle: 'modal', dataTarget:'#modalVisualizarMarca'}"
                            :atualizar="{visivel: true, dataToggle: 'modal', dataTarget:'#modalAtualizarMarca'}"
                            :remover="{visivel: true, dataToggle: 'modal', dataTarget:'#modalRemoverMarca'}"
                            :dados="marcas.data" 
                            :titulos="{
                                id: {titulo: 'Id', tipo:'text'},
                                nome:{titulo: 'Nome', tipo:'text'},
                                imagem:{titulo: 'Imagem', tipo:'image'},
                                created_at:{titulo: 'Data de Criação', tipo:'date'}
                            }"  
                            ></table-component>
                    </template>
                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col">
                                <pagination-component>
                                    <li v-for="m,chave in marcas.links" :key="chave" 
                                        :class="m.active ? 'page-item active'  :'page-item' " 
                                        @click="paginacao(m)">
                                        <a class="page-link" v-html="m.label" style="padding:7px; cursor:pointer"><!--{{m.label}}--></a>
                                    </li>
                                </pagination-component>
                            </div>
                            <div class="col">
                                <button type="submit" 
                                class="btn btn-primary btn-sm float-right" 
                                data-toggle="modal" 
                                data-target="#modalMarca"> Adicionar</button>
                            </div>
                        </div>                   
                    </template>
                </card-component> <!-- nxt step adicionar breadcrumb à navegação -->

                <!-- 
                        Nxt step converter o modal em um componente e vamos acionar esse 
                    modal quando o utilizador clicar em adicionar
                -->
                <!-- Modal -->
                <modal-component id="modalMarca" titulo="Adicionar Marca">
                    <template v-slot:alertas>
                        <alert-component tipo="success"  v-if="estado == 'sucesso' " :mensagem="msgSucErr" titulo="Sucesso no registo"></alert-component>
                        <alert-component tipo="danger" :mensagem="msgSucErr" titulo="Erro ao tentar registar a marca" v-if="estado == 'erro'"></alert-component>
                    </template>
                    <template v-slot:conteudo>
                        <div class="form-group">
                            <input-container-component id="inputNomeMarca" titulo="Nome da Marca" id-help="nomeHelpMarca" texto-ajuda="Informe o Nome da Marca">
                                <input type="text" class="form-control" id="inputNomeMarca" aria-describedby="nomeHelpMarca" placeholder="Nome" v-model="nomeMarca">
                            </input-container-component>
                            {{nomeMarca}}
                        </div>
                        <div class="form-group">
                            <input-container-component id="inputImagem" titulo="Imagem" id-help="imagemHelp" texto-ajuda="Selecione uma Imagem do tipo PNG">
                                <input type="file" class="form-control-file" id="inputImagem" aria-describedby="imagemHelp" placeholder="Selecione uma Imagem (PNG)" @change="carregarImagem($event)">
                            </input-container-component>
                            {{arquivoImagem}}
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary" @click="guardar">Guardar</button>
                    </template>
                </modal-component>
                <!--
                    Dentro do modal nós temos 2 inputs, do tipo text e file e, nós vamos
                criar 2 atributos na instância do vue que vão receber esses valores.
                    Para os receber, teremos de utilizar a diretiva v-model
                    No entanto, em arquivos do tipo file não é permitido utilizar o
                v-model.
                -->

                <!-- Modal Visualizar Marca -->
                <modal-component id="modalVisualizarMarca" titulo="Visualizar Marca">
                    <template v-slot:conteudo>
                        <!-- {{$store.state.item}} -->
                        <div class="form-group">
                            <input-container-component id="inputVisualizarMarca" titulo="Nome da Marca" id-help="visnomeHelpMarca">
                                <input type="text" class="form-control" id="inputVisualizarMarca" aria-describedby="visnomeHelpMarca" :value="$store.state.item.nome" disabled>
                            </input-container-component>                         
                        </div>
                        <div class="form-group">
                            <input-container-component id="inputVisualizarImagem" titulo="Imagem da Marca" id-help="visimagemHelp">
                               <img v-if="$store.state.item.nome" :src="'storage/' + $store.state.item.imagem" alt="">
                            </input-container-component>                           
                        </div>
                    </template>
                </modal-component>

                <!-- Modal Remover Marca Marca -->
                <modal-component id="modalRemoverMarca" titulo="Remover Marca">
                    <template v-slot:alertas>
                        <alert-component tipo="success" titulo="Removido com Sucesso" :mensagem=" $store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" titulo="Erro ao remover" :mensagem="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
                    </template>
                    <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                        <!-- {{$store.state.item}} -->
                        <div class="form-group">
                            <input-container-component titulo="ID da Marca">
                                <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                            </input-container-component>                         
                        </div>
                        <div class="form-group">
                            <input-container-component titulo="Nome da Marca">
                                <input type="text" class="form-control" id="inputVisualizarMarca" :value="$store.state.item.nome" disabled>
                            </input-container-component>                         
                        </div>
                    </template>
                    <template v-slot:rodape v-if="$store.state.transacao.status != 'sucesso'">
                        <button type="button" class="btn btn-danger" @click="removerMarca">Remover</button>
                    </template>
                </modal-component>

                <!-- Modal Atualizar Marca Marca -->
                <modal-component id="modalAtualizarMarca" titulo="Atualizar Marca">
                    <template v-slot:alertas>
                        <alert-component tipo="success" titulo="Atualizado com Sucesso" :mensagem=" $store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                        <alert-component tipo="danger" titulo="Erro ao atualizar" :mensagem="$store.state.transacao" v-if="$store.state.transacao.status == 'erro'"></alert-component>
                    </template>
                    <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                        <!-- {{$store.state.item}} -->
                        <div class="form-group">
                            <input-container-component titulo="Nome da Marca">
                                <input type="text" class="form-control" id="inputAtualizarMarca" v-model="$store.state.item.nome">
                            </input-container-component>                         
                        </div>
                        <div class="form-group">
                            <input-container-component id="inputAtualizarImagem" titulo="Imagem da Marca" id-help="visimagemHelp">
                                <input type="file" class="form-control-file" id="inputAtualizarImagem" aria-describedby="imagemHelp" placeholder="Selecione uma Imagem (PNG)" @change="carregarImagem($event)">
                                <img v-if="$store.state.item.nome" :src="'storage/' + $store.state.item.imagem" alt="">
                            </input-container-component>                           
                        </div>
                    </template>
                    <template v-slot:rodape v-if="$store.state.transacao.status != 'sucesso'">
                        <button type="button" class="btn btn-primary" @click="atualizarMarca">Atualizar</button>
                    </template>
                </modal-component>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        computed: {
            token(){
                // return 'Teste'
                // // token definido em Login.vue
                // Vamos efetuar uma nova sessão e podemos ver que os parâmetros do token 
                // são separados por ;
                // console.log(token)
                let token = document.cookie.split(';').find(indice => {
                    // console.log(indice, indice.startsWith('token='))
                    return indice.startsWith('token=')
                })

                token = token.split('=')[1]
                token = 'Bearer '+token
                // console.log(token)
                return token;
            }
        },
        data(){
            return {
                urlBase: 'http://localhost:8000/api/v1/marcas',
                urlPaginada: '',
                urlFiltrada: '',
                nomeMarca: '',
                arquivoImagem: [], // inputs do tipo file são um array de objetos
                estado: '', //estadoGuardar
                msgSucErr: {}, // mensagem de sucesso ou erro
                marcas: { data:[] },// listar Marcas,
                busca: { id: '', nome: ''} // Pesquisar Marcas
            }
        },
        methods: {
            atualizarMarca(){
                /*console.log("Nome Atualizado : " + this.$store.state.item.nome)
                console.log(this.arquivoImagem)
                console.log("verbo http : " + "put")*/

                let configuracoes = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        /*'Accept': 'application/json',
                        'Authorization': this.token,*/
                    }
                }

                let formData = new FormData();
                formData.append('_method' , 'put')
                formData.append('nome' , this.$store.state.item.nome)

                if(this.arquivoImagem[0]){
                    formData.append('imagem' , this.arquivoImagem[0])
                }

                let url = this.urlBase + '/' + this.$store.state.item.id

                axios.post(url, formData, configuracoes)
                    .then(response => {
                        console.log(response)
                        this.carregarLista()
                        inputAtualizarImagem.value = ''
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.msg = 'Atualização feita com sucesso'
                    }).catch(errors => {
                        console.log("Erro na Atualização : " + errors.response.data.message)
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.msg = 'Erro na Atualização! Confirme todos os campos!'
                        this.$store.state.transacao.msg = errors.response.data.message
                        this.$store.state.transacao.dados = errors.response.data.errors
                    })
            },
            removerMarca(){
                let confirmacao = confirm("Tem a certeza que deseja remover esse registo?")
                if(!confirmacao){
                    return false
                }

                let configuracoes = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        /*'Accept': 'application/json',
                        'Authorization': this.token,*/
                    }
                }

                let url = this.urlBase + '/'+this.$store.state.item.id
                
                let formData = new FormData()
                formData.append('_method', 'delete')
                formData.append('nome', this.$store.state.item.nome)

                // console.log(this.$store.state.transacao)

                axios.post(url, formData, configuracoes)
                    .then(response => {
                        // console.log('Registo removido com sucesso : ' + response.data)
                        this.$store.state.transacao.status = 'sucesso'
                        this.$store.state.transacao.msg = 'Registo removido com sucesso'
                        this.carregarLista()
                    }).catch(errors => {
                        // console.log('Erro ao efetuar a remoção do registo : ' + errors.response.data.message + "," + errors.response.status)
                        this.$store.state.transacao.status = 'erro'
                        this.$store.state.transacao.msg = 'Erro ao efetuar a remoção do registo'
                    })
            },
            pesquisarMarcas(){
                console.log(this.busca)

                let filtro = ''

                for (let chave in this.busca) {
                    console.log(chave, this.busca[chave])
                    // neste caso é uma comparação por igualdade
                    if(this.busca[chave] != ''){
                    // Na 1ª iteração vai estar vazio no entanto na segunda n e aí vamos
                    // concatenar com o ;
                        if(filtro != ''){
                            filtro += ';'
                        }
                        filtro += chave + ':like:' + this.busca[chave]
                    }
                }
                // console.log(filtro)
                if(filtro != ''){
                    this.urlPaginada = 'page=1'
                    this.urlFiltrada = '&filtro=' +filtro
                }else {
                    this.urlFiltrada = ''
                }
                this.carregarLista()
                
            },
            paginacao(m){
                if(m.url){
                    // this.urlBase = m.url
                    this.urlPaginada = m.url.split('?')[1]
                    this.carregarLista()
                }   
            },

            carregarLista(){

                let url = this.urlBase + '?' + this.urlPaginada + this.urlFiltrada
                console.log(url)

                /*let configuracoes = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token,
                    }
                }-bootstrap.js */

                // axios.get(url, configuracoes)
                axios.get(url)
                    .then(response => {
                        // console.log(response.data)
                        this.marcas = response.data
                        // this.marcas = response.data.data // next table-component
                        // console.log(this.marcas)
                    })
                    .catch(errors => console.log(errors))
            },
            carregarImagem(e){
                this.arquivoImagem = e.target.files
            },
            /*
                Método responsável por tomar os valores de nomeMarca e arquivoImagem
                e disparar as requisições via Http.
            */
            guardar(){
                // console.log(this.nomeMarca, this.arquivoImagem[0])
                /*
                        Agora, precisamos de montar uma requisição para o backend da 
                    aplicação e nós vamos fazer isso através da biblioteca chamada axios.
                */
                // axios.post(urlBase, conteudo, configuracoes)

                // Em postman, utilizamos o form-data para anexar imagens

                let formData = new FormData() // já tá um form semelhante ao do postman
                formData.append('nome', this.nomeMarca)
                formData.append('imagem', this.arquivoImagem[0])
                /*
                    Às configurações, vamos adicionar o token de autorização como sendo
                    um cabeçalho da nossa requisição. O cabeçalho vai ser o Authorization.
                    Mas, primeiro, precisamos de recuperar o token.
                */

                let configuracoes = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        // 'Accept': 'application/json',
                        /*
                            Se removermos o token e limparmos o conteúdo ao clicar na 
                            bola, temos o erro de 401. No entanto, se utilizarmos 
                            Bearer + o token que nos foi fornecido anteriormente, podemos
                            ver que já conseguimos guardar o registo, independentemente
                            se o token está nos cookies ou não.
                        */
                        // 'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTYzMzcwMjEyMSwiZXhwIjoxNjMzNzA1NzIxLCJuYmYiOjE2MzM3MDIxMjEsImp0aSI6Ik1KTVo0ME5BVmJ0MGxRMkwiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.ZSdvo3jLFbVpgHEsedtJ2qLgW_s3CwSdhmncQeB8Cc8'
                        // 'Authorization': this.token, - bootstrap.js
                    }
                }
                // Ver a response em marcas -> Network -> Response
                axios.post(this.urlBase, formData, configuracoes)
                    // criar um atributo designado estado que vai guardar o estado da 
                    // requisição e em função do estado, vamos imprimir os alertas
                    .then(response => {
                        // console.log(response)
                        this.estado = 'sucesso' // nxt alert-component
                        // this.msgSucErr = response
                        this.msgSucErr = {
                            msg: response.data.id,
                        }

                    })
                    .catch(errors => {
                        // console.log(errors.response.data.message)
                        this.estado = 'erro'
                        this.msgSucErr = {
                            msg: errors.response.data.message,
                            dados:errors.response.data.errors
                        }
                        // this.msgSucErr = errors.response
                    }) // next alert component
            }
        },
        mounted() {
            this.carregarLista()
        },
    }
</script>