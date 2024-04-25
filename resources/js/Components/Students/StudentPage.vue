<template>
    <Layout>
        <Head><title>Eduket | Student Profile</title></Head>
        <page-header current-page="Student Profile" page="Student"/>
        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                <div class="pd-20 card-box height-100-p">
                    <div class="profile-photo">
                        <img :src="'/img/'+(user.avatar?user.avatar:'boy.png')" alt="" class="avatar-photo">
                    </div>
                    <h5 class="text-center h5 mb-0">{{user.name}}</h5>
                    <p class="text-center text-muted font-14">Form {{user.form[0].number}}{{user.form[0].name}}</p>
                    <div class="profile-info">
                        <h5 class="mb-20 h5 text-blue">Basic Information</h5>
                        <ul>
                            <li>
                                <span>Gender:</span>
                                {{user.gender}}
                            </li>
                            <li>
                                <span>Class:</span>
                                Form {{user.form[0].number}}{{user.form[0].name}}
                            </li>
                            <li>
                                <span>Guardian:</span>
                                <span class="text-dark"  v-if="user.guardian.length > 0">{{user.guardian[0].fname}}{{user.guardian[0].lanme}}</span>
                                <span class="text-dark"  v-else>Not Assigned</span>
                            </li>
                            <li>
                                <span>Sponsorship:</span>
                                <span class="text-dark" v-if="user.bursary.length > 0">{{user.bursary[0].name}}</span>
                                <span class="text-dark" v-else>Self Sponsored</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                <div class="row">
                    <Link :href="$route(data.page,user.id)" :key="data.color" v-for="data in info" class="col-lg-4 col-md-6 col-sm-12 mb-30">
                        <div class="card-box pd-30 height-100-p" :class="data.color">
                            <div class="progress-box text-center">
                                <span :class="data.icon" class="dw fa-5x text-white"></span>
                                <h4  class="padding-top-10 h3 text-white ">{{data.title}}</h4>
                            </div>
                        </div>
                    </Link>
                </div>

            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    name: "StudentPage",

    props:{
        user:{}
    },

    data() {
        return {
            info: [
                {
                    icon: 'dw-user',
                    title:'Profile',
                    color:'bg-info',
                    page:'profile.show',
                },
                {
                    icon: 'dw-analytics-4',
                    title:'Performance',
                    color:'bg-success',
                    page:'performance.show',
                },
                {
                    icon: 'dw-calendar-3',
                    title:'Attendance',
                    color:'bg-danger',
                    page:'attendance.show',
                },
                {
                    icon: 'dw-table',
                    title:'Grade Book',
                    color:'bg-dark',
                    page:'grade-book.show',
                },
                {
                    icon: 'dw-file2',
                    title:'School Reports',
                    color:'bg-light-orange',
                    page:'reports.show',
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
