<template>
    <Layout>
        <Head><title>Eduket | Settings</title></Head>
        <page-header current-page="Manage Settings" page="Settings"/>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="row">
                <Link :href="$route('settings.show',data.page)" :key="data.color" v-for="data in info" class="col-lg-3 col-md-4 col-sm-12 mb-30">
                    <div class="card-box pd-30 height-100-p" :class="data.color">
                        <div class="progress-box text-center">
                            <span :class="data.icon" class="dw fa-5x text-white"></span>
                            <h4  class="padding-top-10 h3 text-white ">{{data.title}}</h4>
                        </div>
                    </div>
                </Link>
            </div>

        </div>
    </Layout>
</template>

<script>
export default {
    name: "Index",
    data() {
        return {
            info: [
                {
                    icon: 'dw-books',
                    title:'Subjects',
                    color:'bg-info',
                    page:'subjects'
                },
                {
                    icon: 'dw-table',
                    title:'Classes',
                    color:'bg-success',
                    page:'classes'
                },
                {
                    icon: 'dw-balance',
                    title:'Grading',
                    color:'bg-danger',
                    page:'grading',
                },
                {
                    icon: 'dw-calendar-3',
                    title:'Calendar',
                    color:'bg-dark',
                    page:'calendar',
                },
                {
                    icon: 'dw-user-13',
                    title:'Staff',
                    color:'bg-light-orange',
                    page:'staff'
                },
                {
                    icon: 'dw-user-11',
                    title:'Parents',
                    color:'bg-primary',
                    page:'parents'
                },
                {
                    icon: 'dw-add-user',
                    title:'Students',
                    color:'bg-secondary',
                    page:'students'
                },
                {
                    icon: 'dw-coding',
                    title:'Codes',
                    color:'bg-success',
                    page:'codes'
                },
                {
                    icon: 'dw-calendar-6',
                    title:'Promotion',
                    color:'bg-danger',
                    page:'promotion'
                },
            ]
        }
    },
}
</script>


<style scoped>
.bg-light-orange {
    background-color: #f56767;
}
</style>

