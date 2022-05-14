<template>
    <div :class='estilo' role="alert">
        {{titulo}}
        <hr>
        <!--<span v-if="mensagem.data.id">
            {{mensagem.data.id}}
        </span>

        <ul v-if="mensagem.data.errors">
            <li v-for="(e, item) in mensagem.data.errors">
                {{ e[0] }}
            </li>
        </ul>-->

        <p>{{ mensagem.msg }}</p>
        <ul v-if="mensagem.dados">
            <li v-for="(e, item) in mensagem.dados">
                {{ e[0] }}
            </li>
        </ul>


    </div><!-- nxt modal.vue -->
</template>
<script>
export default {
    props: ['tipo', 'mensagem', 'titulo'],
    computed: {
        estilo(){
            return "alert alert-"+ this.tipo // nxt Marca.vue axios post em guardar()
        }
    }
}
</script>