<template>
    <Layout>
        <Head><title>Eduket | Analysis</title></Head>
        <page-header current-page="Report Analysis" page="Analysis"/>

            <div class="row clearfix mb-30">
                <StudentPerformance/>
            </div>
        <br>
    </Layout>
</template>

<script>
import StudentPerformance from "./StudentPerformance";
export default {
    name: "Analysis",
    components: {StudentPerformance},
}
</script>

<style scoped>

</style>
