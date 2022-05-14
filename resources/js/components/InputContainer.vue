<template>
    <!-- bck Marca.vue nxt Marca.vue -->
    <div class="form-group">
        <!-- id e título para label -->
        <!-- <label for="inputID" class="form-label">ID</label> - Old Label -->
        <!-- <div id="idHelp" class="form-text text-muted">Informe o ID</div> - Old Div -->
        <label :for="id" class="form-label">{{titulo}}</label>
        <slot></slot>
        <div :id="idHelp" class="form-text text-muted">{{textoAjuda}}</div>
    </div>
</template>

<script>
    export default {
        props: ['id', 'titulo', 'idHelp' , 'textoAjuda'],
    }
</script>
