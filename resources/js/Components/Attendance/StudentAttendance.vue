<template>
    <Layout>
        <Head><title>Eduket | View Attendance</title></Head>
        <page-header :current-page="user.name" page="Attendance"/>
        <div class="row">
            <div class="card-box pd-20 mb-30 col-md-4">
                <div class="profile-photo">
                    <img :src="'img/'+user.avatar" alt="" class="avatar-photo">
                </div>
                <h5 class="text-center h5 mb-0">{{user.name}}</h5>
                <div class="profile-info">
                    <ul>
                        <li>
                            <span>Gender:</span>
                            {{user.gender}}
                        </li>
                        <li>
                            <span>Class:</span>
                            {{user.form[0].number+user.form[0].name}}
                        </li>
                    </ul>
                    <h5 class="h5 mt-15">Attendance Summary</h5>
                    <p>{{days-total}} attended</p>
                    <div style="height: 25px;" class="progress mb-30">
                        <div  :style="{width: ((days-total)/days*100)+'%'}" class="progress-bar bg-success">{{((days-total)/days*100).toFixed(0)+'%'}}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mb-30">
                <div class="card-box pd-20">
                    <h2 class="h4 mb-20">Absenteeism Summary</h2>
                    <column-chart :data="[{name:'Reasons',data:malesAtt()}]"></column-chart>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    name: "StudentAttendance",
    props: {
        attendances:[],
        user:{},
        days:0,
        total:0
    },
    data() {
        return {
            reasons: [
                'Sick',
                'Abscond',
                'Religion',
                'Medical',
                'Suspended'
            ]
        }
    },
    methods: {
        malesAtt() {
            let values = []

            this.reasons.forEach((day)=> {

                this.attendances.forEach((data)=> {
                    if (day === data.reason) values.push([day, data.total])
                    if (day !== data.reason) values.push([day, 0])
                })
            })

            return values
        },
    },
}
</script>

<style scoped>

</style>
