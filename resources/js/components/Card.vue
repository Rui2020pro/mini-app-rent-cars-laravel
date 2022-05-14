<template>
    <div class="card mb-2">
        <div class="card-header">{{titulo}}</div>

        <div class="card-body">
            <!-- Atribuimos um nome a slot porque vamos ter mais que 2 slots -->
            <slot name="conteudo"></slot>
        </div>
        <div class="card-footer">
            <slot name="rodape"></slot>
        </div>
    </div>
</template>
<script>
export default {
    props: ['titulo']
}
</script>