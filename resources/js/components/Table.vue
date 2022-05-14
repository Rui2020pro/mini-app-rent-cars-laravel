<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th v-for="t in titulos" scope="col">
                        {{t.titulo}}
                    </th>
                    <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel"></th>
                </tr>
            </thead>
            <tbody>
                <!-- <tr v-for="obj in dados">
                    {{obj}}
                    <td v-for="valor,chave in obj">
                        <!-- {{chave}} - {{valor}} 
                        <span v-if="chave == 'imagem'">
                            <img :src="'storage/' + valor" alt="" width="30" height="30">
                        </span>
                        <span v-else>
                            {{ valor }}
                        </span>
                    </td>-->              
                    <!--<th scope="row"> {{ m.id }} </th>
                    <td>{{ m.nome }}</td>
                    <td><img :src="'storage/' + m.imagem" alt="" width="30" height="30"></td>-->


                    <!-- Depois disso, falta em table-component passar os títulos que irá ser 
                    um array ['id', 'nome', 'imagem'] -->
                
                <!--
                    Métodos computados são considerados como propriedades.
                -->
                <tr v-for="obj in dadosFiltrados">
                    <td v-for="valor,chave in obj">
                        <!-- {{titulos[chave].tipo}} -->
                        <span v-if="titulos[chave].tipo == 'text'">
                            {{valor}}
                        </span>
                        <span v-if="titulos[chave].tipo == 'image'">
                            <img :src="'storage/' + valor" alt="" width="30" height="30">
                        </span>
                        <span v-if="titulos[chave].tipo == 'date'">
                            {{valor | formataDataTempo}} <!-- // pipes // ir app.js -->
                        </span>
                    </td><!-- nxt carregarLista() - Marca.vue -->
                    <td>
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm" :data-toggle="visualizar.dataToggle" :data-target="visualizar.dataTarget" @click="setStore(obj)">Visualizar</button>
                        <button v-if="atualizar.visivel" class="btn btn-outline-secondary btn-sm" :data-toggle="atualizar.dataToggle" :data-target="atualizar.dataTarget" @click="setStore(obj)">Atualizar</button>
                        <button v-if="remover.visivel" class="btn btn-outline-danger btn-sm" :data-toggle="remover.dataToggle" :data-target="remover.dataTarget" @click="setStore(obj)">Remover</button>
                    </td>
                </tr>
            </tbody>
        </table><!-- Nxt step converter os cards em Marca.vue num component -->
        <!-- {{dados}} -->
    </div>
</template>

<script>
    export default {
        props:["dados", "titulos", "visualizar", "atualizar", "remover"],
        methods: {
            setStore(obj){
                this.$store.state.item = obj
                this.$store.state.transacao.msg = ''
                this.$store.state.transacao.status = ''
                this.$store.state.transacao.dados = ''
                console.log(obj)
            }
        },
        mounted() {
            // console.log('Component mounted.')
        },
        computed: {
            dadosFiltrados(){
                /*
                    Desafio : Obter um array de objetos computados que implemente 
                    atributos iguais a títulos
                */

                // console.log("...")
                // console.log(this.titulos)
                let campos = Object.keys(this.titulos) // isolar os titulos que queremos apresentar
                // console.log(campos)
                // console.log(this.dados) - Para corrigir o erro do map, ao invés de marcas
                // em Marca.vue ser um array teremos de o passar como sendo um obj
                let filtragemDados = []
                // map isola o par chave/valor 
                this.dados.map((valor, chave) =>  {
                    /*
                        console.log(chave, valor)
                        Temos isoladamente cada um dos nossos objetos
                    */

                    let itemFiltrado = {}

                    /*
                        Percorrer cada um dos índices de campos (id, titulo, imagem)
                        O desafio é recuperar apenas os atributos de cada objeto com 
                        base nos campos encaminhados nos títulos.

                    */
                    campos.forEach(campo => {
                        // Ex : itemFiltrado[imagem] = valor[imagem]
                        // console.log(chave, item, valor) - O mesmo objeto está sendo 
                        // repetido 3x em função dos atributos que queremos recuperar
                        // Na 1ª iteração, queremos recuperar o id, na 2ª o nome ...
                        itemFiltrado[campo] = valor[campo] // utilizar a sintaxe do array para atribuir valores a objetos 
                        
                    })
                    // console.log(itemFiltrado)
                    filtragemDados.push(itemFiltrado)
                    // console.log(filtragemDados)
                })
                return filtragemDados // retornar um array de objetos
            }
        }
    }
</script>
