<script>
export default {
    name: "FormError",
    props: {
        error: null,
    },
}
</script>

<template>
    <small class="text-danger font-weight-bold"  v-if="error">{{error}}</small>
</template>

<style scoped>

</style>
